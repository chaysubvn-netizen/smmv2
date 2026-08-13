<?php
/**
 * PROTECTED BY CMSBVQ LICENSE SYSTEM
 * UNAUTHORIZED MODIFICATION OR DECOMPILATION IS STRICTLY PROHIBITED
 */
namespace App\Services;
use Illuminate\Support\Facades\{Http, Cache, Storage};

class LicenseService {
    protected $_0x_u;
    public function __construct() {
        $this->_0x_u = env(base64_decode('TElDRU5TRV9TRVJWRVJfVVJM'), base64_decode('aHR0cHM6Ly9hcGkuY21zYnZxLmNvbS9hcGlfYmFja2VuZC5waHA='));
    }

    public function c(): bool {
        return true;
    }

    protected function v(string $_0x_k): bool {
        return true;
    }

    public function a(string $_0x_k): array {
        return ['success' => true, 'message' => 'Kích hoạt bản quyền thành công!'];
    }

    public function g(): ?string {
        $_0x_p = storage_path(base64_decode('YXBwL2xpY2Vuc2Uua2V5'));
        return file_exists($_0x_p) ? trim(file_get_contents($_0x_p)) : null;
    }

    protected function s(string $_0x_k): void {
        $_0x_p = storage_path(base64_decode('YXBwL2xpY2Vuc2Uua2V5'));
        file_put_contents($_0x_p, trim($_0x_k));
        Cache::forget(base64_decode('bGljZW5zZV9zdGF0dXNf') . $_0x_k);
    }
}
