<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_data', function (Blueprint $table) {
            $table->string('opd')->nullable()->after('judul_permintaan');
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_data', function (Blueprint $table) {
            $table->dropColumn('opd');
        });
    }
};
