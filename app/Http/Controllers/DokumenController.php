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

    public function reuse(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'permintaan_opd_id' => 'required|exists:permintaan_opd,id',
            'dokumen_ids'       => 'required|array',
            'dokumen_ids.*'     => 'required|exists:dokumen,id',
        ]);

        $permintaanOpd = PermintaanOpd::with('permintaan')->findOrFail($request->permintaan_opd_id);
        $permintaan = $permintaanOpd->permintaan;

        $dokumensToReuse = Dokumen::whereIn('id', $request->dokumen_ids)->get();

        $count = 0;
        foreach ($dokumensToReuse as $oldDoc) {
            Dokumen::create([
                'permintaan_id'     => $permintaan->id,
                'permintaan_opd_id' => $permintaanOpd->id,
                'nama_file'         => $oldDoc->nama_file,
                'path_file'         => $oldDoc->path_file,
                'mime_type'         => $oldDoc->mime_type,
                'ukuran_file'       => $oldDoc->ukuran_file,
                'keterangan'        => $oldDoc->keterangan,
                'uploaded_by'       => auth()->id(),
            ]);
            $count++;
        }

        if ($permintaanOpd->status === 'belum' && $count > 0) {
            $permintaanOpd->update(['status' => 'proses']);
        }

        return redirect()->back()->with('success', $count . ' dokumen berhasil ditautkan dari arsip.');
    }

    public function destroy(Dokumen $dokuman)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $permintaanOpd = $dokuman->permintaanOpd;
        $pathFile = $dokuman->path_file;

        $dokuman->delete();

        $stillUsed = Dokumen::where('path_file', $pathFile)->exists();
        if (!$stillUsed) {
            Storage::disk('local')->delete($pathFile);
        }

        if ($permintaanOpd && $permintaanOpd->dokumen()->count() === 0) {
            if ($permintaanOpd->status !== 'selesai') {
                $permintaanOpd->update(['status' => 'belum']);
            }
        }

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function download(Dokumen $dokumen)
    {
        if (!$this->checkDokumenAccess(auth()->user(), $dokumen)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh dokumen ini.');
        }

        if (!Storage::disk('local')->exists($dokumen->path_file)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('local')->download($dokumen->path_file, $dokumen->nama_file);
    }

    public function preview(Dokumen $dokumen)
    {
        if (!$this->checkDokumenAccess(auth()->user(), $dokumen)) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat pratinjau dokumen ini.');
        }

        if (!Storage::disk('local')->exists($dokumen->path_file)) {
            abort(404, 'File tidak ditemukan.');
        }

        $mime = Storage::disk('local')->mimeType($dokumen->path_file);
        $file = Storage::disk('local')->get($dokumen->path_file);

        return response($file, 200, [
            'Content-Type'        => $mime ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . rawurlencode($dokumen->nama_file) . '"',
        ]);
    }

    private function checkDokumenAccess($user, Dokumen $dokumen): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($dokumen->uploaded_by === $user->id) {
            return true;
        }

        $surat = $dokumen->permintaanOpd
            ? $dokumen->permintaanOpd->permintaan?->surat
            : ($dokumen->permintaan ? $dokumen->permintaan->surat : null);

        if ($surat && $surat->pemeriksaan_id) {
            return \App\Models\Pemeriksaan::where('id', $surat->pemeriksaan_id)
                ->whereHas('users', fn($q) => $q->where('user_id', $user->id))
                ->exists();
        }

        return true;
    }
}
