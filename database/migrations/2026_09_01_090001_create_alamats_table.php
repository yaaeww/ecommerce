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
        Schema::create('alamats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('label')->default('Rumah'); // Rumah, Kantor, Toko, dll
            $table->string('nama_penerima');
            $table->string('no_hp', 25);
            $table->string('provinsi')->default('Jawa Barat');
            $table->string('kota_kabupaten')->default('Kab. Indramayu');
            $table->string('kecamatan')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->text('alamat_lengkap');
            $table->string('patokan')->nullable();
            $table->boolean('is_utama')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alamats');
    }
};
