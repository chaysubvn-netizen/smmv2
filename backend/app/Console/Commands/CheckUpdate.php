<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\LicenseChecker;

class CheckUpdate extends Command
{
    protected $signature = 'license:check-update';
    protected $description = 'Check license and update Laravel';

    public function handle()
    {
        $checker = new LicenseChecker();
        $update = $checker->getUpdate();

        if ($update) {
            $this->info("Bản mới: ".$update['version']);
            $this->info("Changelog: ".$update['changelog']);

            if ($checker->downloadUpdate($update['file_path'])) {
                $this->info("Cập nhật thành công!");
            } else {
                $this->error("Tải bản cập nhật thất bại!");
            }
        } else {
            $this->info("Đang dùng phiên bản mới nhất.");
        }
    }
}