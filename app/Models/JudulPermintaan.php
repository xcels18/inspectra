<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JudulPermintaan extends Model
{
    use HasFactory;

    protected $table = 'judul_permintaan';

    protected $fillable = [
        'surat_id',
        'nomor_urut',
        'judul',
    ];

    public function surat()
    {
        return $this->belongsTo(Surat::class, 'surat_id');
    }

    public function permintaanData()
    {
        return $this->hasMany(PermintaanData::class, 'judul_permintaan_id')->orderBy('nomor_urut');
    }
}
