<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Pemeriksaan extends Model
{
    use SoftDeletes;

    protected $table = 'pemeriksaan';

    protected $fillable = [
        'nama',
        'tahun',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan',
        'gdrive_folder_id',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function surat()
    {
        return $this->hasMany(Surat::class, 'pemeriksaan_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'pemeriksaan_user');
    }
}
