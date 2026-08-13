<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

DB::table('users')->where('role', 'admin')->update(['two_factor_method' => null]);
echo "Đã tắt 2FA Telegram cho Admin thành công! Bạn có thể về trang chủ để đăng nhập lại bằng mật khẩu.";
