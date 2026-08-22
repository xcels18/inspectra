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
        return [
            'DINAS PENDIDIKAN DAN KEBUDAYAAN',
            'DINAS KESEHATAN',
            'RUMAH SAKIT UMUM DAERAH MULIA',
            'DINAS PEKERJAAN UMUM DAN PERUMAHAN RAKYAT',
            'BADAN PENANGGULANGAN BENCANA DAERAH',
            'SATUAN POLISI PAMONG PRAJA',
            'DINAS SOSIAL',
            'DINAS TENAGA KERJA',
            'DINAS PEMBERDAYAAN PEREMPUAN, PERLINDUNGAN ANAK DAN KELUARGA BERENCANA',
            'DINAS KETAHANAN PANGAN',
            'DINAS LINGKUNGAN HIDUP PERKEBUNAN DAN PETERNAKAN',
            'DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL',
            'BADAN PEMBERDAYAAN MASYARAKAT KAMPUNG',
            'DINAS PERHUBUNGAN',
            'DINAS KOMUNIKASI DAN INFORMATIKA',
            'DINAS KOPERASI PERINDUSTRIAN PERDAGANGAN DAN UKM',
            'DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU ATAP',
            'DINAS KEARSIPAN DAN PERPUSTAKAAN',
            'DINAS PARIWISATA PEMUDA DAN OLAHRAGA',
            'DINAS PERTANIAN DAN PERIKANAN',
            'SEKRETARIAT DAERAH',
            'SEKRETARIAT DPRD',
            'BADAN PERENCANAAN PEMBANGUNAN DAERAH',
            'BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH ( BPKAD )',
            'BADAN PENGELOLAAN PAJAK DAN RETRIBUSI DAERAH',
            'BADAN KEPEGAWAIAN DAN PENDIDIKAN DAN PELATIHAN DAERAH',
            'INSPEKTORAT',
            'KANTOR DISTRIK MULIA',
            'KANTOR DISTRIK FAWI',
            'KANTOR DISTRIK MEWOLUK',
            'KANTOR DISTRIK YAMO',
            'KANTOR DISTRIK TINGGINAMBUT',
            'KANTOR DISTRIK NUME',
            'KANTOR DISTRIK TORERE',
            'KANTOR DISTRIK PAGALEME',
            'KANTOR DISTRIK MUARA',
            'KANTOR DISTRIK YAMBI',
            'KANTOR DISTRIK ILAMBURAWI',
            'KANTOR DISTRIK DOKOME',
            'KANTOR DISTRIK MOLANIKIME',
            'KANTOR DISTRIK LUMO',
            'KANTOR DISTRIK KIYAGE',
            'KANTOR DISTRIK IRIMULI',
            'KANTOR DISTRIK GURAGE',
            'KANTOR DISTRIK KALOME',
            'KANTOR DISTRIK WONWI',
            'KANTOR DISTRIK NIOGA',
            'KANTOR DISTRIK TAGANOMBAK',
            'KANTOR DISTRIK GUBUME',
            'KANTOR DISTRIK YAMONERI',
            'KANTOR DISTRIK WUYUNERI',
            'KANTOR DISTRIK DAGAI',
            'KANTOR DISTRIK ILU',
            'KANTOR DISTRIK WAEGI',
            'BADAN KESATUAN BANGSA DAN POLITIK',
        ];
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
