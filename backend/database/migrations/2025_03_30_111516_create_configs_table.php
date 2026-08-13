<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->longText('title')->nullable();
            $table->longText('description')->nullable();
            $table->longText('keywords')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('og_image')->nullable();
            $table->string('landing_page')->nullable();
            $table->enum('maintenance_mode', ['on', 'off'])->default('off');
            // rank
            $table->integer('silver_rank')->default(0);
            $table->integer('gold_rank')->default(0);
            $table->integer('platinum_rank')->default(0);
            $table->integer('diamond_rank')->default(0);
            //
            $table->enum('auto_update_service', ['active', 'inactive'])->default('inactive');
            $table->string('rate_update_percent')->default(0);
            // mức tiền lên rank
            $table->string('up_silver')->default(0);
            $table->string('up_gold')->default(0);
            $table->string('up_platinum')->default(0);
            $table->string('up_diamond')->default(0);
            // thông tin nạp
            $table->string('transfer_code')->nullable();
            $table->integer('recharge_promotion')->default(0);
            $table->string('promotion_min')->default(0);
            // Trạng thái thanh toán
            $table->enum('bank_status', ['active', 'inactive'])->default('active');
            $table->enum('usdt_status', ['active', 'inactive'])->default('active');
            // support link
            $table->string('facebook_link')->nullable();
            $table->string('zalo_link')->nullable();
            $table->string('telegram_link')->nullable();
            $table->string('email_link')->nullable();
            // /
            $table->longText('fee_childpanel')->nullable();
            // cấu hình tiếp thị liên kết
            $table->integer('affiliate_percent')->default(0);
            $table->integer('affiliate_min')->default(0);
            $table->integer('affiliate_max')->default(0);
            $table->enum('affiliate_status', ['active', 'inactive'])->default('inactive');
            //script
            $table->longText('script_header')->nullable();
            $table->longText('script_body')->nullable();
            $table->longText('script_footer')->nullable();
            // timezone
            $table->string('timezone')->default('Asia/Ho_Chi_Minh');
            $table->string('currency')->default('VND');
            // bank info
            $table->longText('mb_account_number')->nullable();
            $table->longText('mb_api_key')->nullable();
            $table->longText('vcb_account_number')->nullable();
            $table->longText('vcb_api_key')->nullable();
            $table->longText('acb_account_number')->nullable();
            $table->longText('acb_api_key')->nullable();
            $table->longText('vtb_account_number')->nullable();
            $table->longText('vtb_api_key')->nullable();
            // usdt info
            $table->longText('usdt_wallet')->nullable();
            $table->longText('usdt_wallet_token')->nullable();
            $table->longText('usdt_exchange_rate')->nullable();
            $table->string('discount_usdt')->default(0);
            // telegram_bot
            $table->longText('telegram_bot')->nullable();
            $table->longText('telegram_chat_id')->nullable();
            $table->enum('telegram_status', ['active', 'inactive'])->default('inactive');
            // smtp
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_from_name')->nullable();

            // notiffication
            $table->longText('notice_modal')->nullable();
            $table->longText('notice_service')->nullable();

            $table->enum('status', ['active', 'inactive', 'pending', 'delete', 'expired'])->default('pending');
            $table->timestamp('last_payment_time')->nullable();
            $table->timestamps();
            $table->string('domain_main')->nullable();
            $table->string('domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configs');
    }
};
