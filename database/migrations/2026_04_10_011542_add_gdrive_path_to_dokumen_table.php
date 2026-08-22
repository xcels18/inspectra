<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->string('gdrive_path')->nullable()->after('ai_analyzed_at');
            $table->timestamp('gdrive_synced_at')->nullable()->after('gdrive_path');
        });
    }

    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropColumn(['gdrive_path', 'gdrive_synced_at']);
        });
    }
};
