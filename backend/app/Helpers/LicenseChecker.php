<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class LicenseChecker
{
    protected $server;
    protected $key;
    protected $version;

    public function __construct()
    {
        $this->server = config('app.license_server');
        $this->key = config('app.license_key');
        $this->version = config('app.version');
    }

    // Kiểm tra key
    public function checkKey(): bool
    {
        $response = Http::get($this->server.'/check-key.php', [
            'domain' => request()->getHost(),
            'license_key' => $this->key
        ]);

        if ($response->ok()) {
            $data = $response->json();
            return $data['valid'] ?? false;
        }

        return false;
    }

    // Lấy thông tin bản update
    public function getUpdate()
    {
        $response = Http::get($this->server.'/check-update.php', [
            'domain' => request()->getHost(),
            'license_key' => $this->key
        ]);

        if ($response->ok()) {
            $data = $response->json();
            if (!isset($data['error']) && version_compare($data['version'], $this->version, '>')) {
                return $data;
            }
        }
        return null;
    }

    // Download và giải nén update
    public function downloadUpdate($filePath)
    {
        $response = Http::get($this->server.'/download-update.php', [
            'domain' => request()->getHost(),
            'license_key' => $this->key,
            'version' => basename($filePath)
        ]);

        if ($response->ok()) {
            $tmpFile = storage_path('update_'.basename($filePath));
            file_put_contents($tmpFile, $response->body());

            $zip = new \ZipArchive();
            if ($zip->open($tmpFile) === true) {
                $zip->extractTo(base_path());
                $zip->close();
            }
            unlink($tmpFile);

            return true;
        }

        return false;
    }
}