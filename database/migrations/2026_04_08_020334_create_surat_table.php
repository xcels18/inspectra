<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->date('tanggal_surat');
            $table->date('tanggal_terima');
            $table->string('perihal');
            $table->text('keterangan')->nullable();
            $table->string('tahun_anggaran', 10);
            $table->string('file_surat')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'arsip'])->default('aktif');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat');
    }
};
