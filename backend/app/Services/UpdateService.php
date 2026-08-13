<?php

namespace App\Services;

use App\VersionHelper;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use RuntimeException;
use Throwable;
use ZipArchive;

class UpdateService
{
    private array $steps = [];

    public function currentVersion(): string
    {
        return (string) VersionHelper::get();
    }

    public function status(): array
    {
        return [
            'version' => $this->currentVersion(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'next_version' => $this->packageVersion(config('updater.next_path')),
            'last_checked_at' => cache()->get('system_update_last_checked_at'),
            'requirements' => [
                'zip' => class_exists(ZipArchive::class),
                'backend_writable' => is_writable(base_path()),
                'next_writable' => is_writable((string) config('updater.next_path')),
            ],
        ];
    }

    public function checkUpdate(): ?array
    {
        try {
            $response = Http::acceptJson()->timeout(15)->get((string) config('updater.server_url'), [
                'version' => $this->currentVersion(),
                'domain' => request()->getHost(),
                'bundle' => 'laravel-nextjs',
            ]);
            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            if (($data['status'] ?? null) !== 'success' || empty($data['version'])) {
                return null;
            }

            cache()->forever('system_update_last_checked_at', now()->toIso8601String());
            return $data;
        } catch (Throwable $error) {
            report($error);
            return null;
        }
    }

    public function update(string $downloadUrl, string $newVersion, string $sha256): array
    {
        $workDir = storage_path('app/system-updates/' . preg_replace('/[^a-zA-Z0-9._-]/', '-', $newVersion));
        $archive = $workDir . DIRECTORY_SEPARATOR . 'release.zip';
        $staging = $workDir . DIRECTORY_SEPARATOR . 'staging';
        $backup = storage_path('app/system-update-backups/' . now()->format('Ymd_His') . '_' . $newVersion);
        $maintenance = false;
        $migrationCount = 0;

        try {
            $this->step('Đang tải gói cập nhật PHP + Next.js');
            File::deleteDirectory($workDir);
            File::ensureDirectoryExists($staging);
            $response = Http::withOptions(['sink' => $archive])->timeout(600)->get($downloadUrl);
            throw_unless($response->successful(), new RuntimeException('Tải gói cập nhật thất bại.'));
            throw_unless(hash_equals($sha256, hash_file('sha256', $archive)), new RuntimeException('Checksum SHA-256 không hợp lệ.'));

            $this->step('Đang kiểm tra cấu trúc gói cập nhật');
            $this->extractSafely($archive, $staging);
            $manifest = $this->readManifest($staging);
            throw_unless((string) $manifest['version'] === $newVersion, new RuntimeException('Phiên bản trong manifest không khớp.'));
            throw_unless(is_dir($staging . '/backend') && is_dir($staging . '/nextjs'), new RuntimeException('Gói cập nhật phải có cả backend/ và nextjs/.'));

            $this->step('Đang sao lưu phiên bản hiện tại');
            File::ensureDirectoryExists($backup);
            $this->backupTargets($staging . '/backend', base_path(), $backup . '/backend', config('updater.exclude', []));
            $this->backupTargets($staging . '/nextjs', config('updater.next_path'), $backup . '/nextjs', config('updater.next_exclude', []));

            Artisan::call('down', ['--retry' => 15]);
            $maintenance = true;

            $this->step('Đang cập nhật mã nguồn Laravel');
            $this->copyTree($staging . '/backend', base_path(), config('updater.exclude', []));
            $this->run([config('updater.composer_binary'), 'install', '--no-dev', '--prefer-dist', '--optimize-autoloader', '--no-interaction'], base_path(), 600);

            $this->step('Đang cập nhật và build Next.js');
            $nextPath = (string) config('updater.next_path');
            $this->copyTree($staging . '/nextjs', $nextPath, config('updater.next_exclude', []));
            $this->run([config('updater.npm_binary'), 'ci'], $nextPath, 900);
            $this->run([config('updater.npm_binary'), 'run', 'build'], $nextPath, 1200);

            $this->step('Đang cập nhật cơ sở dữ liệu và cache');
            $migrator = app('migrator');
            $migrationFiles = $migrator->getMigrationFiles(database_path('migrations'));
            $migrationCount = count(array_diff(array_keys($migrationFiles), $migrator->getRepository()->getRan()));
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('optimize:clear');
            $this->saveVersion($newVersion);
            $this->restartNext();
            if (function_exists('opcache_reset')) {
                @opcache_reset();
            }

            Artisan::call('up');
            $maintenance = false;
            File::deleteDirectory($workDir);
            $this->pruneBackups();

            return ['status' => 'success', 'message' => "Đã cập nhật đồng bộ PHP và Next.js lên {$newVersion}.", 'steps' => $this->steps, 'migrations' => $migrationCount];
        } catch (Throwable $error) {
            report($error);
            $this->step('Có lỗi, đang khôi phục mã nguồn đã sao lưu', 'error');
            if (is_dir($backup . '/backend')) {
                $this->copyTree($backup . '/backend', base_path());
            }
            if (is_dir($backup . '/nextjs')) {
                $this->copyTree($backup . '/nextjs', (string) config('updater.next_path'));
            }
            if ($maintenance) {
                Artisan::call('up');
            }
            return ['status' => 'error', 'message' => $error->getMessage(), 'steps' => $this->steps, 'migrations' => $migrationCount];
        }
    }

    private function readManifest(string $staging): array
    {
        $path = $staging . '/update.json';
        throw_unless(File::exists($path), new RuntimeException('Thiếu file update.json.'));
        $manifest = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        throw_unless(($manifest['format'] ?? null) === 1 && !empty($manifest['version']), new RuntimeException('Manifest cập nhật không hợp lệ.'));
        return $manifest;
    }

    private function extractSafely(string $archive, string $destination): void
    {
        $zip = new ZipArchive();
        throw_unless($zip->open($archive) === true, new RuntimeException('Không mở được file ZIP.'));
        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = str_replace('\\', '/', (string) $zip->getNameIndex($index));
            if ($name === '' || str_starts_with($name, '/') || preg_match('#(^|/)\.\.(/|$)#', $name)) {
                $zip->close();
                throw new RuntimeException('Gói ZIP chứa đường dẫn không an toàn.');
            }
        }
        throw_unless($zip->extractTo($destination), new RuntimeException('Không thể giải nén bản cập nhật.'));
        $zip->close();
    }

