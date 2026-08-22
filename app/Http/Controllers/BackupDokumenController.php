<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupDokumenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $suratList = Surat::with([
            'judulPermintaan.permintaanData.permintaanOpd.dokumen',
        ])->orderByDesc('tanggal_surat')->get();

        $suratStats = $suratList->map(function ($surat) {
            $dokumenCount = 0;
            foreach ($surat->judulPermintaan as $judul) {
                foreach ($judul->permintaanData as $permintaan) {
                    foreach ($permintaan->permintaanOpd as $opd) {
                        $dokumenCount += $opd->dokumen->count();
                    }
                }
            }

            return [
                'surat' => $surat,
                'dokumen_count' => $dokumenCount,
            ];
        });

        return view('backup-dokumen.index', [
            'suratStats' => $suratStats,
            'defaultStructure' => ['nomor_surat', 'opd', 'judul_permintaan'],
            'structureOptions' => [
                'nomor_surat' => 'Nomor Surat',
                'opd' => 'OPD',
                'judul_permintaan' => 'Judul Permintaan',
            ],
        ]);
    }

    public function downloadZip(Request $request)
    {
        $validated = $request->validate([
            'surat_ids' => 'required|array|min:1',
            'surat_ids.*' => 'integer|exists:surat,id',
            'structure' => 'required|array|min:1',
            'structure.*' => 'in:nomor_surat,opd,judul_permintaan',
        ]);

        $structure = array_values(array_unique($validated['structure']));
        if (!in_array('nomor_surat', $structure, true)) {
            array_unshift($structure, 'nomor_surat');
        }

        $suratList = Surat::with([
            'judulPermintaan.permintaanData.permintaanOpd.dokumen',
        ])->whereIn('id', $validated['surat_ids'])->get();

        if ($suratList->isEmpty()) {
            return back()->with('error', 'Data surat tidak ditemukan.');
        }

        $zipName = 'backup_dokumen_' . now()->format('Ymd_His') . '.zip';
        $zipRelativePath = 'temp/' . $zipName;
        $zipFullPath = storage_path('app/' . $zipRelativePath);

        if (!is_dir(dirname($zipFullPath))) {
            mkdir(dirname($zipFullPath), 0775, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        $addedFiles = 0;

        foreach ($suratList as $surat) {
            foreach ($surat->judulPermintaan as $judul) {
                foreach ($judul->permintaanData as $permintaan) {
                    foreach ($permintaan->permintaanOpd as $opd) {
                        foreach ($opd->dokumen as $dokumen) {
                            $storedPath = $dokumen->path_file;
                            if (empty($storedPath)) {
                                continue;
                            }

                            $storageDisk = Storage::disk('local');
                            if (!$storageDisk->exists($storedPath)) {
                                continue;
                            }

                            $pathSegments = [];
                            foreach ($structure as $level) {
                                if ($level === 'nomor_surat') {
                                    $pathSegments[] = $this->sanitizeSegment($surat->nomor_surat ?: ('Surat-' . $surat->id));
                                } elseif ($level === 'opd') {
                                    $pathSegments[] = $this->sanitizeSegment($opd->opd ?: 'OPD');
                                } elseif ($level === 'judul_permintaan') {
                                    $pathSegments[] = $this->sanitizeSegment($judul->judul ?: 'Judul');
                                }
                            }

                            $innerPath = implode('/', array_filter($pathSegments));
                            $originalName = $dokumen->nama_file ?: basename($storedPath);
                            $fileName = $this->sanitizeSegment($originalName);
                            $absoluteFilePath = $storageDisk->path($storedPath);

                            $zip->addFile($absoluteFilePath, trim($innerPath . '/' . $fileName, '/'));
                            $addedFiles++;
                        }
                    }
                }
            }
        }

        if ($addedFiles === 0) {
            $zip->close();
            @unlink($zipFullPath);
            return back()->with('error', 'Tidak ada file dokumen yang dapat dibackup.');
        }

        $zip->close();

        return response()->download($zipFullPath, $zipName)->deleteFileAfterSend(true);
    }

    private function sanitizeSegment(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/[\\\\\\/:\*\?"<>\|]+/', '-', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        $name = trim($name, '. ');
        return $name !== '' ? $name : 'Unknown';
    }
}
