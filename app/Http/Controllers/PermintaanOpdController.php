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

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui.',
                'status'  => $permintaanOpd->status,
                'status_badge' => $permintaanOpd->status_badge,
                'status_label' => $permintaanOpd->status_label,
                'selesai_at' => $permintaanOpd->selesai_at ? $permintaanOpd->selesai_at->format('d/m/Y') : null,
            ]);
        }

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
            'permintaan_opd_ids'   => 'nullable|array',
            'permintaan_opd_ids.*' => 'integer|exists:permintaan_opd,id',
            'permintaan_ids'       => 'nullable|array',
            'permintaan_ids.*'     => 'integer|exists:permintaan_data,id',
            'status'               => 'required|in:belum,proses,selesai',
            'catatan'              => 'nullable|string',
        ]);

        $opdIds = $validated['permintaan_opd_ids'] ?? [];
        if (!empty($validated['permintaan_ids'])) {
            $fromPermintaan = PermintaanOpd::whereIn('permintaan_id', $validated['permintaan_ids'])->pluck('id')->toArray();
            $opdIds = array_unique(array_merge($opdIds, $fromPermintaan));
        }

        if (empty($opdIds)) {
            return redirect()->back()->with('error', 'Tidak ada item data yang dipilih.');
        }

        $rows = PermintaanOpd::whereIn('id', $opdIds)->get();

        foreach ($rows as $row) {
            $payload = [
                'status' => $validated['status'],
            ];

            if ($request->filled('catatan')) {
                $payload['catatan'] = $validated['catatan'];
            }

            if ($validated['status'] === 'selesai' && $row->status !== 'selesai') {
                $payload['selesai_at'] = now();
            } elseif ($validated['status'] !== 'selesai') {
                $payload['selesai_at'] = null;
            }

            $row->update($payload);
        }

        return redirect()->back()->with('success', count($rows) . ' status berhasil diperbarui secara massal.');
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
