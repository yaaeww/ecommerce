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
        Schema::table('ulasan', function (Blueprint $table) {
            $table->enum('status_moderasi', ['published', 'hidden', 'flagged'])->default('published')->after('ulasan');
            $table->text('catatan_moderasi')->nullable()->after('status_moderasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ulasan', function (Blueprint $table) {
            $table->dropColumn(['status_moderasi', 'catatan_moderasi']);
        });
    }
};
