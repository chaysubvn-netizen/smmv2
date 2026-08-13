<?php
/**
 * SMM Panel Update Server
 * 
 * Cách dùng:
 * 1. Upload file này lên host của bạn.
 * 2. Tạo thư mục 'releases' cùng cấp với file này.
 * 3. Nén code của bạn thành file ZIP (ví dụ: 1.9.1.zip) và bỏ vào thư mục 'releases'.
 * 4. Gọi api qua: smm_update_server.php?action=check
 */

header('Content-Type: application/json');

$releases_dir = __DIR__ . '/releases/';
if (!is_dir($releases_dir)) {
    mkdir($releases_dir, 0755, true);
}

// Lấy danh sách các bản cập nhật
$action = $_GET['action'] ?? 'check';

if ($action === 'check') {
    $files = glob($releases_dir . '*.zip');
    
    if (empty($files)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Chưa có bản cập nhật nào trong thư mục releases.'
        ]);
        exit;
    }

    // Sắp xếp lấy bản mới nhất (dựa trên tên file dạng 1.0.0.zip)
    usort($files, function($a, $b) {
        return version_compare(basename($b, '.zip'), basename($a, '.zip'));
    });

    $latest_file = basename($files[0]);
    $latest_version = basename($latest_file, '.zip');
    
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $base_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    $base_url = rtrim($base_url, '/\\');

    echo json_encode([
        'status' => 'success',
        'version' => $latest_version,
        'download_url' => $base_url . '/smm_update_server.php?action=download&tag=' . $latest_version,
        'changelog' => 'Bản cập nhật v' . $latest_version . ' - Tối ưu hóa hệ thống và vá lỗi.',
        'message' => 'Phát hiện phiên bản mới: v' . $latest_version
    ]);
    exit;
}

if ($action === 'download') {
    $tag = $_GET['tag'] ?? '';
    
    if (empty($tag)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Thiếu thông tin phiên bản (tag).']);
        exit;
    }

    $file_path = $releases_dir . $tag . '.zip';
    
    if (!file_exists($file_path)) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy file cho phiên bản ' . $tag]);
        exit;
    }

    // Trả về file ZIP để tải xuống
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $tag . '.zip"');
    header('Content-Length: ' . filesize($file_path));
    header('Pragma: no-cache');
    header('Expires: 0');
    
    readfile($file_path);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Hành động không hợp lệ.']);
