<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('UPDATE permintaan_data SET opd = CONCAT(\'["\', REPLACE(opd, \'"\', \'\\\\"\'), \'"]\') WHERE opd IS NOT NULL AND opd NOT LIKE \'[%\'');

        Schema::table('permintaan_data', function (Blueprint $table) {
            $table->json('opd')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_data', function (Blueprint $table) {
            $table->string('opd')->nullable()->change();
        });
    }
};
