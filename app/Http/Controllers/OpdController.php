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

        $penandatanganNama = \App\Models\Setting::get('penandatangan_nama', '');
        $penandatanganJabatan = \App\Models\Setting::get('penandatangan_jabatan', 'Inspektur Kabupaten Puncak Jaya');
        $penandatanganNip = \App\Models\Setting::get('penandatangan_nip', '');

        return view('laporan.index', compact('pemeriksaanList', 'suratList', 'masterOpds', 'penandatanganNama', 'penandatanganJabatan', 'penandatanganNip'));
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
        try {
            $dokumens = \App\Models\Dokumen::with(['permintaan.surat.pemeriksaan', 'permintaanOpd'])
                ->orderByDesc('created_at')
                ->get();
                
            $data = $dokumens->map(function($doc) {
                $ext = pathinfo((string)$doc->nama_file, PATHINFO_EXTENSION);
                if (!$ext) $ext = 'unknown';

                return [
                    'id' => $doc->id,
                    'nama_file' => $doc->nama_file,
                    'ext' => strtolower($ext),
                    'opd' => $doc->permintaanOpd ? $doc->permintaanOpd->opd : '-',
                    'ukuran' => $doc->ukuran_format,
                    'tanggal' => $doc->created_at ? $doc->created_at->format('d M Y, H:i') : '-',
                    'surat' => ($doc->permintaan && $doc->permintaan->surat) ? $doc->permintaan->surat->nomor_surat : '-',
                    'pemeriksaan' => ($doc->permintaan && $doc->permintaan->surat && $doc->permintaan->surat->pemeriksaan) ? $doc->permintaan->surat->pemeriksaan->nama . ' ' . $doc->permintaan->surat->pemeriksaan->tahun : '-',
                    'judul_permintaan' => ($doc->permintaan && $doc->permintaan->judul_permintaan) ? $doc->permintaan->judul_permintaan : '-',
                ];
            });
            
            return response()->json($data);
        } catch (\Throwable $e) {
            \Log::error('Arsip Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function buildEksekutifData(Request $request): array
    {
        $validated = $request->validate([
            'judul_laporan'          => 'required|string|max:255',
            'surat_ids'              => 'required|array|min:1',
            'surat_ids.*'            => 'required|integer|exists:surat,id',
            'pemeriksaan_id'         => 'nullable',
            'opds'                   => 'nullable|array',
            'opds.*'                 => 'string',
            'filter_kepatuhan'       => 'nullable|string|in:semua,belum_lengkap,selesai_100',
            'penandatangan_nama'    => 'nullable|string|max:255',
            'penandatangan_jabatan' => 'nullable|string|max:255',
            'penandatangan_nip'     => 'nullable|string|max:255',
        ]);

        if (isset($validated['penandatangan_nama'])) {
            \App\Models\Setting::set('penandatangan_nama', (string) $validated['penandatangan_nama']);
        }
        if (isset($validated['penandatangan_jabatan'])) {
            \App\Models\Setting::set('penandatangan_jabatan', (string) $validated['penandatangan_jabatan']);
        }
        if (isset($validated['penandatangan_nip'])) {
            \App\Models\Setting::set('penandatangan_nip', (string) $validated['penandatangan_nip']);
        }

        $filterSuratIds = collect($validated['surat_ids'])->map(fn($id) => (int) $id)->unique()->values()->all();
        $filterPemeriksaanId = $validated['pemeriksaan_id'] ?? null;
        $filterOpds = $validated['opds'] ?? [];
        $filterKepatuhan = $validated['filter_kepatuhan'] ?? 'semua';

        $suratList = \App\Models\Surat::whereIn('id', $filterSuratIds)->orderBy('tanggal_terima', 'desc')->get();
        $permintaanDataList = \App\Models\PermintaanData::with(['judulPermintaan', 'surat'])
            ->whereIn('surat_id', $filterSuratIds)
            ->orderBy('id', 'asc')
            ->get();

        // Group columns by Judul Permintaan to prevent 1000 columns overflow
        $judulGroups = [];
        foreach ($permintaanDataList as $item) {
            $jTitle = $item->judulPermintaan ? $item->judulPermintaan->judul_permintaan : ($item->judul_permintaan ?: 'Permintaan Data');
            if (!isset($judulGroups[$jTitle])) {
                $judulGroups[$jTitle] = [
                    'title' => $jTitle,
                    'item_ids' => [],
                ];
            }
            $judulGroups[$jTitle]['item_ids'][] = $item->id;
        }

        $daftarOpd = PermintaanData::daftarOpd();
        if (!empty($filterOpds)) {
            $daftarOpd = array_values(array_intersect($daftarOpd, $filterOpds));
        }

        $allPermintaanOpd = PermintaanOpd::with(['dokumen', 'permintaan'])
            ->whereHas('permintaan', fn($q) => $q->whereIn('surat_id', $filterSuratIds))
            ->get()
            ->groupBy('opd');

        $opdRows = [];
        $totalOpd = 0;
        $opdLengkapCount = 0;
        $opdProsesCount = 0;
        $opdBelumCount = 0;
        $totalCellCount = 0;
        $selesaiCellCount = 0;

        foreach ($daftarOpd as $opdNama) {
            $itemsForOpd = $allPermintaanOpd->get($opdNama, collect());
            if ($itemsForOpd->isEmpty()) continue;

            $itemMap = [];
            $selesai = 0;
            $proses = 0;
            $belum = 0;
            $total = $itemsForOpd->count();

            foreach ($itemsForOpd as $po) {
                $itemMap[$po->permintaan_id] = $po;
                if ($po->status === 'selesai') $selesai++;
                elseif ($po->status === 'proses') $proses++;
                else $belum++;
            }

            $pct = $total > 0 ? round(($selesai / $total) * 100) : 0;

            if ($filterKepatuhan === 'belum_lengkap' && $pct >= 100) continue;
            if ($filterKepatuhan === 'selesai_100' && $pct < 100) continue;

            $totalOpd++;
            if ($pct >= 100) $opdLengkapCount++;
            elseif ($selesai > 0 || $proses > 0) $opdProsesCount++;
            else $opdBelumCount++;

            $totalCellCount += $total;
            $selesaiCellCount += $selesai;

            // Calculate status per Judul Group
            $groupStats = [];
            foreach ($judulGroups as $jTitle => $gInfo) {
                $gTotal = 0;
                $gSelesai = 0;
                $gProses = 0;
                $gBelum = 0;
                $gDocs = 0;

                foreach ($gInfo['item_ids'] as $pId) {
                    if (isset($itemMap[$pId])) {
                        $po = $itemMap[$pId];
                        $gTotal++;
                        if ($po->status === 'selesai') $gSelesai++;
                        elseif ($po->status === 'proses') $gProses++;
                        else $gBelum++;
                        $gDocs += $po->dokumen->count();
                    }
                }

                $gPct = $gTotal > 0 ? round(($gSelesai / $gTotal) * 100) : 0;
                $groupStats[$jTitle] = [
                    'total'   => $gTotal,
                    'selesai' => $gSelesai,
                    'proses'  => $gProses,
                    'belum'   => $gBelum,
                    'docs'    => $gDocs,
                    'pct'     => $gPct,
                ];
            }

            // Categorize detailed item titles per OPD (Selesai, Proses, Belum)
            $detailItems = [
                'selesai' => [],
                'proses'  => [],
                'belum'   => [],
            ];
            foreach ($permintaanDataList as $item) {
                $po = $itemMap[$item->id] ?? null;
                if ($po) {
                    $docCount = $po->dokumen->count();
                    $itemLabel = $item->judul_permintaan . ($docCount > 0 ? " ($docCount berkas)" : "");
                    if ($po->status === 'selesai') {
                        $detailItems['selesai'][] = $itemLabel;
                    } elseif ($po->status === 'proses') {
                        $detailItems['proses'][] = $itemLabel;
                    } else {
                        $detailItems['belum'][] = $item->judul_permintaan;
                    }
                }
            }

            $opdRows[] = [
                'opd_nama'     => $opdNama,
                'items'        => $itemMap,
                'groupStats'   => $groupStats,
                'detailItems'  => $detailItems,
                'total'        => $total,
                'selesai'      => $selesai,
                'proses'       => $proses,
                'belum'        => $belum,
                'progress_pct' => $pct,
            ];
        }

        usort($opdRows, function ($a, $b) {
            if ($a['progress_pct'] === $b['progress_pct']) {
                return strcmp($a['opd_nama'], $b['opd_nama']);
            }
            return $b['progress_pct'] <=> $a['progress_pct'];
        });

        $pemeriksaan = ($filterPemeriksaanId && $filterPemeriksaanId !== 'null')
            ? \App\Models\Pemeriksaan::find($filterPemeriksaanId)
            : null;
            
        $overallPct = $totalCellCount > 0 ? round(($selesaiCellCount / $totalCellCount) * 100) : 0;

        // Base64 encode logos for formal PDF Kop
        $logoLeftPath = public_path('images/logo-puncak-jaya.png');
        $logoRightPath = public_path('images/logo-inspektorat.png');
        $logoLeftBase64 = file_exists($logoLeftPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoLeftPath)) : null;
        $logoRightBase64 = file_exists($logoRightPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoRightPath)) : null;

        return [
            'validated' => $validated,
            'pemeriksaan' => $pemeriksaan,
            'suratList' => $suratList,
            'permintaanDataList' => $permintaanDataList,
            'judulGroups' => $judulGroups,
            'opdRows' => $opdRows,
            'summary' => [
                'total_opd' => $totalOpd,
                'opd_lengkap' => $opdLengkapCount,
                'opd_proses' => $opdProsesCount,
                'opd_belum' => $opdBelumCount,
                'overall_pct' => $overallPct,
            ],
            'logoLeftBase64' => $logoLeftBase64,
            'logoRightBase64' => $logoRightBase64,
            'generatedAt' => now()->setTimezone('Asia/Jayapura'),
        ];
    }

    public function exportEksekutifPdf(Request $request)
    {
        @ini_set('memory_limit', '1024M');
        @set_time_limit(300);

        $data = $this->buildEksekutifData($request);
        $filename = 'laporan-eksekutif-bpk-' . $data['generatedAt']->format('Ymd-His') . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf_eksekutif', $data)
            ->setPaper('a4', 'landscape');

        gc_collect_cycles();

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }

    public function exportEksekutifExcel(Request $request)
    {
        @ini_set('memory_limit', '1024M');
        @set_time_limit(300);

        $data = $this->buildEksekutifData($request);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Sheet 1: Matriks Pemenuhan
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Matriks Pemenuhan');

        $sheet->setCellValue('A1', strtoupper($data['validated']['judul_laporan']));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        $sheet->setCellValue('A3', 'Pemeriksaan: ' . ($data['pemeriksaan'] ? $data['pemeriksaan']->nama : 'Semua Pemeriksaan'));
        $sheet->setCellValue('A4', 'Tanggal Cetak: ' . $data['generatedAt']->format('d/m/Y H:i') . ' WIT');
        $sheet->setCellValue('A5', 'Total Entitas: ' . $data['summary']['total_opd'] . ' | Lengkap (100%): ' . $data['summary']['opd_lengkap'] . ' | Total Kepatuhan: ' . $data['summary']['overall_pct'] . '%');

        $row = 7;
        $sheet->setCellValue('A' . $row, 'NO');
        $sheet->setCellValue('B' . $row, 'ENTITAS / OPD');
        $sheet->setCellValue('C' . $row, 'KEPATUHAN (%)');
        $sheet->setCellValue('D' . $row, 'SELESAI');
        $sheet->setCellValue('E' . $row, 'PROSES');
        $sheet->setCellValue('F' . $row, 'BELUM');

        $colIdx = 7;
        foreach ($data['permintaanDataList'] as $item) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue($colLetter . $row, $item->judul_permintaan);
            $colIdx++;
        }

        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx - 1);
        $headerRange = 'A' . $row . ':' . $lastColLetter . $row;
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('0B192C');

        $no = 1;
        $row++;
        foreach ($data['opdRows'] as $opd) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $opd['opd_nama']);
            $sheet->setCellValue('C' . $row, $opd['progress_pct'] . '%');
            $sheet->setCellValue('D' . $row, $opd['selesai']);
            $sheet->setCellValue('E' . $row, $opd['proses']);
            $sheet->setCellValue('F' . $row, $opd['belum']);

            $cIdx = 7;
            foreach ($data['permintaanDataList'] as $item) {
                $cLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx);
                $po = $opd['items'][$item->id] ?? null;
                if ($po) {
                    $docCount = $po->dokumen->count();
                    $cellText = strtoupper($po->status) . ($docCount > 0 ? " ($docCount)" : "");
                    $sheet->setCellValue($cLetter . $row, $cellText);

                    $cellStyle = $sheet->getStyle($cLetter . $row);
                    if ($po->status === 'selesai') {
                        $cellStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('DCFCE7');
                        $cellStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('15803D'));
                    } elseif ($po->status === 'proses') {
                        $cellStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FEF3C7');
                        $cellStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('B45309'));
                    } else {
                        $cellStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FEE2E2');
                        $cellStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('DC2626'));
                    }
                } else {
                    $sheet->setCellValue($cLetter . $row, '-');
                }
                $cIdx++;
            }
            $row++;
        }

        for ($i = 1; $i <= $colIdx - 1; $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Sheet 2: Rincian Status per OPD
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Rincian Status per OPD');
        $sheet2->setCellValue('A1', 'NO');
        $sheet2->setCellValue('B1', 'ENTITAS / OPD');
        $sheet2->setCellValue('C1', 'JUDUL PERMINTAAN DATA');
        $sheet2->setCellValue('D1', 'STATUS');
        $sheet2->setCellValue('E1', 'JUMLAH FILE');
        $sheet2->getStyle('A1:E1')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet2->getStyle('A1:E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('0B192C');

        $r2 = 2;
        $no2 = 1;
        foreach ($data['opdRows'] as $opd) {
            foreach ($data['permintaanDataList'] as $item) {
                $po = $opd['items'][$item->id] ?? null;
                $statusStr = $po ? strtoupper($po->status) : 'BELUM';
                $docCount = $po ? $po->dokumen->count() : 0;

                $sheet2->setCellValue('A' . $r2, $no2++);
                $sheet2->setCellValue('B' . $r2, $opd['opd_nama']);
                $sheet2->setCellValue('C' . $r2, $item->judul_permintaan);
                $sheet2->setCellValue('D' . $r2, $statusStr);
                $sheet2->setCellValue('E' . $r2, $docCount);

                $cellStyle = $sheet2->getStyle('D' . $r2);
                if ($statusStr === 'SELESAI') {
                    $cellStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('DCFCE7');
                    $cellStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('15803D'));
                } elseif ($statusStr === 'PROSES') {
                    $cellStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FEF3C7');
                    $cellStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('B45309'));
                } else {
                    $cellStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FEE2E2');
                    $cellStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('DC2626'));
                }
                $r2++;
            }
        }
        foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        // Sheet 3: Rincian File Dokumen
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Rincian File Dokumen');
        $sheet3->setCellValue('A1', 'NO');
        $sheet3->setCellValue('B1', 'OPD');
        $sheet3->setCellValue('C1', 'JUDUL PERMINTAAN');
        $sheet3->setCellValue('D1', 'NAMA FILE');
        $sheet3->setCellValue('E1', 'TANGGAL UPLOAD');
        $sheet3->getStyle('A1:E1')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet3->getStyle('A1:E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('0B192C');

        $r3 = 2;
        $no3 = 1;
        foreach ($data['opdRows'] as $opd) {
            foreach ($opd['items'] as $po) {
                foreach ($po->dokumen as $dok) {
                    $sheet3->setCellValue('A' . $r3, $no3++);
                    $sheet3->setCellValue('B' . $r3, $opd['opd_nama']);
                    $sheet3->setCellValue('C' . $r3, $po->permintaan->judul_permintaan ?? '-');
                    $sheet3->setCellValue('D' . $r3, $dok->nama_file);
                    $sheet3->setCellValue('E' . $r3, $dok->created_at ? $dok->created_at->format('d/m/Y H:i') : '-');
                    $r3++;
                }
            }
        }
        foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
            $sheet3->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'matriks-eksekutif-bpk-' . $data['generatedAt']->format('Ymd-His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
