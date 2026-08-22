<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('judul_permintaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_id')->constrained('surat')->onDelete('cascade');
            $table->integer('nomor_urut');
            $table->string('judul');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('judul_permintaan');
    }
};
