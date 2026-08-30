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
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $userId = $user->id;

        // Base Query untuk Pemeriksaan
        $pemeriksaanBaseQuery = \App\Models\Pemeriksaan::query();
        if (!$isAdmin) {
            $pemeriksaanBaseQuery->whereHas('users', fn($q) => $q->where('user_id', $userId));
        }

        // 1. Pemeriksaan Stats
        $totalPemeriksaan = (clone $pemeriksaanBaseQuery)->count();
        $pemeriksaanAktif = (clone $pemeriksaanBaseQuery)->where('status', 'aktif')->count();
        $pemeriksaanSelesai = (clone $pemeriksaanBaseQuery)->where('status', 'selesai')->count();

        // Base Query untuk Surat
        $suratBaseQuery = Surat::query();
        if (!$isAdmin) {
            $suratBaseQuery->whereHas('pemeriksaan.users', fn($q) => $q->where('user_id', $userId));
        }

        // 2. Surat Stats (Global)
        $totalSurat = (clone $suratBaseQuery)->count();
        $suratAktif = (clone $suratBaseQuery)->where('status', 'aktif')->count();

        // 3. OPD Progress (Hanya pada Pemeriksaan Aktif)
        $opdBaseQuery = PermintaanOpd::whereHas('permintaan.surat', function($q) use ($isAdmin, $userId) {
            $q->whereNull('deleted_at')
              ->whereHas('pemeriksaan', function($pq) use ($isAdmin, $userId) {
                  $pq->where('status', 'aktif');
                  if (!$isAdmin) {
                      $pq->whereHas('users', fn($uq) => $uq->where('user_id', $userId));
                  }
              });
        });
        $totalOpd = (clone $opdBaseQuery)->count();
        $opdBelum = (clone $opdBaseQuery)->where('status', 'belum')->count();
        $opdProses = (clone $opdBaseQuery)->where('status', 'proses')->count();
        $opdSelesai = (clone $opdBaseQuery)->where('status', 'selesai')->count();
        $progressPersen = $totalOpd > 0 ? round((($opdSelesai + $opdProses) / $totalOpd) * 100) : 0;

        $dokumenQuery = Dokumen::where(function ($q) {
            $q->whereHas('permintaan.surat', fn($sq) => $sq->whereNull('deleted_at'))
              ->orWhereHas('permintaanOpd.permintaan.surat', fn($sq) => $sq->whereNull('deleted_at'));
        });
        if (!$isAdmin) {
            $dokumenQuery->whereHas('permintaan.surat.pemeriksaan.users', fn($q) => $q->where('user_id', $userId));
        }
        $totalDokumen = $dokumenQuery->count();

        // 4. Critical Alerts
        $suratDeadlineDekat = (clone $suratBaseQuery)->whereNotNull('deadline')
            ->where('deadline', '>=', now())
            ->where('deadline', '<=', now()->addDays(14))
            ->where('status', '!=', 'selesai')
            ->count();
            
        $suratOverdue = (clone $suratBaseQuery)->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->where('status', '!=', 'selesai')
            ->count();

        // 5. Ranking OPD (Reward & Punishment) based on Active Pemeriksaan
        $opdRankings = PermintaanOpd::whereHas('permintaan.surat.pemeriksaan', function($q) use ($isAdmin, $userId) {
                $q->where('status', 'aktif');
                if (!$isAdmin) {
                    $q->whereHas('users', fn($uq) => $uq->where('user_id', $userId));
                }
            })
            ->selectRaw('opd, count(*) as total, sum(case when status = "selesai" then 1 else 0 end) as selesai, sum(case when status = "proses" then 1 else 0 end) as proses, sum(case when status = "belum" then 1 else 0 end) as belum')
            ->groupBy('opd')
            ->having('total', '>', 0)
            ->get()
            ->map(function($item) {
                $item->persentase = $item->total > 0 ? round(($item->selesai / $item->total) * 100) : 0;
                return $item;
            });
            
        // Top 5 OPD (Performa Terbaik)
        $topOpd = $opdRankings->sortByDesc(function($item) {
            return $item->persentase * 1000 + $item->selesai;
        })->take(5)->values();
        
        // Perlu Perhatian: Filter out OPDs with 100% completion!
        $bottomOpd = $opdRankings->filter(function($item) {
            return $item->persentase < 100;
        })->sortBy(function($item) {
            return $item->persentase * 1000 - $item->total;
        })->take(5)->values();

        // 6. Progres per Pemeriksaan Aktif
        $pemeriksaanProgress = (clone $pemeriksaanBaseQuery)->where('status', 'aktif')->get()->map(function($p) {
            $opdQuery = PermintaanOpd::whereHas('permintaan.surat', function($q) use ($p) {
                $q->where('pemeriksaan_id', $p->id)->whereNull('deleted_at');
            });
            
            $total = (clone $opdQuery)->count();
            $selesai = (clone $opdQuery)->whereIn('status', ['selesai', 'proses'])->count();
            $persentase = $total > 0 ? round(($selesai / $total) * 100) : 0;
            
            return (object)[
                'nama' => $p->nama,
                'total' => $total,
                'selesai' => $selesai,
                'persentase' => $persentase
            ];
        })->sortByDesc('persentase')->values();

        // 7. Recent Activity & Deadline list
        $aktivitasTerbaruQuery = Dokumen::with([
            'permintaanOpd',
            'permintaan.surat.pemeriksaan',
            'permintaan.judulPermintaan',
            'uploader',
        ])->where(function ($q) {
            $q->whereHas('permintaan.surat', fn($sq) => $sq->whereNull('deleted_at'))
              ->orWhereHas('permintaanOpd.permintaan.surat', fn($sq) => $sq->whereNull('deleted_at'));
        });
        
        if (!$isAdmin) {
            $aktivitasTerbaruQuery->whereHas('permintaan.surat.pemeriksaan.users', fn($q) => $q->where('user_id', $userId));
        }
        $aktivitasTerbaru = $aktivitasTerbaruQuery->orderByDesc('created_at')->limit(8)->get();

        $suratDeadline = (clone $suratBaseQuery)->with('pemeriksaan')->whereNotNull('deadline')
            ->where('status', '!=', 'selesai')
            ->orderBy('deadline')
            ->limit(6)
            ->get();

        $suratTerbaru = (clone $suratBaseQuery)->orderByDesc('tanggal_terima')->limit(5)->get();

        $opdProgress = PermintaanOpd::whereHas('permintaan.surat', function($q) use ($isAdmin, $userId) {
            $q->whereNull('deleted_at');
            if (!$isAdmin) {
                $q->whereHas('pemeriksaan.users', fn($uq) => $uq->where('user_id', $userId));
            }
        })
            ->selectRaw('opd, count(*) as total, sum(case when status in ("selesai","proses") then 1 else 0 end) as selesai')
            ->groupBy('opd')
            ->having('total', '>', 0)
            ->orderByRaw('(selesai/total) desc, total desc')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'totalPemeriksaan', 'pemeriksaanAktif', 'pemeriksaanSelesai',
            'totalSurat', 'suratAktif',
            'totalOpd', 'opdBelum', 'opdProses', 'opdSelesai', 'progressPersen', 'totalDokumen',
            'suratDeadlineDekat', 'suratOverdue',
            'topOpd', 'bottomOpd', 'pemeriksaanProgress',
            'aktivitasTerbaru', 'suratDeadline', 'suratTerbaru', 'opdProgress'
        ));
    }
}
