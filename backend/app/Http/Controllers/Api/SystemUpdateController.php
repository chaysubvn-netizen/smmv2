<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UpdateService;
use App\Models\SystemUpdateLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SystemUpdateController extends Controller
{
    public function status(Request $request, UpdateService $updates): JsonResponse
    {
        $this->authorizeAdmin($request);

        return response()->json([
            'status' => true,
            'data' => $updates->status(),
            'history' => SystemUpdateLog::query()->latest()->limit(50)->get(),
        ]);
    }

    public function check(Request $request, UpdateService $updates): JsonResponse
    {
        $this->authorizeAdmin($request);
        $release = $updates->checkUpdate();

        if (!$release) {
            return response()->json(['status' => false, 'message' => 'Không thể kết nối máy chủ cập nhật.'], 502);
        }
        $release['download_url'] = $release['download_url'] ?? $release['url'] ?? null;
        if (empty($release['download_url']) || !preg_match('/^[a-fA-F0-9]{64}$/', (string) ($release['sha256'] ?? ''))) {
            return response()->json(['status' => false, 'message' => 'Máy chủ cập nhật trả về gói thiếu URL hoặc SHA-256.'], 502);
        }

        return response()->json([
            'status' => true,
            'data' => $release,
            'update_available' => version_compare((string) ($release['version'] ?? '0'), $updates->currentVersion(), '>'),
        ]);
    }

    public function install(Request $request, UpdateService $updates): JsonResponse
    {
        $this->authorizeAdmin($request);
        $requested = $request->validate([
            'version' => ['required', 'string', 'max:40'],
            'download_url' => ['required', 'url', 'max:2048'],
            'sha256' => ['required', 'string', 'size:64', 'regex:/^[a-fA-F0-9]+$/'],
        ]);
        $release = $updates->checkUpdate();
        $serverUrl = $release ? ($release['download_url'] ?? $release['url'] ?? null) : null;
        abort_unless(
            $release
            && hash_equals((string) $release['version'], $requested['version'])
            && hash_equals((string) $serverUrl, $requested['download_url'])
            && hash_equals(strtolower((string) ($release['sha256'] ?? '')), strtolower($requested['sha256'])),
            422,
            'Thông tin bản phát hành không khớp với máy chủ cập nhật.'
        );

        $log = SystemUpdateLog::create([
            'from_version' => $updates->currentVersion(),
            'to_version' => $requested['version'],
            'status' => 'processing',
            'file_name' => basename((string) parse_url($requested['download_url'], PHP_URL_PATH)),
            'admin_name' => $request->user()->name ?: $request->user()->username,
            'message' => 'Đang cập nhật hệ thống.',
        ]);
        $result = $updates->update($requested['download_url'], $requested['version'], strtolower($requested['sha256']));
        $log->update([
            'status' => $result['status'],
            'message' => $result['message'],
            'log' => $result['steps'] ?? [],
            'migrations' => (int) ($result['migrations'] ?? 0),
        ]);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy(Request $request, SystemUpdateLog $log): JsonResponse
    {
        $this->authorizeAdmin($request);
        $log->delete();
        return response()->json(['status' => true]);
    }

    public function destroyAll(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        SystemUpdateLog::query()->delete();
        return response()->json(['status' => true]);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403, 'Bạn không có quyền quản trị.');
    }
}
