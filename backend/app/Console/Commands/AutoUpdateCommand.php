<?php

namespace App\Console\Commands;

use App\Models\Config;
use App\VersionHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class AutoUpdateCommand extends Command
{
    protected $signature = 'system:auto-update';
    protected $description = 'Automatically check and apply system updates if enabled';

    public function handle()
    {
        $domain = env('APP_URL', 'localhost');
        $config = Config::where('domain', parse_url($domain, PHP_URL_HOST))->first();

        // If not found by domain, try first one
        if (!$config) {
            $config = Config::first();
        }

        if (!$config || !$config->auto_update) {
            $this->info("Auto update is disabled or config not found.");
            return;
        }

        $currentVersion = config('app.version');
        $licenseKey = config('app.license_key'); // Get license key
        $updateServer = config('updater.server_url');

        $this->info("Checking for updates (Current: v$currentVersion)...");

        try {
            // 1. Check for update
            $response = Http::get($updateServer, [
                'action' => 'checkupdate',
                'version' => $currentVersion,
                'key' => $licenseKey, // Add key
                'domain' => parse_url($domain, PHP_URL_HOST)
            ]);

            if (!$response->successful()) {
                $this->error("Failed to connect to update server.");
                return;
            }

            $data = $response->json();
            if (!($data['update_available'] ?? false)) {
                $this->info("System is already up to date.");
                return;
            }

            $latestVersion = $data['latest_version'];
            $this->info("New version v$latestVersion available. Starting auto-upgrade...");

            // 2. Get file URL via get_products
            $fileResponse = Http::get($updateServer, [
                'action' => 'get_products',
                'key' => $licenseKey,
                'domain' => parse_url($domain, PHP_URL_HOST)
            ]);

            if (!$fileResponse->successful()) {
                $this->error("Failed to get product info for update.");
                return;
            }

            $productsData = $fileResponse->json()['data'] ?? $fileResponse->json();
            $fileUrl = null;

            // Try to find update_link in product list
            if (is_array($productsData)) {
                foreach ($productsData as $product) {
                    // Check if it matches our version or just take the first valid link
                    if (isset($product['update_link']) && !empty($product['update_link'])) {
                         $fileUrl = $product['update_link'];
                         break;
                    }
                }
            }
            
            // Fallback: check top-level if structure differs
            if (!$fileUrl && isset($productsData['update_link'])) {
                $fileUrl = $productsData['update_link'];
            }

            if (!$fileUrl) {
                $this->error("Failed to find update link in product info.");
                return;
            }

            // 3. Download
            $zipContent = Http::get($fileUrl)->body();
            $tempPath = storage_path('app/updates');
            if (!is_dir($tempPath)) mkdir($tempPath, 0777, true);
            
            $zipFile = $tempPath . "/autoupdate_{$latestVersion}.zip";
            file_put_contents($zipFile, $zipContent);

            // 4. Extract
            $zip = new ZipArchive;
            if ($zip->open($zipFile) === TRUE) {
                $zip->extractTo(base_path());
                $zip->close();
                unlink($zipFile);

                $this->info("Code updated. Running migrations...");

                // 5. AUTO MIGRATION SQL
                Artisan::call('migrate', ['--force' => true]);
                $migrationOutput = Artisan::output();
                $this->info("Migration Output: " . $migrationOutput);
                Log::info("Auto Update Migration Output: " . $migrationOutput);

                // 6. Clear Cache
                Artisan::call('cache:clear');
                Artisan::call('view:clear');
                Artisan::call('config:clear');

                $this->info("System auto-updated to v$latestVersion successfully!");
                Log::info("System auto-updated to v$latestVersion successfully!");
            } else {
                $this->error("Failed to extract update package.");
            }

        } catch (\Exception $e) {
            $this->error("Auto update error: " . $e->getMessage());
            Log::error("Auto Update Command Error: " . $e->getMessage());
        }
    }
}
