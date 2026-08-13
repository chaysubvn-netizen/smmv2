<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use ZipArchive;
use File;

class UpdateController extends Controller
{
    public function autoUpdate(Request $request)
    {
        // Version hiện tại từ query string
        $currentVersion = $request->input('version');
        if (!$currentVersion) {
            return response()->json(['ok'=>false, 'message'=>'Missing version parameter']);
        }

        // Use configured URL or fallback to local mock for testing
        // In production, this should come from env('UPDATE_SERVER_URL')
        $apiUrl = config('updater.server_url');

        // Gọi API check update
        $response = Http::timeout(5)->get($apiUrl, [
            'version' => $currentVersion,
            'domain' => request()->getHost()
        ])->json();

        if (!$response['ok'] || !$response['update_available']) {
            return response()->json([
                'ok' => true,
                'message' => 'No update available or already latest version.'
            ]);
        }

        $latestVersion = $response['latest_version'] ?? null;
        $fileUrl = $response['file_url'] ?? null;

        if (!$latestVersion || !$fileUrl) {
            return response()->json(['ok'=>false, 'message'=>'Invalid update info']);
        }

        // Tạo thư mục storage nếu chưa có
        $zipDir = storage_path("app/updates");
        if (!is_dir($zipDir)) mkdir($zipDir, 0777, true);

        // Tải file zip
        $zipFile = $zipDir . "/app_update_$latestVersion.zip";
        $zipData = Http::get($fileUrl)->body();
        file_put_contents($zipFile, $zipData);

        // Giải nén vào root project
        $zip = new ZipArchive;
        if ($zip->open($zipFile) === TRUE) {
            $zip->extractTo(base_path());
            $zip->close();
            unlink($zipFile);
            return response()->json([
                'ok' => true,
                'message' => "Update applied successfully to version $latestVersion"
            ]);
        } else {
            return response()->json(['ok'=>false, 'message'=>'Failed to extract zip file']);
        }
    }
}