    private function backupTargets(string $source, string $target, string $backup, array $excludes): void
    {
        foreach ($this->files($source) as [$relative]) {
            if ($this->excluded($relative, $excludes)) continue;
            $current = $target . DIRECTORY_SEPARATOR . $relative;
            if (is_file($current)) {
                File::ensureDirectoryExists(dirname($backup . DIRECTORY_SEPARATOR . $relative));
                File::copy($current, $backup . DIRECTORY_SEPARATOR . $relative);
            }
        }
    }

    private function copyTree(string $source, string $target, array $excludes = []): void
    {
        foreach ($this->files($source) as [$relative, $absolute]) {
            if ($this->excluded($relative, $excludes)) continue;
            $destination = $target . DIRECTORY_SEPARATOR . $relative;
            File::ensureDirectoryExists(dirname($destination));
            File::copy($absolute, $destination);
        }
    }

    private function files(string $root): array
    {
        return array_map(fn ($file) => [
            str_replace('\\', '/', $file->getRelativePathname()),
            $file->getPathname(),
        ], iterator_to_array(File::allFiles($root)));
    }

    private function excluded(string $relative, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (fnmatch($pattern, $relative, FNM_PATHNAME) || fnmatch(rtrim($pattern, '/*'), $relative)) return true;
        }
        return false;
    }

    private function run(array $command, string $path, int $timeout): void
    {
        $result = Process::path($path)->timeout($timeout)->run($command);
        throw_unless($result->successful(), new RuntimeException(trim($result->errorOutput() ?: $result->output()) ?: 'Lệnh triển khai thất bại.'));
    }

    private function restartNext(): void
    {
        $command = trim((string) config('updater.next_restart_command'));
        if ($command !== '') {
            $result = Process::path((string) config('updater.next_path'))->timeout(60)->run($command);
            throw_unless($result->successful(), new RuntimeException('Không thể khởi động lại Next.js: ' . trim($result->errorOutput())));
        }
    }

    private function packageVersion(string $path): ?string
    {
        $file = rtrim($path, '/\\') . DIRECTORY_SEPARATOR . 'package.json';
        if (!is_file($file)) return null;
        return json_decode((string) file_get_contents($file), true)['dependencies']['next'] ?? null;
    }

    private function saveVersion(string $version): void
    {
        foreach ([config_path('updater.php'), config_path('app.php')] as $path) {
            $content = File::get($path);
            File::put($path, preg_replace("/'version'\s*=>\s*'[^']*'/", "'version' => '{$version}'", $content, 1));
        }
    }

    private function pruneBackups(): void
    {
        $folders = collect(File::directories(storage_path('app/system-update-backups')))->sortDesc()->values();
        $folders->slice(max(1, (int) config('updater.keep_backups')))->each(fn ($folder) => File::deleteDirectory($folder));
    }

    private function step(string $message, string $status = 'success'): void
    {
        $this->steps[] = ['message' => $message, 'status' => $status, 'time' => now()->toIso8601String()];
    }
}
