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
        if (Schema::hasColumn('kategori_produks', 'icon')) {
            try {
                Schema::table('kategori_produks', function (Blueprint $table) {
                    $table->renameColumn('icon', 'gambar');
                });
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `kategori_produks` CHANGE `icon` `gambar` VARCHAR(255) NULL");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('kategori_produks', 'gambar')) {
            try {
                Schema::table('kategori_produks', function (Blueprint $table) {
                    $table->renameColumn('gambar', 'icon');
                });
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `kategori_produks` CHANGE `gambar` `icon` VARCHAR(255) NULL");
            }
        }
    }
};
