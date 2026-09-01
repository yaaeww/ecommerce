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
        // 1. Enhancements untuk tabel produks
        Schema::table('produks', function (Blueprint $table) {
            if (!Schema::hasColumn('produks', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('stok');
            }
            if (!Schema::hasColumn('produks', 'harga_coret')) {
                $table->decimal('harga_coret', 10, 2)->nullable()->after('harga');
            }
            if (!Schema::hasColumn('produks', 'berat_gram')) {
                $table->integer('berat_gram')->default(1000)->after('harga_coret');
            }
        });

        // 2. Enhancements untuk tabel umkms (Mode Libur / Holiday Mode)
        Schema::table('umkms', function (Blueprint $table) {
            if (!Schema::hasColumn('umkms', 'is_libur')) {
                $table->boolean('is_libur')->default(false)->after('status');
            }
            if (!Schema::hasColumn('umkms', 'libur_pesan')) {
                $table->text('libur_pesan')->nullable()->after('is_libur');
            }
            if (!Schema::hasColumn('umkms', 'libur_sampai')) {
                $table->date('libur_sampai')->nullable()->after('libur_pesan');
            }
        });

        // 3. Enhancements untuk tabel ulasan (Tanggapan / Balasan Penjual)
        Schema::table('ulasan', function (Blueprint $table) {
            if (!Schema::hasColumn('ulasan', 'balasan_penjual')) {
                $table->text('balasan_penjual')->nullable()->after('status_moderasi');
            }
            if (!Schema::hasColumn('ulasan', 'balasan_penjual_at')) {
                $table->timestamp('balasan_penjual_at')->nullable()->after('balasan_penjual');
            }
        });

        // 4. Enhancements untuk tabel orders (Bukti Foto Pengiriman)
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'foto_bukti_pengiriman')) {
                $table->string('foto_bukti_pengiriman')->nullable()->after('resi_pengiriman');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'harga_coret', 'berat_gram']);
        });

        Schema::table('umkms', function (Blueprint $table) {
            $table->dropColumn(['is_libur', 'libur_pesan', 'libur_sampai']);
        });

        Schema::table('ulasan', function (Blueprint $table) {
            $table->dropColumn(['balasan_penjual', 'balasan_penjual_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['foto_bukti_pengiriman']);
        });
    }
};
