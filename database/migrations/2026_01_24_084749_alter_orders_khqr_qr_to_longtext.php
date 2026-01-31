<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // change khqr_qr to LONGTEXT
            $table->longText('khqr_qr')->nullable()->change();

            // md5 can stay string, but make sure length is enough
            $table->string('khqr_md5', 128)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('khqr_qr')->nullable()->change();
            $table->string('khqr_md5')->nullable()->change();
        });
    }
};
