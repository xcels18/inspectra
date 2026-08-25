<?php

namespace App\Http\Controllers;

use App\Models\PermintaanData;
use App\Models\PermintaanOpd;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OpdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $filterSuratIds = collect((array) $request->get('surat_ids', []))
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
        $filterPemeriksaanId = $request->get('pemeriksaan_id');

        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $userId = $user->id;

        // Auto-select the first active pemeriksaan if none specified
        if (!$filterPemeriksaanId) {
            $activePemeriksaanQuery = \App\Models\Pemeriksaan::where('status', 'aktif')
                ->orderByDesc('created_at');
            if (!$isAdmin) {
                $activePemeriksaanQuery->whereHas('users', fn($q) => $q->where('user_id', $userId));
            }
            $activePemeriksaan = $activePemeriksaanQuery->first();
            if ($activePemeriksaan) {
                $filterPemeriksaanId = $activePemeriksaan->id;
                // Redirect to include pemeriksaan_id in URL so the dropdown shows as selected
                return redirect()->route('opd.index', array_merge($request->except('pemeriksaan_id'), ['pemeriksaan_id' => $filterPemeriksaanId]));
            }
        }

        $stats = $this->buildStats($search, $filterSuratIds, $filterPemeriksaanId);

        $suratQuery = \App\Models\Surat::orderByDesc('tanggal_terima');
        if (!$isAdmin) {
            $suratQuery->where(function ($q) use ($userId) {
                $q->whereNull('pemeriksaan_id')
                  ->orWhereHas('pemeriksaan.users', fn($uq) => $uq->where('user_id', $userId));
            });
        }
        
        if ($filterPemeriksaanId) {
            if ($filterPemeriksaanId === 'null') {
                $suratQuery->whereNull('pemeriksaan_id');
            } else {
                $suratQuery->where('pemeriksaan_id', $filterPemeriksaanId);
            }
        }
        $suratList = $suratQuery->get();

        $pemeriksaanQuery = \App\Models\Pemeriksaan::orderByDesc('created_at');
        if (!$isAdmin) {
            $pemeriksaanQuery->whereHas('users', fn($q) => $q->where('user_id', $userId));
        }
        $pemeriksaanList = $pemeriksaanQuery->get();

        return view('opd.index', compact('stats', 'suratList', 'search', 'filterSuratIds', 'filterPemeriksaanId', 'pemeriksaanList'));
    }

    public function laporanIndex(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $userId = $user->id;

        $pemeriksaanQuery = \App\Models\Pemeriksaan::orderByDesc('created_at');
        if (!$isAdmin) {
            $pemeriksaanQuery->whereHas('users', fn($q) => $q->where('user_id', $userId));
        }
        $pemeriksaanList = $pemeriksaanQuery->get();

        $suratQuery = \App\Models\Surat::with('pemeriksaan')->orderByDesc('tanggal_terima');
        if (!$isAdmin) {
            $suratQuery->where(function ($q) use ($userId) {
                $q->whereNull('pemeriksaan_id')
                  ->orWhereHas('pemeriksaan.users', fn($uq) => $uq->where('user_id', $userId));
            });
        }
        $suratList = $suratQuery->get();

        $masterOpds = \App\Models\MasterOpd::orderBy('nama')->get()->groupBy('kategori');

        return view('laporan.index', compact('pemeriksaanList', 'suratList', 'masterOpds'));
    }

    public function print(Request $request)
    {
        $validated = $request->validate([
            'judul_laporan' => 'required|string|max:255',
            'surat_ids' => 'required|array|min:1',
            'surat_ids.*' => 'required|integer|exists:surat,id',
            'search' => 'nullable|string|max:255',
            'detail' => 'nullable|boolean',
            'pemeriksaan_id' => 'nullable|integer|exists:pemeriksaan,id',
            'opds' => 'nullable|array',
            'opds.*' => 'string',
        ], [
            'judul_laporan.required' => 'Judul laporan wajib diisi.',
            'surat_ids.required' => 'Pilih minimal 1 nomor surat untuk mencetak laporan.',
            'surat_ids.array' => 'Format pilihan surat tidak valid.',
            'surat_ids.min' => 'Pilih minimal 1 nomor surat untuk mencetak laporan.',
        ]);

        $search = trim((string) ($validated['search'] ?? ''));
        $filterSuratIds = collect($validated['surat_ids'])->map(fn($id) => (int) $id)->unique()->values()->all();
        $filterPemeriksaanId = $validated['pemeriksaan_id'] ?? null;
        $filterOpds = $validated['opds'] ?? [];

        $stats = $this->buildStats($search, $filterSuratIds, $filterPemeriksaanId, $filterOpds);
        $user = auth()->user();
        $suratQuery = \App\Models\Surat::whereIn('id', $filterSuratIds)
            ->orderByDesc('tanggal_terima');
        if (!$user->isAdmin()) {
            $suratQuery->where(function ($q) use ($user) {
                $q->whereNull('pemeriksaan_id')
                  ->orWhereHas('pemeriksaan.users', fn($uq) => $uq->where('user_id', $user->id));
            });
        }
        $selectedSurat = $suratQuery->get(['id', 'nomor_surat', 'perihal', 'tanggal_surat']);
        $showDetail = (bool) ($validated['detail'] ?? false);

        $detailByStatus = $showDetail ? $this->buildDetailByStatus($search, $filterSuratIds, $filterPemeriksaanId, $filterOpds) : [
            'belum' => [],
            'proses' => [],
            'selesai' => [],
        ];

        $stats = array_values(array_filter($stats, fn($row) => $row['total'] > 0));

        $pemeriksaan = $filterPemeriksaanId
            ? \App\Models\Pemeriksaan::find($filterPemeriksaanId)
            : null;

        $generatedAt = now()->setTimezone('Asia/Jayapura');
        $filename = 'monitoring-opd-' . $generatedAt->format('Ymd-His') . '.pdf';

        $pdf = Pdf::loadView('opd.print', [
            'judulLaporan' => $validated['judul_laporan'],
            'stats' => $stats,
            'selectedSurat' => $selectedSurat,
            'search' => $search,
            'generatedAt' => $generatedAt,
            'showDetail' => $showDetail,
            'detailByStatus' => $detailByStatus,
            'pemeriksaan' => $pemeriksaan,
        ])->setPaper('a4', 'landscape');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'Pragma' => 'public',
        ]);
    }

    private function buildStats(string $search = '', array $filterSuratIds = [], $filterPemeriksaanId = null, array $filterOpds = []): array
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $userId = $user->id;
        $daftarOpd = PermintaanData::daftarOpd();

        if (!empty($filterOpds)) {
            $daftarOpd = array_values(array_intersect($daftarOpd, $filterOpds));
        }

        if ($search !== '') {
            $daftarOpd = array_values(array_filter($daftarOpd, function ($opd) use ($search) {
                return stripos($opd, $search) !== false;
            }));
        }

        $query = PermintaanOpd::with('permintaan')
            ->whereHas('permintaan.surat', function($q) use ($isAdmin, $userId) {
                $q->whereNull('deleted_at');
                if (!$isAdmin) {
                    $q->where(function ($sq) use ($userId) {
                        $sq->whereNull('pemeriksaan_id')
                           ->orWhereHas('pemeriksaan.users', fn($uq) => $uq->where('user_id', $userId));
                    });
                }
            });

        if (!empty($filterSuratIds)) {
            $query->whereHas('permintaan', fn($q) => $q->whereIn('surat_id', $filterSuratIds));
        }

        if ($filterPemeriksaanId) {
            if ($filterPemeriksaanId === 'null') {
                $query->whereHas('permintaan.surat', fn($q) => $q->whereNull('pemeriksaan_id'));
            } else {
                $query->whereHas('permintaan.surat', fn($q) => $q->where('pemeriksaan_id', $filterPemeriksaanId));
            }
        }

        $allRows = $query->get();

        $stats = [];
        foreach ($daftarOpd as $opd) {
            $filtered = $allRows->where('opd', $opd);

            $total = $filtered->count();
            if ($total === 0) {
                continue;
            }
            
            $belum = $filtered->where('status', 'belum')->count();
            $proses = $filtered->where('status', 'proses')->count();
            $selesai = $filtered->where('status', 'selesai')->count();
            $progress = $total > 0 ? round((($proses + $selesai) / $total) * 100, 2) : 0;

            $stats[] = [
                'opd' => $opd,
                'total' => $total,
                'belum' => $belum,
                'proses' => $proses,
                'selesai' => $selesai,
                'progress' => $progress,
            ];
        }

        usort($stats, function ($a, $b) {
            // OPD dengan data diutamakan, lalu urutkan berdasarkan progress
            $aHasData = $a['total'] > 0 ? 1 : 0;
            $bHasData = $b['total'] > 0 ? 1 : 0;
            if ($aHasData !== $bHasData) return $bHasData <=> $aHasData;
            if ($a['progress'] === $b['progress']) {
                return $b['total'] <=> $a['total'];
            }
            return $b['progress'] <=> $a['progress'];
        });

        return $stats;
    }

    private function buildDetailByStatus(string $search = '', array $filterSuratIds = [], $filterPemeriksaanId = null, array $filterOpds = []): array
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $userId = $user->id;
        $allowedOpd = PermintaanData::daftarOpd();
        
        if (!empty($filterOpds)) {
            $allowedOpd = array_values(array_intersect($allowedOpd, $filterOpds));
        }

        if ($search !== '') {
            $allowedOpd = array_values(array_filter($allowedOpd, function ($opd) use ($search) {
                return stripos($opd, $search) !== false;
            }));
        }

        $query = PermintaanOpd::with(['permintaan.surat', 'permintaan.judulPermintaan'])
            ->whereIn('status', ['belum', 'proses', 'selesai'])
            ->whereHas('permintaan.surat', function($q) use ($isAdmin, $userId) {
                $q->whereNull('deleted_at');
                if (!$isAdmin) {
                    $q->where(function ($sq) use ($userId) {
                        $sq->whereNull('pemeriksaan_id')
                           ->orWhereHas('pemeriksaan.users', fn($uq) => $uq->where('user_id', $userId));
                    });
                }
            });

        if (!empty($filterSuratIds)) {
            $query->whereHas('permintaan', fn($q) => $q->whereIn('surat_id', $filterSuratIds));
        }

        if ($filterPemeriksaanId) {
            if ($filterPemeriksaanId === 'null') {
                $query->whereHas('permintaan.surat', fn($q) => $q->whereNull('pemeriksaan_id'));
            } else {
                $query->whereHas('permintaan.surat', fn($q) => $q->where('pemeriksaan_id', $filterPemeriksaanId));
            }
        }

        if (!empty($allowedOpd)) {
            $query->whereIn('opd', $allowedOpd);
        }

        $rows = $query->get()->sortBy('opd');

        $statusLabel = [
            'selesai' => 'Sudah',
            'proses' => 'Proses',
            'belum' => 'Belum',
        ];

        $result = $rows
            ->groupBy('opd')
            ->map(function ($opdRows, $opdName) use ($statusLabel) {
                $statuses = collect(['selesai', 'proses', 'belum'])->map(function ($statusKey) use ($opdRows, $statusLabel) {
                    $suratGroups = $opdRows->where('status', $statusKey)
                        ->groupBy(function ($row) {
                            return $row->permintaan?->surat?->nomor_surat
                                ?? ('SURAT-ID-' . ($row->permintaan?->surat_id ?? '-'));
                        })
                        ->map(function ($suratRows, $nomorSurat) {
                            $firstPermintaan = $suratRows->first()?->permintaan;
                            $tanggalSurat = $firstPermintaan?->surat?->tanggal_surat
                                ? \Carbon\Carbon::parse($firstPermintaan->surat->tanggal_surat)->format('d-m-Y')
                                : '-';
                            $perihalSurat = $firstPermintaan?->surat?->perihal ?: '-';

                            $items = $suratRows
                                ->map(function ($row) {
                                    $p = $row->permintaan;
                                    if (!$p) return null;

                                    $judul     = trim((string) ($p->judul_permintaan ?? ''));
                                    $deskripsi = trim((string) ($p->deskripsi ?? ''));
                                    $nomorUrut = $p->nomor_urut ?? null;

                                    $text = $judul !== '' ? $judul : ($deskripsi !== '' ? $deskripsi : null);

                                    if ($text === null) {
                                        if ($nomorUrut !== null) {
                                            $text = (string) $nomorUrut;
                                        } else {
                                            return null;
                                        }
                                    }

                                    $textForSort = trim((string) $text);
                                    preg_match('/^\D*(\d{1,5})/u', $textForSort, $m);
                                    $sortKey = isset($m[1]) ? (int) $m[1] : PHP_INT_MAX;

                                    return [
                                        'text' => $text,
                                        'sort_key' => $sortKey,
                                        'sort_text' => mb_strtolower(trim((string) $textForSort)),
                                    ];
                                })
                                ->filter()
                                ->sortBy([
                                    ['sort_key', 'asc'],
                                    ['sort_text', 'asc'],
                                ])
                                ->pluck('text')
                                ->values()
                                ->all();

                            return [
                                'nomor_surat' => $nomorSurat,
                                'tanggal_surat' => $tanggalSurat,
                                'perihal' => $perihalSurat,
                                'items' => $items,
                            ];
                        })
                        ->sortBy('nomor_surat')
                        ->values()
                        ->all();

                    return [
                        'status_key' => $statusKey,
                        'status_label' => $statusLabel[$statusKey],
                        'surat_groups' => $suratGroups,
                    ];
                })->all();

                return [
                    'opd' => $opdName,
                    'statuses' => $statuses,
                ];
            })
            ->values()
            ->all();

        return $result;
    }

    public function show(Request $request, string $opd)
    {
        $opdNama = urldecode($opd);
        $filterSurat = $request->get('surat_id');
        $filterPemeriksaan = $request->get('pemeriksaan_id');

        $user = auth()->user();
        
        $pemeriksaanQuery = \App\Models\Pemeriksaan::query();
        if (!$user->isAdmin()) {
            $pemeriksaanQuery->whereHas('users', fn($uq) => $uq->where('user_id', $user->id));
        }
        $pemeriksaanList = $pemeriksaanQuery->orderByDesc('tahun')->orderByDesc('id')->get();

        $query = PermintaanOpd::with(['permintaan.surat', 'permintaan.judulPermintaan', 'dokumen'])
            ->where('opd', $opdNama)
            ->whereHas('permintaan.surat', function($q) use ($user) {
                $q->whereNull('deleted_at');
                if (!$user->isAdmin()) {
                    $q->where(function ($sq) use ($user) {
                        $sq->whereNull('pemeriksaan_id')
                           ->orWhereHas('pemeriksaan.users', fn($uq) => $uq->where('user_id', $user->id));
                    });
                }
            });
            
        if ($filterPemeriksaan) {
            $query->whereHas('permintaan.surat', fn($q) => $q->where('pemeriksaan_id', $filterPemeriksaan));
        }

        if ($filterSurat) {
            $query->whereHas('permintaan', fn($q) => $q->where('surat_id', $filterSurat));
        }

        $rows = $query->get()->sortBy([
            fn($r) => $r->permintaan->surat?->nomor_surat ?? '',
            fn($r) => $r->permintaan->judulPermintaan?->nomor_urut ?? 0,
            fn($r) => $r->permintaan->nomor_urut ?? 0,
        ]);

        $groupedBySurat = $rows->groupBy(fn($r) => $r->permintaan->surat_id)->sortKeys();
        
        $suratQuery = \App\Models\Surat::orderByDesc('tanggal_terima');
        if (!$user->isAdmin()) {
            $suratQuery->where(function ($q) use ($user) {
                $q->whereNull('pemeriksaan_id')
                  ->orWhereHas('pemeriksaan.users', fn($uq) => $uq->where('user_id', $user->id));
            });
        }
        
        if ($filterPemeriksaan) {
            $suratQuery->where('pemeriksaan_id', $filterPemeriksaan);
        }
        $suratList = $suratQuery->get();

        return view('opd.show', compact('opdNama', 'rows', 'groupedBySurat', 'suratList', 'filterSurat', 'pemeriksaanList', 'filterPemeriksaan'));
    }

    public function arsip(string $opd)
    {
        $opdNama = urldecode($opd);
        
        $dokumens = \App\Models\Dokumen::with(['permintaan.surat.pemeriksaan'])
            ->whereHas('permintaanOpd', function($q) use ($opdNama) {
                $q->where('opd', $opdNama);
            })
            ->orderByDesc('created_at')
            ->get();
            
        $data = $dokumens->map(function($doc) {
            return [
                'id' => $doc->id,
                'nama_file' => $doc->nama_file,
                'ukuran' => $doc->ukuran_format,
                'tanggal' => $doc->created_at->format('d/m/Y'),
                'surat' => $doc->permintaan->surat ? $doc->permintaan->surat->nomor_surat : '-',
                'pemeriksaan' => ($doc->permintaan->surat && $doc->permintaan->surat->pemeriksaan) ? $doc->permintaan->surat->pemeriksaan->nama . ' ' . $doc->permintaan->surat->pemeriksaan->tahun : '-',
            ];
        });
        
        return response()->json($data);
    }
}
