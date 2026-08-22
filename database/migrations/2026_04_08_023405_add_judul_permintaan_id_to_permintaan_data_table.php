<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_data', function (Blueprint $table) {
            $table->foreignId('judul_permintaan_id')->nullable()->after('surat_id')->constrained('judul_permintaan')->onDelete('cascade');
            $table->dropColumn('deadline');
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_data', function (Blueprint $table) {
            $table->dropForeign(['judul_permintaan_id']);
            $table->dropColumn('judul_permintaan_id');
            $table->date('deadline')->nullable();
        });
    }
};
