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
        Schema::create('pesan_kontaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama', 255);
            $table->string('email', 255);
            $table->string('no_telepon', 50)->nullable();
            $table->string('kategori', 50)->default('pertanyaan_umum'); // pertanyaan_umum, kerjasama_umkm, partai_besar, kendala_transaksi, masukan
            $table->string('subjek', 255);
            $table->text('pesan');
            $table->string('status', 30)->default('belum_dibaca'); // belum_dibaca, dibaca, dibalas, diarsipkan
            $table->text('balasan_admin')->nullable();
            $table->foreignId('dibalas_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dibalas_pada')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('kategori');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesan_kontaks');
    }
};
