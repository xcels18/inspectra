<?php

namespace App\Http\Controllers;

use App\Models\Pemeriksaan;
use App\Models\Surat;
use Illuminate\Http\Request;

class PemeriksaanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = Pemeriksaan::query();
        
        if (!auth()->user()->isAdmin()) {
            $query->whereHas('users', fn($q) => $q->where('user_id', auth()->id()));
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        
        $pemeriksaans = $query->with(['users', 'surat'])->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $tahunList = Pemeriksaan::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        
        return view('pemeriksaan.index', compact('pemeriksaans', 'tahunList'));
    }

    public function create()
    {
        $users = \App\Models\User::where('role', '!=', 'admin')->get();
        return view('pemeriksaan.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tahun' => 'required|string|max:4',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $pemeriksaan = Pemeriksaan::create($request->except('user_ids'));
        
        if ($request->has('user_ids')) {
            $pemeriksaan->users()->sync($request->user_ids);
        }

        return redirect()->route('pemeriksaan.index')->with('success', 'Pemeriksaan berhasil ditambahkan');
    }

    public function show(Pemeriksaan $pemeriksaan)
    {
        if (!auth()->user()->isAdmin() && !$pemeriksaan->users()->where('user_id', auth()->id())->exists()) {
            abort(403, 'Anda tidak memiliki akses ke pemeriksaan ini.');
        }

        $surats = $pemeriksaan->surat()->with('pembuat')->orderBy('created_at', 'desc')->paginate(10);
        $unmappedSurats = Surat::whereNull('pemeriksaan_id')->orderBy('created_at', 'desc')->get();
        return view('pemeriksaan.show', compact('pemeriksaan', 'surats', 'unmappedSurats'));
    }

    public function edit(Pemeriksaan $pemeriksaan)
    {
        $users = \App\Models\User::where('role', '!=', 'admin')->get();
        $assignedUsers = $pemeriksaan->users()->pluck('users.id')->toArray();
        return view('pemeriksaan.edit', compact('pemeriksaan', 'users', 'assignedUsers'));
    }

    public function update(Request $request, Pemeriksaan $pemeriksaan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tahun' => 'required|string|max:4',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'status' => 'required|in:aktif,selesai',
            'keterangan' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $pemeriksaan->update($request->except('user_ids'));
        
        $pemeriksaan->users()->sync($request->user_ids ?? []);

        return redirect()->route('pemeriksaan.index')->with('success', 'Pemeriksaan berhasil diperbarui');
    }

    public function destroy(Pemeriksaan $pemeriksaan)
    {
        $pemeriksaan->delete();
        return redirect()->route('pemeriksaan.index')->with('success', 'Pemeriksaan berhasil dihapus');
    }

    public function attachSurat(Request $request, Pemeriksaan $pemeriksaan)
    {
        $request->validate([
            'surat_ids' => 'required|array',
            'surat_ids.*' => 'exists:surat,id',
        ]);

        Surat::whereIn('id', $request->surat_ids)->update(['pemeriksaan_id' => $pemeriksaan->id]);

        return redirect()->route('pemeriksaan.show', $pemeriksaan)->with('success', count($request->surat_ids) . ' surat berhasil ditambahkan ke pemeriksaan ini.');
    }
}
