<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Services\UpdateService;
use Illuminate\Http\Request;
use App\VersionHelper;

class SystemUpdateController extends Controller
{
    /**
     * Hiển thị trang cập nhật
     */
    public function index()
    {
        $currentVersion = VersionHelper::get();
        return view('admin.system.update', compact('currentVersion'));
    }

    /**
     * Kiểm tra cập nhật hệ thống
     */
    public function apiCheckUpdate(Request $request, UpdateService $updateService)
    {
        $domain = $request->getHost();
        // Kiểm tra nếu là site chính (main site) mới được phép cập nhật
        if ($domain != env('MAIN_SITE')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không có quyền thực hiện chức năng này.'
            ], 403);
        }

        $data = $updateService->checkUpdate();
        $currentVersion = VersionHelper::get();

        if ($data) {
            $hasUpdate = version_compare((string)$data['version'], (string)$currentVersion) > 0;
            return response()->json([
                'ok' => true,
                'status' => 'success',
                'has_update' => $hasUpdate,
                'update_available' => $hasUpdate,
                'latest_version' => $data['version'],
                'data' => $data,
                'message' => $hasUpdate ? 'Phát hiện phiên bản mới!' : 'Hệ thống đã ở phiên bản mới nhất.',
                'current_version' => $currentVersion
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Không thể kết nối đến máy chủ cập nhật.'
        ]);
    }

    /**
     * Chạy cập nhật hệ thống
     */
    public function runUpdate(Request $request, UpdateService $updateService)
    {
        $domain = $request->getHost();
        if ($domain != env('MAIN_SITE')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không có quyền thực hiện chức năng này.'
            ], 403);
        }

        $downloadUrl = $request->input('url');
        $newVersion = $request->input('version');

        if (empty($downloadUrl) || empty($newVersion)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thông tin cập nhật không đầy đủ.'
            ]);
        }

        // Chạy cập nhật
        $result = $updateService->update($downloadUrl, $newVersion);

        return response()->json($result);
    }
}
