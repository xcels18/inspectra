<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_data', function (Blueprint $table) {
            $table->text('judul_permintaan')->change();
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_data', function (Blueprint $table) {
            $table->string('judul_permintaan')->change();
        });
    }
};
