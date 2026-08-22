<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanData extends Model
{
    use HasFactory;

    protected $table = 'permintaan_data';

    protected $fillable = [
        'surat_id',
        'judul_permintaan_id',
        'nomor_urut',
        'judul_permintaan',
        'opd',
        'deskripsi',
        'status',
        'catatan',
        'penanggung_jawab',
        'selesai_at',
    ];

    protected $casts = [
        'opd' => 'array',
        'selesai_at' => 'datetime',
    ];

    public static function daftarOpd(): array
    {
        return \App\Models\MasterOpd::orderBy('nama')->pluck('nama')->toArray();
    }

    public static function opsiOpd(): array
    {
        return array_merge(['Semua OPD'], self::daftarOpd());
    }

    public function surat()
    {
        return $this->belongsTo(Surat::class, 'surat_id');
    }

    public function judulPermintaan()
    {
        return $this->belongsTo(JudulPermintaan::class, 'judul_permintaan_id');
    }

    public function penanggungjawab()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab');
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'permintaan_id');
    }

    public function permintaanOpd()
    {
        return $this->hasMany(PermintaanOpd::class, 'permintaan_id');
    }

    public function syncOpd(array $opdList): void
    {
        $existing = $this->permintaanOpd()->pluck('opd')->toArray();
        $toAdd = array_diff($opdList, $existing);
        foreach ($toAdd as $opd) {
            $this->permintaanOpd()->create(['opd' => $opd, 'status' => 'belum']);
        }
        $toRemove = array_diff($existing, $opdList);
        if (!empty($toRemove)) {
            $this->permintaanOpd()->whereIn('opd', $toRemove)->delete();
        }
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'belum' => 'Belum',
            'proses' => 'Sedang Diproses',
            'selesai' => 'Selesai',
            default => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'belum' => 'danger',
            'proses' => 'warning',
            'selesai' => 'success',
            default => 'secondary',
        };
    }
}
