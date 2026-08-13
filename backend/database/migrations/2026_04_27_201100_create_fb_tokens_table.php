<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fb_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('cookie_token'); // cookie_token, cookie, token
            $table->text('data');
            $table->string('uid')->nullable();
            $table->string('name')->nullable();
            $table->string('note')->nullable();
            $table->string('status')->default('live'); // live, die, block, processing
            $table->string('domain')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fb_tokens');
    }
};
