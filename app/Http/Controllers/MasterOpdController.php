<?php

namespace App\Http\Controllers;

use App\Models\MasterOpd;
use Illuminate\Http\Request;

class MasterOpdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $opds = MasterOpd::when($search, function ($query, $search) {
            return $query->where('nama', 'like', "%{$search}%");
        })->orderBy('nama')->paginate(20);

        return view('master-opd.index', compact('opds', 'search'));
    }

    public function create()
    {
        return view('master-opd.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:master_opds,nama',
            'kategori' => 'required|string|in:OPD,Sekolah,Partai Politik,Instansi Vertical',
        ]);

        MasterOpd::create($request->all());

        return redirect()->route('master-opd.index')->with('success', 'OPD berhasil ditambahkan.');
    }

    public function edit(MasterOpd $masterOpd)
    {
        return view('master-opd.edit', compact('masterOpd'));
    }

    public function update(Request $request, MasterOpd $masterOpd)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:master_opds,nama,' . $masterOpd->id,
            'kategori' => 'required|string|in:OPD,Sekolah,Partai Politik,Instansi Vertical',
        ]);

        $masterOpd->update($request->all());

        return redirect()->route('master-opd.index')->with('success', 'OPD berhasil diperbarui.');
    }

    public function destroy(MasterOpd $masterOpd)
    {
        $masterOpd->delete();
        return redirect()->route('master-opd.index')->with('success', 'OPD berhasil dihapus.');
    }
}
