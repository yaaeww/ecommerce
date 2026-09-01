<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah enum status_pesanan menjadi string(50) agar mendukung status 'batal', 'dikemas', 'dikirim', 'diterima', 'belum_diterima'
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status_pesanan` VARCHAR(50) NULL DEFAULT 'belum_diterima'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status_pesanan` ENUM('dikemas', 'dikirim', 'diterima', 'belum_diterima') NULL DEFAULT 'belum_diterima'");
    }
};
