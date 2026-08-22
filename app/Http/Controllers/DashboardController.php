<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\PermintaanData;
use App\Models\PermintaanOpd;
use App\Models\Dokumen;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $totalSurat = Surat::count();
        $suratAktif = Surat::where('status', 'aktif')->count();
        $suratSelesai = Surat::where('status', 'selesai')->count();

        $opdBaseQuery = PermintaanOpd::whereHas('permintaan.surat', fn($q) => $q->whereNull('deleted_at'));

        $totalOpd = (clone $opdBaseQuery)->count();
        $opdBelum = (clone $opdBaseQuery)->where('status', 'belum')->count();
        $opdProses = (clone $opdBaseQuery)->where('status', 'proses')->count();
        $opdSelesai = (clone $opdBaseQuery)->where('status', 'selesai')->count();

        $totalDokumen = Dokumen::where(function ($q) {
            $q->whereHas('permintaan.surat', fn($sq) => $sq->whereNull('deleted_at'))
              ->orWhereHas('permintaanOpd.permintaan.surat', fn($sq) => $sq->whereNull('deleted_at'));
        })->count();

        $progressPersen = $totalOpd > 0 ? round((($opdSelesai + $opdProses) / $totalOpd) * 100) : 0;

        $suratDeadlineDekat = Surat::whereNotNull('deadline')
            ->where('deadline', '>=', now())
            ->where('deadline', '<=', now()->addDays(14))
            ->where('status', '!=', 'selesai')
            ->orderBy('deadline')
            ->count();

        $suratOverdue = Surat::whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->where('status', '!=', 'selesai')
            ->count();

        $suratTerbaru = Surat::orderByDesc('tanggal_terima')->limit(5)->get();

        $aktivitasTerbaru = Dokumen::with([
            'permintaanOpd',
            'permintaan.surat',
            'permintaan.judulPermintaan',
            'uploader',
        ])->where(function ($q) {
            $q->whereHas('permintaan.surat', fn($sq) => $sq->whereNull('deleted_at'))
              ->orWhereHas('permintaanOpd.permintaan.surat', fn($sq) => $sq->whereNull('deleted_at'));
        })->orderByDesc('created_at')->limit(10)->get();

        $suratDeadline = Surat::whereNotNull('deadline')
            ->where('status', '!=', 'selesai')
            ->orderBy('deadline')
            ->limit(6)
            ->get();

        $opdProgress = PermintaanOpd::whereHas('permintaan.surat', fn($q) => $q->whereNull('deleted_at'))
            ->selectRaw('opd, count(*) as total, sum(case when status in ("selesai","proses") then 1 else 0 end) as selesai')
            ->groupBy('opd')
            ->having('total', '>', 0)
            ->orderByRaw('(selesai/total) desc, total desc')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'totalSurat', 'suratAktif', 'suratSelesai',
            'totalOpd', 'opdBelum', 'opdProses', 'opdSelesai',
            'totalDokumen', 'progressPersen',
            'suratDeadlineDekat', 'suratOverdue',
            'suratTerbaru', 'aktivitasTerbaru', 'suratDeadline', 'opdProgress'
        ));
    }
}
