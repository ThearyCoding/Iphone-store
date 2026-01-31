<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // KHQR / Bakong
            $table->string('khqr_md5')->nullable()->index();
            $table->string('khqr_qr')->nullable(); // store raw KHQR string (optional)
            $table->string('bakong_hash')->nullable();
            $table->string('paid_from_account')->nullable();
            $table->string('paid_to_account')->nullable();
            $table->timestamp('paid_at')->nullable();

            // adjust payment_status default to pending
            $table->string('payment_status')->default('pending')->change();
            $table->string('transaction_id')->nullable()->change(); // allow null until paid
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'khqr_md5',
                'khqr_qr',
                'bakong_hash',
                'paid_from_account',
                'paid_to_account',
                'paid_at',
            ]);
            // cannot safely revert "change" without knowing old schema; optional
        });
    }
};
