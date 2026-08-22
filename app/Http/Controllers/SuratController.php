<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\User;
use App\Models\JudulPermintaan;
use App\Models\PermintaanData;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SuratController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Surat::with(['pembuat', 'pemeriksaan', 'pemeriksaan.users', 'judulPermintaan.permintaanData.permintaanOpd']);

        $user = auth()->user();
        if (!$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('pemeriksaan_id')
                  ->orWhereHas('pemeriksaan.users', fn($uq) => $uq->where('user_id', $user->id));
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        $status = $request->input('status');
        if ($status === null) {
            $status = 'aktif';
        }
        
        if ($status !== 'semua') {
            $query->where('status', $status);
            // Replace status in request so that the view receives the updated default
            $request->merge(['status' => $status]);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }

        if ($request->filled('pemeriksaan_id')) {
            if ($request->pemeriksaan_id === 'null') {
                $query->whereNull('pemeriksaan_id');
            } else {
                $query->where('pemeriksaan_id', $request->pemeriksaan_id);
            }
        }

        $suratList = $query->orderByDesc('nomor_surat')->paginate(15)->withQueryString();
        $tahunList = Surat::select('tahun_anggaran')->distinct()->orderByDesc('tahun_anggaran')->pluck('tahun_anggaran');
        $pemeriksaanQuery = Pemeriksaan::orderBy('created_at', 'desc');
        if (!$user->isAdmin()) {
            $pemeriksaanQuery->whereHas('users', fn($q) => $q->where('user_id', $user->id));
        }
        $pemeriksaanList = $pemeriksaanQuery->get();

        return view('surat.index', compact('suratList', 'tahunList', 'pemeriksaanList'));
    }

    public function create()
    {
        $user = auth()->user();
        $pemeriksaanQuery = Pemeriksaan::orderBy('created_at', 'desc');
        if (!$user->isAdmin()) {
            $pemeriksaanQuery->whereHas('users', fn($q) => $q->where('user_id', $user->id));
        }
        $pemeriksaans = $pemeriksaanQuery->get();
        return view('surat.create', compact('pemeriksaans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => [
                'required',
                'string',
                Rule::unique('surat', 'nomor_surat')->whereNull('deleted_at'),
            ],
            'tanggal_surat' => 'required|date',
            'tanggal_terima' => 'required|date',
            'perihal' => 'required|string|max:500',
            'keterangan' => 'nullable|string',
            'tahun_anggaran' => 'required|string|max:10',
            'deadline' => 'nullable|date',
            'file_surat' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'import_payload' => 'nullable|string',
            'judul_permintaan' => 'required_without:import_payload|array|min:1',
            'judul_permintaan.*.judul' => 'required|string',
            'judul_permintaan.*.list_data' => 'nullable|array',
            'judul_permintaan.*.list_data.*' => 'nullable|string',
            'judul_permintaan.*.list_opd' => 'nullable|array',
            'judul_permintaan.*.list_opd.*' => 'nullable|array',
            'judul_permintaan.*.list_opd.*.*' => 'nullable|string',
            'pemeriksaan_id' => 'nullable|exists:pemeriksaan,id',
        ]);


        $pemeriksaanId = $validated['pemeriksaan_id'] ?? null;
        if ($pemeriksaanId) {
            if (!auth()->user()->isAdmin()) {
                $hasAccess = \App\Models\Pemeriksaan::where('id', $pemeriksaanId)
                    ->whereHas('users', fn($q) => $q->where('user_id', auth()->id()))
                    ->exists();
                if (!$hasAccess) {
                    abort(403, 'Anda tidak memiliki akses ke pemeriksaan ini.');
                }
            }
        } else {
             if (!auth()->user()->isAdmin()) {
                 abort(403, 'Anda harus memilih pemeriksaan induk.');
             }
        }
        
        $filePath = null;
        if ($request->hasFile('file_surat')) {
            $filePath = $request->file('file_surat')->store('surat', 'local');
        }

        try {
            $surat = Surat::create([
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'tanggal_terima' => $validated['tanggal_terima'],
                'perihal' => $validated['perihal'],
                'keterangan' => $validated['keterangan'] ?? null,
                'tahun_anggaran' => $validated['tahun_anggaran'],
                'deadline' => $validated['deadline'] ?? null,
                'file_surat' => $filePath,
                'status' => 'aktif',
                'created_by' => auth()->id(),
                'pemeriksaan_id' => $validated['pemeriksaan_id'] ?? null,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ((string) $e->getCode() === '23000') {
                throw ValidationException::withMessages([
                    'nomor_surat' => 'Nomor surat sudah digunakan.',
                ]);
            }
            throw $e;
        }

        $importPayloadRaw = $validated['import_payload'] ?? null;
        if (!empty($importPayloadRaw)) {
            $decoded = json_decode($importPayloadRaw, true);
            if (!is_array($decoded) || empty($decoded)) {
                return back()->withErrors(['import_payload' => 'Payload import tidak valid.'])->withInput();
            }

            foreach ($decoded as $judulIndex => $group) {
                $judulText = trim((string) ($group['judul'] ?? ''));
                if ($judulText === '') {
                    continue;
                }

                $judul = JudulPermintaan::create([
                    'surat_id' => $surat->id,
                    'nomor_urut' => $judulIndex + 1,
                    'judul' => $judulText,
                ]);

                $items = $group['items'] ?? [];
                if (!is_array($items)) {
                    continue;
                }

                $nomorUrut = 1;
                foreach ($items as $item) {
                    $listData = trim((string) ($item['list_data'] ?? ''));
                    if ($listData === '') {
                        continue;
                    }

                    $opdList = $item['opd'] ?? ['Semua OPD'];
                    if (!is_array($opdList) || empty($opdList)) {
                        $opdList = ['Semua OPD'];
                    }
                    $opdList = array_values(array_filter(array_map(fn($v) => trim((string) $v), $opdList)));
                    if (empty($opdList)) {
                        $opdList = ['Semua OPD'];
                    }

                    $pd = $surat->permintaanData()->create([
                        'judul_permintaan_id' => $judul->id,
                        'nomor_urut'          => $nomorUrut++,
                        'judul_permintaan'    => $listData,
                        'opd'                 => $opdList,
                        'status'              => 'belum',
                    ]);

                    $syncList = in_array('Semua OPD', $opdList, true)
                        ? \App\Models\PermintaanData::daftarOpd()
                        : $opdList;
                    $pd->syncOpd($syncList);
                }
            }
        } else {
            foreach ($request->judul_permintaan as $judulIndex => $judulItem) {
                $judul = JudulPermintaan::create([
                    'surat_id' => $surat->id,
                    'nomor_urut' => $judulIndex + 1,
                    'judul' => $judulItem['judul'],
                ]);

                if (!empty($judulItem['list_data'])) {
                    $nomorUrut = 1;
                    foreach ($judulItem['list_data'] as $idx => $listData) {
                        if (!empty($listData)) {
                            $opdList = $judulItem['list_opd'][$idx] ?? ['Semua OPD'];
                            if (empty($opdList)) $opdList = ['Semua OPD'];
                            $pd = $surat->permintaanData()->create([
                                'judul_permintaan_id' => $judul->id,
                                'nomor_urut'          => $nomorUrut++,
                                'judul_permintaan'    => $listData,
                                'opd'                 => $opdList,
                                'status'              => 'belum',
                            ]);
                            $syncList = in_array('Semua OPD', $opdList)
                                ? \App\Models\PermintaanData::daftarOpd()
                                : $opdList;
                            $pd->syncOpd($syncList);
                        }
                    }
                }
            }
        }

        return redirect()->route('surat.show', $surat)->with('success', 'Surat berhasil ditambahkan.');
    }

    public function show(Surat $surat)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $surat->pemeriksaan_id) {
            if (!$surat->pemeriksaan->users()->where('user_id', $user->id)->exists()) {
                abort(403, 'Anda tidak memiliki akses ke surat ini.');
            }
        }

        $surat->load([
            'pembuat',
            'judulPermintaan',
        ]);

        $totalOpd = \App\Models\PermintaanOpd::whereHas('permintaan', fn($q) => $q->where('surat_id', $surat->id));
        $opdBelum   = (clone $totalOpd)->where('status', 'belum')->count();
        $opdProses  = (clone $totalOpd)->where('status', 'proses')->count();
        $opdSelesai = (clone $totalOpd)->where('status', 'selesai')->count();
        $opdTotal   = $opdBelum + $opdProses + $opdSelesai;

        $users = User::where('is_active', true)->orderBy('name')->get();

        $isAdmin = auth()->user()->isAdmin();
        $isTimBpk = auth()->user()->isTimBpk();
        $judulInitialItems = collect();
        if ($isTimBpk) {
            $judulIds = $surat->judulPermintaan->pluck('id');
            $judulInitialItems = PermintaanData::with(['permintaanOpd.dokumen'])
                ->whereIn('judul_permintaan_id', $judulIds)
                ->orderBy('nomor_urut')
                ->get()
                ->groupBy('judul_permintaan_id');
        }

        return view('surat.show', compact(
            'surat',
            'users',
            'opdBelum',
            'opdProses',
            'opdSelesai',
            'opdTotal',
            'isAdmin',
            'isTimBpk',
            'judulInitialItems'
        ));
    }

    public function edit(Surat $surat)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $surat->pemeriksaan_id) {
            if (!$surat->pemeriksaan->users()->where('user_id', $user->id)->exists()) {
                abort(403, 'Anda tidak memiliki akses ke surat ini.');
            }
        }

        $pemeriksaanQuery = Pemeriksaan::orderBy('created_at', 'desc');
        if (!$user->isAdmin()) {
            $pemeriksaanQuery->whereHas('users', fn($q) => $q->where('user_id', $user->id));
        }
        $pemeriksaans = $pemeriksaanQuery->get();
        return view('surat.edit', compact('surat', 'pemeriksaans'));
    }

    public function update(Request $request, Surat $surat)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $surat->pemeriksaan_id) {
            if (!$surat->pemeriksaan->users()->where('user_id', $user->id)->exists()) {
                abort(403, 'Anda tidak memiliki akses ke surat ini.');
            }
        }

        $validated = $request->validate([
            'nomor_surat' => [
                'required',
                'string',
                Rule::unique('surat', 'nomor_surat')
                    ->ignore($surat->id)
                    ->whereNull('deleted_at'),
            ],
            'tanggal_surat' => 'required|date',
            'tanggal_terima' => 'required|date',
            'perihal' => 'required|string|max:500',
            'keterangan' => 'nullable|string',
            'tahun_anggaran' => 'required|string|max:10',
            'status' => 'required|in:aktif,selesai,arsip',
            'file_surat' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'pemeriksaan_id' => 'nullable|exists:pemeriksaan,id',
        ]);

        if ($request->hasFile('file_surat')) {
            if ($surat->file_surat) {
                Storage::disk('local')->delete($surat->file_surat);
            }
            $validated['file_surat'] = $request->file('file_surat')->store('surat', 'local');
        }

        $surat->update($validated);

        return redirect()->route('surat.show', $surat)->with('success', 'Surat berhasil diperbarui.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template');

        $cols = ['A', 'B', 'C'];
        $headers = ['Judul Permintaan', 'List Data', 'OPD (pisahkan dengan ;)'];
        foreach ($cols as $i => $col) {
            $cell = $col . '1';
            $sheet->setCellValue($cell, $headers[$i]);
            $sheet->getStyle($cell)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('1e40af');
        }

        $examples = [
            ['Laporan Keuangan', 'Laporan Realisasi Anggaran', 'DINAS PENDIDIKAN DAN KEBUDAYAAN;DINAS KESEHATAN'],
            ['Laporan Keuangan', 'Neraca Daerah', 'Semua OPD'],
            ['Aset Daerah', 'Daftar Aset Tetap', 'BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH ( BPKAD )'],
        ];

        foreach ($examples as $row => $data) {
            foreach ($cols as $i => $col) {
                $sheet->setCellValue($col . ($row + 2), $data[$i]);
            }
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $petunjukSheet = $spreadsheet->createSheet();
        $petunjukSheet->setTitle('Petunjuk');
        $petunjukSheet->setCellValue('A1', 'PETUNJUK PENGISIAN');
        $petunjukSheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $petunjukSheet->setCellValue('A3', '1. Sheet "Template" berisi data yang harus diisi.');
        $petunjukSheet->setCellValue('A4', '2. Kolom "Judul Permintaan": tulis judul/kelompok permintaan data. Baris dengan judul yang sama akan digabung.');
        $petunjukSheet->setCellValue('A5', '3. Kolom "List Data": tulis item data yang diminta (satu item per baris).');
        $petunjukSheet->setCellValue('A6', '4. Kolom "OPD": pisahkan nama OPD dengan titik koma (;). Tulis "Semua OPD" untuk semua OPD.');
        $petunjukSheet->setCellValue('A7', '5. Lihat sheet "Daftar OPD" untuk referensi nama OPD yang tersedia.');
        $petunjukSheet->getColumnDimension('A')->setWidth(90);

        $opdSheet = $spreadsheet->createSheet();
        $opdSheet->setTitle('Daftar OPD');
        $opdSheet->setCellValue('A1', 'No');
        $opdSheet->setCellValue('B1', 'Nama OPD');
        $opdSheet->getStyle('A1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $opdSheet->getStyle('B1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $opdSheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('1e40af');
        $opdSheet->getStyle('B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('1e40af');

        $daftarOpd = PermintaanData::daftarOpd();
        foreach ($daftarOpd as $i => $opd) {
            $opdSheet->setCellValue('A' . ($i + 2), $i + 1);
            $opdSheet->setCellValue('B' . ($i + 2), $opd);
        }

        $opdSheet->getColumnDimension('A')->setWidth(6);
        $opdSheet->getColumnDimension('B')->setAutoSize(true);

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_permintaan_data.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file_excel')->getRealPath());
            $sheet = $spreadsheet->getSheet(0);
            $rows = $sheet->toArray(null, true, true, true);

            $judulGroups = [];
            foreach ($rows as $rowNum => $row) {
                if ($rowNum === 1) continue;
                $judul = trim($row['A'] ?? '');
                $listData = trim($row['B'] ?? '');
                $opdRaw = trim($row['C'] ?? '');

                if ($judul === '' && $listData === '') continue;

                if (!isset($judulGroups[$judul])) {
                    $judulGroups[$judul] = [];
                }

                if ($listData !== '') {
                    $opdList = array_filter(array_map('trim', explode(';', $opdRaw)));
                    if (empty($opdList)) $opdList = ['Semua OPD'];
                    $judulGroups[$judul][] = [
                        'list_data' => $listData,
                        'opd' => $opdList,
                    ];
                }
            }

            if (empty($judulGroups)) {
                return response()->json(['success' => false, 'message' => 'File Excel kosong atau format tidak sesuai.']);
            }

            return response()->json([
                'success' => true,
                'data' => array_values(array_map(function ($judul, $items) {
                    return ['judul' => $judul, 'items' => $items];
                }, array_keys($judulGroups), $judulGroups)),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membaca file: ' . $e->getMessage()]);
        }
    }

    public function exportExcelReport(Surat $surat)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $surat->pemeriksaan_id) {
            if (!$surat->pemeriksaan->users()->where('user_id', $user->id)->exists()) {
                abort(403, 'Anda tidak memiliki akses ke surat ini.');
            }
        }
        $surat->load(['judulPermintaan.permintaanData']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Surat');

        $headers = ['No. Surat', 'Judul Permintaan', 'OPD', 'List Data', 'Status', 'Catatan'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($cols as $i => $col) {
            $cell = $col . '1';
            $sheet->setCellValue($cell, $headers[$i]);
            $sheet->getStyle($cell)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('1e40af');
        }

        $row = 2;
        foreach ($surat->judulPermintaan as $judul) {
            foreach ($judul->permintaanData as $item) {
                $opdRows = $item->permintaanOpd()->get();

                if ($opdRows->isEmpty()) {
                    $sheet->setCellValue('A' . $row, $surat->nomor_surat);
                    $sheet->setCellValue('B' . $row, $judul->judul);
                    $sheet->setCellValue('C' . $row, '-');
                    $sheet->setCellValue('D' . $row, $item->judul_permintaan ?? '-');
                    $sheet->setCellValue('E' . $row, ucfirst((string) ($item->status ?? 'belum')));
                    $sheet->setCellValue('F' . $row, $item->catatan ?? '-');
                    $row++;
                    continue;
                }

                foreach ($opdRows as $opdRow) {
                    $sheet->setCellValue('A' . $row, $surat->nomor_surat);
                    $sheet->setCellValue('B' . $row, $judul->judul);
                    $sheet->setCellValue('C' . $row, $opdRow->opd ?? '-');
                    $sheet->setCellValue('D' . $row, $item->judul_permintaan ?? '-');
                    $sheet->setCellValue('E' . $row, ucfirst((string) ($opdRow->status ?? $item->status ?? 'belum')));
                    $sheet->setCellValue('F' . $row, $opdRow->catatan ?? $item->catatan ?? '-');
                    $row++;
                }
            }
        }

        if ($row === 2) {
            $sheet->setCellValue('A2', $surat->nomor_surat);
            $sheet->setCellValue('B2', '-');
            $sheet->setCellValue('C2', '-');
            $sheet->setCellValue('D2', '-');
            $sheet->setCellValue('E2', '-');
            $sheet->setCellValue('F2', '-');
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filenameSafe = preg_replace('/[^A-Za-z0-9\-_]+/', '_', (string) $surat->nomor_surat);
        $filename = 'laporan_surat_' . trim($filenameSafe, '_') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function downloadFile(Surat $surat)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $surat->pemeriksaan_id) {
            if (!$surat->pemeriksaan->users()->where('user_id', $user->id)->exists()) {
                abort(403, 'Anda tidak memiliki akses ke surat ini.');
            }
        }
        if (!$surat->file_surat || !Storage::disk('local')->exists($surat->file_surat)) {
            abort(404, 'File surat tidak ditemukan.');
        }

        $extension = pathinfo($surat->file_surat, PATHINFO_EXTENSION);
        $filename  = 'Surat_' . preg_replace('/[^A-Za-z0-9\-_]+/', '_', $surat->nomor_surat) . '.' . $extension;

        return Storage::disk('local')->download($surat->file_surat, $filename);
    }

    public function destroy(Surat $surat)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            if ($surat->pemeriksaan_id) {
                $hasAccess = $surat->pemeriksaan->users()->where('user_id', $user->id)->exists();
                if (!$hasAccess) {
                    abort(403, 'Anda tidak memiliki akses untuk menghapus surat ini.');
                }
            } else {
                abort(403, 'Anda tidak memiliki akses untuk menghapus surat tanpa pemeriksaan induk.');
            }
        }

        if ($surat->file_surat) {
            Storage::disk('local')->delete($surat->file_surat);
        }
        $surat->forceDelete();

        return redirect()->route('surat.index')->with('success', 'Surat berhasil dihapus.');
    }
}
