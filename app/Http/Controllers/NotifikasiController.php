<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $notifikasi = Dokumen::with(['permintaanOpd', 'permintaan.surat', 'permintaan.judulPermintaan'])
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        return response()->json([
            'count' => $notifikasi->count(),
            'items' => $notifikasi->map(function ($dok) {
                return [
                    'id'          => $dok->id,
                    'nama_file'   => $dok->nama_file,
                    'opd'         => $dok->permintaanOpd?->opd ?? '-',
                    'judul'       => $dok->permintaan?->judul_permintaan ?? '-',
                    'surat'       => $dok->permintaan?->surat?->nomor_surat ?? '-',
                    'opd_url'     => $dok->permintaanOpd?->opd ? url('/opd/' . rawurlencode($dok->permintaanOpd->opd)) : null,
                    'waktu'       => $dok->created_at->diffForHumans(),
                ];
            }),
        ]);
    }

    public function markRead(Request $request)
    {
        Dokumen::where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
