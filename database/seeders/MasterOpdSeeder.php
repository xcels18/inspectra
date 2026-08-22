<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterOpdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opds = [
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
            'BADAN KESATUAN BANGSA DAN POLITIK'
        ];
        foreach ($opds as $opd) {
            \App\Models\MasterOpd::firstOrCreate(['nama' => $opd]);
        }
    }
}
