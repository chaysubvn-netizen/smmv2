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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->string('level')->default('member');
            $table->decimal('balance', 10, 2)->default(0);
            $table->decimal('total_deposit', 10, 2)->default(0);
            $table->decimal('commission', 10, 2)->default(0);
            $table->decimal('total_commission', 10, 2)->default(0);
            $table->decimal('total_withdraw', 10, 2)->default(0);
            $table->string('timezone')->default('Asia/Ho_Chi_Minh');
            $table->string('language')->default('vi');
            $table->string('currency')->default('VND');
            $table->string('avatar')->nullable();
            $table->string('two_factor_secret')->nullable();
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active');
            $table->string('referral_code')->nullable(); // mã giới thiệu cua người dùng
            $table->string('ref_by_code')->nullable(); // mã giới thiệu của người giới thiệu
            $table->string('api_key')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->string('domain')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
