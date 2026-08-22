<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop unique index lama jika ada
        try {
            Schema::table('surat', function (Blueprint $table) {
                $table->dropUnique('surat_nomor_surat_unique');
            });
        } catch (\Throwable $e) {
            // Abaikan jika index sudah tidak ada
        }

        // Tambah unique composite index agar soft-deleted row tidak memblokir reuse nomor_surat
        Schema::table('surat', function (Blueprint $table) {
            $table->unique(['nomor_surat', 'deleted_at'], 'surat_nomor_surat_deleted_at_unique');
        });
    }

    public function down(): void
    {
        // Hapus composite unique index
        try {
            Schema::table('surat', function (Blueprint $table) {
                $table->dropUnique('surat_nomor_surat_deleted_at_unique');
            });
        } catch (\Throwable $e) {
            // Abaikan jika index sudah tidak ada
        }

        // Kembalikan unique index lama
        Schema::table('surat', function (Blueprint $table) {
            $table->unique('nomor_surat', 'surat_nomor_surat_unique');
        });
    }
};
