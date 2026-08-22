<?php

namespace App\Http\Controllers;

use App\Models\PermintaanOpd;
use Illuminate\Http\Request;

class PermintaanOpdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function update(Request $request, PermintaanOpd $permintaanOpd)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $validated = $request->validate([
            'status'  => 'required|in:belum,proses,selesai',
            'catatan' => 'nullable|string',
        ]);

        if ($validated['status'] === 'selesai' && $permintaanOpd->status !== 'selesai') {
            $validated['selesai_at'] = now();
        } elseif ($validated['status'] !== 'selesai') {
            $validated['selesai_at'] = null;
        }

        $permintaanOpd->update($validated);

        $opdNama = urlencode($permintaanOpd->opd);
        if (url()->previous() && str_contains(url()->previous(), '/opd/')) {
            return redirect()->back()->with('success', 'Status berhasil diperbarui.');
        }

        return redirect()->route('surat.show', $permintaanOpd->permintaan->surat_id)
            ->with('success', 'Status OPD berhasil diperbarui.');
    }

    public function bulkUpdate(Request $request)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $validated = $request->validate([
            'permintaan_opd_ids'   => 'required|array|min:1',
            'permintaan_opd_ids.*' => 'integer|exists:permintaan_opd,id',
            'status'               => 'required|in:belum,proses,selesai',
            'catatan'              => 'nullable|string',
        ]);

        $rows = PermintaanOpd::whereIn('id', $validated['permintaan_opd_ids'])->get();

        foreach ($rows as $row) {
            $payload = [
                'status' => $validated['status'],
                'catatan' => $validated['catatan'] ?? null,
            ];

            if ($validated['status'] === 'selesai' && $row->status !== 'selesai') {
                $payload['selesai_at'] = now();
            } elseif ($validated['status'] !== 'selesai') {
                $payload['selesai_at'] = null;
            }

            $row->update($payload);
        }

        return redirect()->back()->with('success', 'Status berhasil diperbarui secara massal.');
    }

    public function destroy(PermintaanOpd $permintaanOpd)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $permintaan = $permintaanOpd->permintaan;
        $suratId = $permintaan->surat_id;
        $opdNama = $permintaanOpd->opd;

        $permintaanOpd->delete();

        $opdList = array_values(array_filter(
            $permintaan->opd ?? [],
            fn($o) => $o !== $opdNama && $o !== 'Semua OPD'
        ));
        $permintaan->update(['opd' => $opdList]);

        return redirect()->route('surat.show', $suratId)->with('success', 'Tag OPD berhasil dihapus.');
    }
}
