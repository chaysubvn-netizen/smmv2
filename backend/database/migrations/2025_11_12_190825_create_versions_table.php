<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('versions', function (Blueprint $table) {
            $table->id();
            $table->string('version');
            $table->text('changelog')->nullable();
            $table->string('file_url');
            $table->boolean('is_latest')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('versions');
    }
};
