<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_opd', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_id')->constrained('permintaan_data')->onDelete('cascade');
            $table->string('opd');
            $table->enum('status', ['belum', 'proses', 'selesai'])->default('belum');
            $table->text('catatan')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();

            $table->unique(['permintaan_id', 'opd']);
        });

        Schema::table('dokumen', function (Blueprint $table) {
            $table->foreignId('permintaan_opd_id')->nullable()->after('permintaan_id')->constrained('permintaan_opd')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropForeign(['permintaan_opd_id']);
            $table->dropColumn('permintaan_opd_id');
        });
        Schema::dropIfExists('permintaan_opd');
    }
};
