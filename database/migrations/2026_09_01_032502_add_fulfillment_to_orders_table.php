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
            $table->string('resi_pengiriman')->nullable()->after('status_pesanan');
            $table->string('kurir')->nullable()->after('resi_pengiriman');
            $table->timestamp('tanggal_dikirim')->nullable()->after('kurir');
            $table->text('catatan_pengiriman')->nullable()->after('tanggal_dikirim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['resi_pengiriman', 'kurir', 'tanggal_dikirim', 'catatan_pengiriman']);
        });
    }
};
