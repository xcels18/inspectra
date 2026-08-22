<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanOpd extends Model
{
    use HasFactory;

    protected $table = 'permintaan_opd';

    protected $fillable = [
        'permintaan_id',
        'opd',
        'status',
        'catatan',
        'selesai_at',
    ];

    protected $casts = [
        'selesai_at' => 'datetime',
    ];

    public function permintaan()
    {
        return $this->belongsTo(PermintaanData::class, 'permintaan_id');
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'permintaan_opd_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'belum'   => 'Belum',
            'proses'  => 'Sedang Diproses',
            'selesai' => 'Selesai',
            default   => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'belum'   => 'danger',
            'proses'  => 'warning',
            'selesai' => 'success',
            default   => 'secondary',
        };
    }
}
