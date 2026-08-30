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

        $oldNama = $masterOpd->nama;
        $newNama = $request->nama;

        $masterOpd->update($request->all());

        if ($oldNama !== $newNama) {
            // Update all existing tags in PermintaanOpd so they are not orphaned
            \App\Models\PermintaanOpd::where('opd', $oldNama)->update(['opd' => $newNama]);
        }

        return redirect()->route('master-opd.index')->with('success', 'OPD berhasil diperbarui.');
    }

    public function destroy(MasterOpd $masterOpd)
    {
        $masterOpd->delete();
        return redirect()->route('master-opd.index')->with('success', 'OPD berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $search = $request->get('search');
        $opds = MasterOpd::when($search, function ($query, $search) {
            return $query->where('nama', 'like', "%{$search}%");
        })->orderBy('nama')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Master OPD');

        $headers = ['NO', 'NAMA OPD', 'KATEGORI'];
        $cols = ['A', 'B', 'C'];

        foreach ($cols as $i => $col) {
            $cell = $col . '1';
            $sheet->setCellValue($cell, $headers[$i]);
            $sheet->getStyle($cell)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('1e40af');
        }

        $row = 2;
        foreach ($opds as $idx => $opd) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $opd->nama);
            $sheet->setCellValue('C' . $row, $opd->kategori ?? 'OPD');
            $row++;
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Data_Master_OPD_Sekolah.xlsx';
        
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
