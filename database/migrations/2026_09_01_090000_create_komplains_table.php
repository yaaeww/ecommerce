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
        Schema::create('komplains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pembeli
            $table->enum('tipe_komplain', [
                'buah_busuk',
                'kardus_rusak',
                'berat_kurang',
                'tidak_sesuai',
                'lainnya'
            ])->default('buah_busuk');
            $table->text('deskripsi');
            $table->string('foto_bukti')->nullable();
            $table->string('video_unboxing')->nullable();
            $table->enum('solusi_diminta', ['refund', 'ganti_buah'])->default('refund');
            $table->enum('status', ['diajukan', 'diproses', 'disetujui', 'ditolak', 'selesai'])->default('diajukan');
            $table->text('catatan_admin')->nullable();
            $table->decimal('nominal_refund', 12, 2)->nullable();
            $table->timestamp('diproses_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komplains');
    }
};
