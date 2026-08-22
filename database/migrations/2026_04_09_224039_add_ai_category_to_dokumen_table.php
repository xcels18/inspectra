<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->string('ai_kategori')->nullable()->after('is_read');
            $table->text('ai_ringkasan')->nullable()->after('ai_kategori');
            $table->timestamp('ai_analyzed_at')->nullable()->after('ai_ringkasan');
        });
    }

    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropColumn(['ai_kategori', 'ai_ringkasan', 'ai_analyzed_at']);
        });
    }
};
