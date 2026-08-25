<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';

    protected $fillable = [
        'permintaan_id',
        'permintaan_opd_id',
        'nama_file',
        'path_file',
        'mime_type',
        'ukuran_file',
        'keterangan',
        'is_read',
        'uploaded_by',
        'gdrive_path',
        'gdrive_synced_at',
    ];

    protected $casts = [
        'gdrive_synced_at' => 'datetime',
        'is_read'          => 'boolean',
    ];

    public function permintaan()
    {
        return $this->belongsTo(PermintaanData::class, 'permintaan_id');
    }

    public function permintaanOpd()
    {
        return $this->belongsTo(PermintaanOpd::class, 'permintaan_opd_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUkuranFormatAttribute(): string
    {
        $bytes = (float)($this->ukuran_file ?? 0);
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
