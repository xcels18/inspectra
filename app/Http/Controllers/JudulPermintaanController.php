<?php

namespace App\Http\Controllers;

use App\Models\JudulPermintaan;
use App\Models\PermintaanData;
use App\Models\Surat;
use Illuminate\Http\Request;

class JudulPermintaanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function items(Request $request, JudulPermintaan $judulPermintaan)
    {
        $perPage = 50;
        $page    = max(1, (int) $request->get('page', 1));

        $search = trim((string) $request->get('search', ''));

        $query = $judulPermintaan->permintaanData()
            ->with(['permintaanOpd.dokumen'])
            ->orderBy('nomor_urut');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul_permintaan', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('permintaanOpd', function ($opdQ) use ($search) {
                      $opdQ->where('opd', 'like', "%{$search}%")
                           ->orWhere('catatan', 'like', "%{$search}%");
                  });
            });
        }

        $items = $query->paginate($perPage, ['*'], 'page', $page);

        $isAdmin   = auth()->user()->isAdmin();
        $daftarOpd = PermintaanData::opsiOpd();
        $users     = \App\Models\User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'role']);

        $html = view('surat._judul_items', compact('items', 'isAdmin', 'daftarOpd', 'users', 'judulPermintaan'))->render();

        return response()->json([
            'html'         => $html,
            'current_page' => $items->currentPage(),
            'last_page'    => $items->lastPage(),
            'total'        => $items->total(),
            'from'         => $items->firstItem(),
            'to'           => $items->lastItem(),
            'is_admin'     => $isAdmin,
        ]);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'surat_id' => 'required|exists:surat,id',
            'judul' => 'required|string|max:500',
        ]);

        $surat = Surat::findOrFail($request->surat_id);
        $lastUrut = $surat->judulPermintaan()->max('nomor_urut') ?? 0;

        $surat->judulPermintaan()->create([
            'nomor_urut' => $lastUrut + 1,
            'judul' => $request->judul,
        ]);

        return redirect()->route('surat.show', $surat)->with('success', 'Judul permintaan berhasil ditambahkan.');
    }

    public function update(Request $request, JudulPermintaan $judulPermintaan)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'judul' => 'required|string|max:500',
        ]);

        $judulPermintaan->update(['judul' => $request->judul]);

        return redirect()->route('surat.show', $judulPermintaan->surat_id)->with('success', 'Judul berhasil diperbarui.');
    }

    public function destroy(JudulPermintaan $judulPermintaan)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $suratId = $judulPermintaan->surat_id;
        $judulPermintaan->delete();

        return redirect()->route('surat.show', $suratId)->with('success', 'Judul permintaan berhasil dihapus.');
    }
}
