<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\JudulPermintaanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\OpdController;
use App\Http\Controllers\PermintaanDataController;
use App\Http\Controllers\PermintaanOpdController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\GoogleDriveSyncController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BackupDokumenController;
use App\Http\Controllers\PemeriksaanController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Auth::routes(['register' => false, 'reset' => false]);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/switch-role', function () {
        $user = auth()->user();
        if ($user->role !== 'admin') abort(403);
        if (session('preview_role') === 'tim_bpk') {
            session()->forget('preview_role');
        } else {
            session(['preview_role' => 'tim_bpk']);
        }
        return back();
    })->name('switch-role');

    Route::get('/surat/template-excel', [SuratController::class, 'downloadTemplate'])->name('surat.template-excel');
    Route::get('/surat/{surat}/download-file', [SuratController::class, 'downloadFile'])->name('surat.download-file');
    Route::post('/surat/import-excel', [SuratController::class, 'importExcel'])->name('surat.import-excel');
    Route::get('/surat/{surat}/export-excel', [SuratController::class, 'exportExcelReport'])->name('surat.export-excel');
    Route::get('/judul-permintaan/{judulPermintaan}/items', [JudulPermintaanController::class, 'items'])->name('judul-permintaan.items');
    Route::post('/pemeriksaan/{pemeriksaan}/attach-surat', [PemeriksaanController::class, 'attachSurat'])->name('pemeriksaan.attach-surat');
    Route::resource('pemeriksaan', PemeriksaanController::class);
    Route::resource('surat', SuratController::class);
    Route::post('/permintaan/bulk-assign-opd', [PermintaanDataController::class, 'bulkAssignOpd'])->name('permintaan.bulk-assign-opd');
    Route::resource('permintaan', PermintaanDataController::class);
    Route::resource('judul-permintaan', JudulPermintaanController::class);
    Route::resource('dokumen', DokumenController::class);
    Route::put('/permintaan-opd/bulk-update', [PermintaanOpdController::class, 'bulkUpdate'])->name('permintaan-opd.bulk-update');
    Route::post('/permintaan-opd/bulk-update', [PermintaanOpdController::class, 'bulkUpdate'])->name('permintaan-opd.bulk-update.post');
    Route::put('/permintaan-opd/{permintaanOpd}', [PermintaanOpdController::class, 'update'])->name('permintaan-opd.update');
    Route::delete('/permintaan-opd/{permintaanOpd}', [PermintaanOpdController::class, 'destroy'])->name('permintaan-opd.destroy');
    Route::get('/opd', [OpdController::class, 'index'])->name('opd.index');
    Route::get('/laporan', [OpdController::class, 'laporanIndex'])->name('laporan.index');
    Route::get('/opd/print', [OpdController::class, 'print'])->name('opd.print');
    Route::get('/opd/{opd}', [OpdController::class, 'show'])->name('opd.show');
    Route::resource('master-opd', \App\Http\Controllers\MasterOpdController::class);
    Route::get('/dokumen/{dokumen}/download', [DokumenController::class, 'download'])->name('dokumen.download');

    Route::get('/validasi', function (\Illuminate\Http\Request $request) {
        return view('validasi', ['kode' => $request->get('kode')]);
    })->name('validasi');

    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/mark-read', [NotifikasiController::class, 'markRead'])->name('notifikasi.mark-read');

    Route::resource('users', UserController::class);

    Route::middleware('admin')->group(function () {
        Route::get('/google-drive', [GoogleDriveSyncController::class, 'index'])->name('google-drive.index');
        Route::post('/google-drive/sync-one/{dokumen}', [GoogleDriveSyncController::class, 'syncOne'])->name('google-drive.sync-one');
        Route::post('/google-drive/sync-all', [GoogleDriveSyncController::class, 'syncAll'])->name('google-drive.sync-all');
        Route::post('/google-drive/sync-surat/{surat}', [GoogleDriveSyncController::class, 'syncSurat'])->name('google-drive.sync-surat');
        Route::get('/google-drive/progress', [GoogleDriveSyncController::class, 'syncProgress'])->name('google-drive.progress');
        Route::get('/google-drive/progress-surat/{surat}', [GoogleDriveSyncController::class, 'syncProgressSurat'])->name('google-drive.progress-surat');
        Route::post('/google-drive/set-folder/{surat}', [GoogleDriveSyncController::class, 'setFolderIdFromUrl'])->name('google-drive.set-folder');
        Route::post('/google-drive/set-structure/{surat}', [GoogleDriveSyncController::class, 'setFolderStructure'])->name('google-drive.set-structure');
        Route::post('/google-drive/reset-sync', [GoogleDriveSyncController::class, 'resetSync'])->name('google-drive.reset-sync');
        Route::post('/google-drive/reset-sync-surat/{surat}', [GoogleDriveSyncController::class, 'resetSyncSurat'])->name('google-drive.reset-sync-surat');

        Route::get('/backup-dokumen', [BackupDokumenController::class, 'index'])->name('backup-dokumen.index');
        Route::post('/backup-dokumen/download', [BackupDokumenController::class, 'downloadZip'])->name('backup-dokumen.download');

        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    });
});
