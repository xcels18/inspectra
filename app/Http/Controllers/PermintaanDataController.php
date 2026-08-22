<?php

namespace App\Http\Controllers;

use App\Models\PermintaanData;
use App\Models\Surat;
use Illuminate\Http\Request;

class PermintaanDataController extends Controller
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
        $this->authorize_admin();

        $validated = $request->validate([
            'surat_id' => 'required|exists:surat,id',
            'judul_permintaan_id' => 'required|exists:judul_permintaan,id',
            'judul_permintaan' => 'required|string',
            'opd' => 'nullable|array',
            'opd.*' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        $surat = Surat::findOrFail($validated['surat_id']);
        $lastUrut = $surat->permintaanData()->where('judul_permintaan_id', $validated['judul_permintaan_id'])->max('nomor_urut') ?? 0;

        $opdList = $validated['opd'] ?? ['Semua OPD'];
        if (empty($opdList)) $opdList = ['Semua OPD'];

        $pd = $surat->permintaanData()->create([
            'judul_permintaan_id' => $validated['judul_permintaan_id'],
            'nomor_urut'          => $lastUrut + 1,
            'judul_permintaan'    => $validated['judul_permintaan'],
            'opd'                 => $opdList,
            'deskripsi'           => $validated['deskripsi'] ?? null,
            'status'              => 'belum',
        ]);

        $syncList = in_array('Semua OPD', $opdList)
            ? \App\Models\PermintaanData::daftarOpd()
            : $opdList;
        $pd->syncOpd($syncList);

        return redirect()->route('surat.show', $surat)->with('success', 'Item permintaan berhasil ditambahkan.');
    }

    public function show(PermintaanData $permintaan)
    {
        return redirect()->route('surat.show', $permintaan->surat_id);
    }

    public function edit(PermintaanData $permintaan)
    {
        $this->authorize_admin();
        $surat = $permintaan->surat;
        return view('permintaan.edit', compact('permintaan', 'surat'));
    }

    public function update(Request $request, PermintaanData $permintaan)
    {
        $validated = $request->validate([
            'judul_permintaan' => 'required|string',
            'opd' => 'nullable|array',
            'opd.*' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:belum,proses,selesai',
            'catatan' => 'nullable|string',
            'penanggung_jawab' => 'nullable|exists:users,id',
        ]);

        if ($validated['status'] === 'selesai' && $permintaan->status !== 'selesai') {
            $validated['selesai_at'] = now();
        } elseif ($validated['status'] !== 'selesai') {
            $validated['selesai_at'] = null;
        }

        $permintaan->update($validated);

        $opdList = $validated['opd'] ?? $permintaan->opd ?? ['Semua OPD'];
        if (empty($opdList)) $opdList = ['Semua OPD'];
        $syncList = in_array('Semua OPD', $opdList)
            ? \App\Models\PermintaanData::daftarOpd()
            : $opdList;
        $permintaan->syncOpd($syncList);

        $surat = $permintaan->surat;
        $allSelesai = $surat->permintaanData()->where('status', '!=', 'selesai')->count() === 0;
        if ($allSelesai && $surat->status === 'aktif') {
            $surat->update(['status' => 'selesai']);
        }

        return redirect()->route('surat.show', $permintaan->surat_id)->with('success', 'Status permintaan berhasil diperbarui.');
    }

    public function destroy(PermintaanData $permintaan)
    {
        $this->authorize_admin();
        $suratId = $permintaan->surat_id;
        $permintaan->delete();

        return redirect()->route('surat.show', $suratId)->with('success', 'Item permintaan berhasil dihapus.');
    }

    public function bulkAssignOpd(Request $request)
    {
        $this->authorize_admin();

        $validated = $request->validate([
            'surat_id' => 'required|exists:surat,id',
            'permintaan_ids' => 'required|array|min:1',
            'permintaan_ids.*' => 'required|integer|exists:permintaan_data,id',
            'opd' => 'required|array|min:1',
            'opd.*' => 'required|string',
        ], [
            'permintaan_ids.required' => 'Pilih minimal 1 item permintaan data.',
            'opd.required' => 'Pilih minimal 1 OPD.',
        ]);

        $suratId = (int) $validated['surat_id'];
        $permintaanIds = collect($validated['permintaan_ids'])->map(fn($v) => (int) $v)->unique()->values();
        $opdList = collect($validated['opd'])->map(fn($v) => trim((string) $v))->filter()->unique()->values()->all();

        $items = PermintaanData::query()
            ->where('surat_id', $suratId)
            ->whereIn('id', $permintaanIds->all())
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('surat.show', $suratId)->with('error', 'Item permintaan tidak ditemukan untuk surat ini.');
        }

        foreach ($items as $item) {
            $existing = collect($item->opd ?? [])->map(fn($v) => trim((string) $v))->filter();
            $merged = $existing->merge($opdList)->unique()->values()->all();

            if (empty($merged)) {
                $merged = ['Semua OPD'];
            }

            $item->update(['opd' => $merged]);
            $syncList = in_array('Semua OPD', $merged, true)
                ? PermintaanData::daftarOpd()
                : $merged;
            $item->syncOpd($syncList);
        }

        return redirect()
            ->route('surat.show', $suratId)
            ->with('success', 'Berhasil menandai ' . $items->count() . ' item ke ' . count($opdList) . ' OPD.');
    }

    private function authorize_admin()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
    }
}
