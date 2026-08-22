<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Surat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat';

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'tanggal_terima',
        'perihal',
        'keterangan',
        'tahun_anggaran',
        'deadline',
        'file_surat',
        'status',
        'created_by',
        'gdrive_folder_id',
        'gdrive_folder_structure',
        'pemeriksaan_id',
    ];

    protected $casts = [
        'tanggal_surat'           => 'date',
        'tanggal_terima'          => 'date',
        'deadline'                => 'date',
        'gdrive_folder_structure' => 'array',
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pemeriksaan()
    {
        return $this->belongsTo(Pemeriksaan::class, 'pemeriksaan_id');
    }

    public function judulPermintaan()
    {
        return $this->hasMany(JudulPermintaan::class, 'surat_id')->orderBy('nomor_urut');
    }

    public function permintaanData()
    {
        return $this->hasMany(PermintaanData::class, 'surat_id')->orderBy('nomor_urut');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'aktif' => 'Aktif',
            'selesai' => 'Selesai',
            'arsip' => 'Arsip',
            default => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'aktif' => 'primary',
            'selesai' => 'success',
            'arsip' => 'secondary',
            default => 'secondary',
        };
    }

    public function getTotalPermintaanAttribute(): int
    {
        return $this->permintaanData()->count();
    }

    public function getTotalSelesaiAttribute(): int
    {
        return $this->permintaanData()->whereIn('status', ['selesai', 'proses'])->count();
    }

    public function getProgressAttribute(): int
    {
        $total = $this->total_permintaan;
        if ($total === 0) return 0;
        return (int) round(($this->total_selesai / $total) * 100);
    }

    public function getOpdSelesaiAttribute(): int
    {
        return $this->judulPermintaan
            ->flatMap->permintaanData
            ->flatMap->permintaanOpd
            ->where('status', 'selesai')
            ->count();
    }

    public function getOpdProsesAttribute(): int
    {
        return $this->judulPermintaan
            ->flatMap->permintaanData
            ->flatMap->permintaanOpd
            ->where('status', 'proses')
            ->count();
    }

    public function getOpdTotalAttribute(): int
    {
        return $this->judulPermintaan
            ->flatMap->permintaanData
            ->flatMap->permintaanOpd
            ->count();
    }

    public function getOpdProgressAttribute(): int
    {
        $total = $this->opd_total;
        if ($total === 0) return 0;
        return (int) round((($this->opd_selesai + $this->opd_proses) / $total) * 100);
    }
}
