<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_id')->constrained('surat')->onDelete('cascade');
            $table->integer('nomor_urut');
            $table->string('judul_permintaan');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['belum', 'proses', 'selesai'])->default('belum');
            $table->text('catatan')->nullable();
            $table->date('deadline')->nullable();
            $table->foreignId('penanggung_jawab')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_data');
    }
};
