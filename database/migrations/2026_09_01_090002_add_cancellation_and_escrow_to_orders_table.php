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
        Schema::table('orders', function (Blueprint $table) {
            $table->text('batal_alasan')->nullable()->after('foto_bukti_pengiriman');
            $table->timestamp('batal_at')->nullable()->after('batal_alasan');
            $table->timestamp('dikemas_at')->nullable()->after('batal_at');
            $table->timestamp('dikirim_at')->nullable()->after('dikemas_at');
            $table->timestamp('diterima_at')->nullable()->after('dikirim_at');
            $table->boolean('is_escrow_released')->default(false)->after('diterima_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'batal_alasan',
                'batal_at',
                'dikemas_at',
                'dikirim_at',
                'diterima_at',
                'is_escrow_released'
            ]);
        });
    }
};
