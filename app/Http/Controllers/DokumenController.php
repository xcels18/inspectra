<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\PermintaanData;
use App\Models\PermintaanOpd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return redirect()->route('surat.index');
    }

    public function create()
    {
        return redirect()->route('surat.index');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'permintaan_opd_id' => 'required|exists:permintaan_opd,id',
            'file'              => 'required|array|max:100',
            'file.*'            => 'required|file|max:512000',
            'keterangan'        => 'nullable|string|max:500',
            'rename_enabled'    => 'nullable|in:1',
            'rename_custom'     => 'nullable|string|max:100',
        ]);

        $permintaanOpd = PermintaanOpd::with('permintaan')->findOrFail($request->permintaan_opd_id);
        $permintaan = $permintaanOpd->permintaan;

        $renameEnabled = $request->input('rename_enabled') === '1';
        $customRaw = trim((string) $request->input('rename_custom', ''));
        $baseNamaPersis = trim((string) $permintaan->judul_permintaan);
        if ($baseNamaPersis === '') {
            $baseNamaPersis = 'Dokumen';
        }

        foreach ($request->file('file') as $index => $file) {
            $storedPath = null;
            $namaSimpan = $file->getClientOriginalName();

            if ($renameEnabled) {
                $ext = strtolower((string) $file->getClientOriginalExtension());
                $seq = $index + 1;

                $finalDisplayName = $baseNamaPersis;
                if ($customRaw !== '') {
                    $finalDisplayName .= ' - ' . $customRaw;
                }
                $finalDisplayName .= ' - ' . $seq;
                $finalDisplayName .= ($ext !== '' ? '.' . $ext : '');

                $safeBaseForStorage = Str::of($baseNamaPersis)->slug('-')->value();
                if ($safeBaseForStorage === '') {
                    $safeBaseForStorage = 'dokumen';
                }
                $safeCustomForStorage = Str::of($customRaw)->slug('-')->value();

                $safeFileName = $safeBaseForStorage;
                if ($safeCustomForStorage !== '') {
                    $safeFileName .= '-' . $safeCustomForStorage;
                }
                $safeFileName .= '-' . $seq;
                $safeFileName .= ($ext !== '' ? '.' . $ext : '');

                $storedPath = $file->storeAs('dokumen/' . $permintaan->surat_id, $safeFileName, 'local');
                $namaSimpan = $finalDisplayName;
            } else {
                $storedPath = $file->store('dokumen/' . $permintaan->surat_id, 'local');
            }

            Dokumen::create([
                'permintaan_id'     => $permintaan->id,
                'permintaan_opd_id' => $permintaanOpd->id,
                'nama_file'         => $namaSimpan,
                'path_file'         => $storedPath,
                'mime_type'         => $file->getClientMimeType(),
                'ukuran_file'       => $file->getSize(),
                'keterangan'        => $request->keterangan,
                'uploaded_by'       => auth()->id(),
            ]);
        }

        if ($permintaanOpd->status === 'belum') {
            $permintaanOpd->update(['status' => 'proses']);
        }

        $jumlah = count($request->file('file'));
        return redirect()->back()->with('success', $jumlah . ' dokumen berhasil diupload.');
    }

    public function show(Dokumen $dokuman)
    {
        return redirect()->route('surat.show', $dokuman->permintaan->surat_id);
    }

    public function edit(Dokumen $dokuman)
    {
        return redirect()->route('surat.show', $dokuman->permintaan->surat_id);
    }

    public function update(Request $request, Dokumen $dokuman)
    {
        return redirect()->route('surat.show', $dokuman->permintaan->surat_id);
    }

    public function destroy(Dokumen $dokuman)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $permintaanOpd = $dokuman->permintaanOpd;

        Storage::disk('local')->delete($dokuman->path_file);
        $dokuman->delete();

        if ($permintaanOpd && $permintaanOpd->dokumen()->count() === 0) {
            if ($permintaanOpd->status !== 'selesai') {
                $permintaanOpd->update(['status' => 'belum']);
            }
        }

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function download(Dokumen $dokumen)
    {
        if (!Storage::disk('local')->exists($dokumen->path_file)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('local')->download($dokumen->path_file, $dokumen->nama_file);
    }
}
