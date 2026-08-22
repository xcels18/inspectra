<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\PermintaanData;
use App\Models\PermintaanOpd;
use App\Models\Setting;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Google\Client as GoogleClient;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;

class GoogleDriveSyncController extends Controller
{
    const DEFAULT_STRUCTURE = ['nomor_surat', 'opd', 'judul_permintaan'];

    const LEVEL_LABELS = [
        'nomor_surat'      => 'Nomor Surat',
        'opd'              => 'OPD',
        'judul_permintaan' => 'List Permintaan',
    ];

    private function getDriveService(): Drive
    {
        $client = new GoogleClient();

        if (env('GOOGLE_DRIVE_REFRESH_TOKEN')) {
            $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
            $client->refreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
        } else {
            $client->setAuthConfig(base_path(env('GOOGLE_DRIVE_CREDENTIALS')));
            $client->addScope(Drive::DRIVE);
        }

        return new Drive($client);
    }

    private function getOrCreateFolder(Drive $service, string $name, string $parentId): string
    {
        $safeName = addslashes($name);
        $results = $service->files->listFiles([
            'q'      => "mimeType='application/vnd.google-apps.folder' and name='{$safeName}' and '{$parentId}' in parents and trashed=false",
            'fields' => 'files(id,name)',
        ]);

        foreach ($results->getFiles() as $f) {
            if ($f->getName() === $name) {
                return $f->getId();
            }
        }

        $meta   = new DriveFile([
            'name'     => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents'  => [$parentId],
        ]);
        $folder = $service->files->create($meta, ['fields' => 'id']);

        $this->shareWithOwner($service, $folder->getId());

        return $folder->getId();
    }

    private function uploadFile(Drive $service, string $localPath, string $fileName, string $folderId): string
    {
        $existing = $service->files->listFiles([
            'q'      => "'{$folderId}' in parents and trashed=false",
            'fields' => 'files(id,name)',
        ]);

        $existingId = null;
        foreach ($existing->getFiles() as $f) {
            if ($f->getName() === $fileName) {
                $existingId = $f->getId();
                break;
            }
        }

        $mimeType = mime_content_type($localPath) ?: 'application/octet-stream';
        $content  = file_get_contents($localPath);

        if ($existingId) {
            $updateMeta = new DriveFile();
            $file       = $service->files->update($existingId, $updateMeta, [
                'data'       => $content,
                'mimeType'   => $mimeType,
                'uploadType' => 'multipart',
                'fields'     => 'id',
            ]);
            return $file->getId();
        }

        $meta = new DriveFile([
            'name'    => $fileName,
            'parents' => [$folderId],
        ]);

        $file = $service->files->create($meta, [
            'data'       => $content,
            'mimeType'   => $mimeType,
            'uploadType' => 'multipart',
            'fields'     => 'id',
        ]);
        return $file->getId();
    }

    private function shareWithOwner(Drive $service, string $fileId): void
    {
        $ownerEmail = env('GOOGLE_DRIVE_OWNER_EMAIL');
        if (!$ownerEmail) return;

        try {
            $perm = new \Google\Service\Drive\Permission([
                'type'         => 'user',
                'role'         => 'writer',
                'emailAddress' => $ownerEmail,
            ]);
            $service->permissions->create($fileId, $perm, [
                'sendNotificationEmail' => false,
            ]);
        } catch (\Throwable) {
        }
    }

    private function folderExists(Drive $service, string $folderId): bool
    {
        try {
            $file = $service->files->get($folderId, ['fields' => 'id,trashed']);
            return !$file->getTrashed();
        } catch (\Throwable) {
            return false;
        }
    }

    private function sanitize(string $name): string
    {
        $search  = ["\u{2013}", "\u{2014}", "\u{2018}", "\u{2019}", "\u{201C}", "\u{201D}"];
        $name    = str_replace($search, '-', $name);
        $name    = preg_replace('/[\/\\\:\*\?"<>\|\']/u', '-', $name);
        $name    = preg_replace('/\s+/', ' ', $name);
        return mb_substr(trim($name), 0, 100);
    }

    private function getOrCreateRootFolder(Drive $service, ?string $folderInduk = null): string
    {
        $folderName = $folderInduk ?: Setting::get('gdrive_root_folder_name', env('GOOGLE_DRIVE_ROOT_FOLDER_NAME', 'BPK Dokumen'));

        $results = $service->files->listFiles([
            'q'      => "name='" . addslashes($folderName) . "' and mimeType='application/vnd.google-apps.folder' and 'root' in parents and trashed=false",
            'fields' => 'files(id)',
        ]);

        if (!empty($results->getFiles())) {
            return $results->getFiles()[0]->getId();
        }

        $meta   = new DriveFile([
            'name'     => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder',
        ]);
        $folder = $service->files->create($meta, ['fields' => 'id']);

        $this->shareWithOwner($service, $folder->getId());

        return $folder->getId();
    }

    private function syncDokumen(Dokumen $dokumen, ?string $folderInduk = null): void
    {
        $localPath = Storage::disk('local')->path($dokumen->path_file);

        if (!file_exists($localPath)) {
            throw new \RuntimeException('File tidak ditemukan di server.');
        }

        $dokumen->load(['permintaanOpd.permintaan.surat', 'permintaanOpd.permintaan']);

        $service       = $this->getDriveService();
        $permintaanOpd = $dokumen->permintaanOpd;
        $permintaan    = $permintaanOpd->permintaan;
        $surat         = $permintaan->surat;

        $structure = $surat->gdrive_folder_structure ?: self::DEFAULT_STRUCTURE;

        $levelValues = [
            'nomor_surat'      => $this->sanitize($surat->nomor_surat ?? ('Surat-' . $surat->id)),
            'opd'              => $this->sanitize($permintaanOpd->opd ?? 'OPD'),
            'judul_permintaan' => $this->sanitize($permintaan->judul_permintaan ?? ('Permintaan-' . $permintaan->id)),
        ];

        $fileName = $dokumen->nama_file;

        $rootFolderId = $this->getOrCreateRootFolder($service, $folderInduk);

        if ($surat->gdrive_folder_id && $this->folderExists($service, $surat->gdrive_folder_id)) {
            $currentFolderId = $surat->gdrive_folder_id;
        } else {
            $nomorSurat = $levelValues['nomor_surat'];
            $currentFolderId = $this->getOrCreateFolder($service, $nomorSurat, $rootFolderId);
            $surat->update(['gdrive_folder_id' => $currentFolderId]);
        }

        $remainingLevels = array_filter($structure, fn($l) => $l !== 'nomor_surat');
        foreach ($remainingLevels as $level) {
            if (isset($levelValues[$level])) {
                $currentFolderId = $this->getOrCreateFolder($service, $levelValues[$level], $currentFolderId);
            }
        }

        $this->uploadFile($service, $localPath, $fileName, $currentFolderId);

        $rootLabel    = $folderInduk ?: Setting::get('gdrive_root_folder_name', env('GOOGLE_DRIVE_ROOT_FOLDER_NAME', 'BPK Dokumen'));
        $pathParts    = [$rootLabel, $levelValues['nomor_surat']];
        foreach ($remainingLevels as $level) {
            if (isset($levelValues[$level])) {
                $pathParts[] = $levelValues[$level];
            }
        }
        $pathParts[] = $fileName;

        $dokumen->update([
            'gdrive_path'      => implode('/', $pathParts),
            'gdrive_synced_at' => now(),
        ]);
    }

    public function index(Request $request)
    {
        $rootFolderName = Setting::get('gdrive_root_folder_name', env('GOOGLE_DRIVE_ROOT_FOLDER_NAME', 'BPK Dokumen'));
        $perPage        = 10;
        $page           = $request->get('page', 1);

        $suratList = Surat::with([
            'judulPermintaan.permintaanData.permintaanOpd.dokumen',
        ])->orderByDesc('created_at')->get();

        $suratStats = $suratList->map(function ($surat) {
            $allDokumen = $surat->judulPermintaan
                ->flatMap->permintaanData
                ->flatMap->permintaanOpd
                ->flatMap->dokumen;

            return [
                'surat'      => $surat,
                'total'      => $allDokumen->count(),
                'synced'     => $allDokumen->whereNotNull('gdrive_synced_at')->count(),
                'unsynced'   => $allDokumen->whereNull('gdrive_synced_at')->count(),
                'structure'  => $surat->gdrive_folder_structure ?: self::DEFAULT_STRUCTURE,
            ];
        });

        $suratStatsPaginated = new LengthAwarePaginator(
            $suratStats->forPage($page, $perPage)->values(),
            $suratStats->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $totalDokumen = Dokumen::count();
        $sudahSync    = Dokumen::whereNotNull('gdrive_synced_at')->count();
        $belumSync    = $totalDokumen - $sudahSync;

        return view('google-drive.index', compact('suratStatsPaginated', 'totalDokumen', 'sudahSync', 'belumSync', 'rootFolderName'));
    }

    public function setRootFolder(Request $request)
    {
        $request->validate(['root_folder_name' => 'required|string|max:100']);
        Setting::set('gdrive_root_folder_name', trim($request->input('root_folder_name')));
        return redirect()->route('google-drive.index')->with('success', 'Nama folder root berhasil diperbarui.');
    }

    public function setFolderStructure(Request $request, Surat $surat)
    {
        $request->validate([
            'structure'   => 'required|array|min:1',
            'structure.*' => 'in:nomor_surat,opd,judul_permintaan',
            'folder_url'  => 'nullable|string',
        ]);

        $structure = $request->input('structure');

        if (!in_array('nomor_surat', $structure)) {
            array_unshift($structure, 'nomor_surat');
        }

        $updateData = ['gdrive_folder_structure' => $structure];

        $folderUrl = trim($request->input('folder_url', ''));
        if ($folderUrl) {
            $folderId = null;
            if (preg_match('/\/folders\/([a-zA-Z0-9_-]+)/', $folderUrl, $m)) {
                $folderId = $m[1];
            } elseif (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $folderUrl, $m)) {
                $folderId = $m[1];
            }
            if ($folderId) {
                $updateData['gdrive_folder_id'] = $folderId;
            }
        }

        $surat->update($updateData);

        return redirect()->route('google-drive.index')->with('success', 'Pengaturan folder surat ' . $surat->nomor_surat . ' berhasil disimpan.');
    }

    public function setFolderIdFromUrl(Request $request, Surat $surat)
    {
        $request->validate(['folder_url' => 'required|string']);

        $url      = trim($request->input('folder_url'));
        $folderId = null;

        if (preg_match('/\/folders\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            $folderId = $m[1];
        } elseif (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $url, $m)) {
            $folderId = $m[1];
        }

        if (!$folderId) {
            return redirect()->route('google-drive.index')->with('error', 'URL Google Drive tidak valid.');
        }

        $surat->update(['gdrive_folder_id' => $folderId]);
        return redirect()->route('google-drive.index')->with('success', 'Folder berhasil dipetakan untuk surat: ' . $surat->nomor_surat);
    }

    public function syncOne(Dokumen $dokumen, Request $request)
    {
        try {
            $folderInduk = trim($request->input('folder_induk', ''));
            $this->syncDokumen($dokumen, $folderInduk ?: null);
            return response()->json(['success' => true, 'message' => 'Berhasil disinkron ke Google Drive.']);
        } catch (\Throwable $e) {
            $namaFile = $dokumen->nama_file ?: basename($dokumen->path_file);
            return response()->json(['success' => false, 'message' => $namaFile . ': ' . $e->getMessage()]);
        }
    }

    public function syncSurat(Surat $surat, Request $request)
    {
        $folderInduk = trim($request->input('folder_induk', ''));
        $dokumens    = Dokumen::whereHas('permintaanOpd.permintaan', fn($q) => $q->where('surat_id', $surat->id))
                        ->whereNull('gdrive_synced_at')->get();

        $berhasil = 0;
        $gagal    = 0;
        $errors   = [];

        foreach ($dokumens as $dok) {
            try {
                $this->syncDokumen($dok, $folderInduk ?: null);
                $berhasil++;
            } catch (\Throwable $e) {
                $gagal++;
                $errors[] = $dok->nama_file . ': ' . $e->getMessage();
            }
        }

        return response()->json([
            'success'  => true,
            'berhasil' => $berhasil,
            'gagal'    => $gagal,
            'errors'   => $errors,
        ]);
    }

    public function syncAll()
    {
        $belumSync = Dokumen::whereNull('gdrive_synced_at')->get();
        $berhasil  = 0;
        $gagal     = 0;
        $errors    = [];

        foreach ($belumSync as $dokumen) {
            try {
                $this->syncDokumen($dokumen);
                $berhasil++;
            } catch (\Throwable $e) {
                $gagal++;
                $errors[] = ($dokumen->nama_file ?: basename($dokumen->path_file)) . ': ' . $e->getMessage();
            }
        }

        return response()->json([
            'success'  => true,
            'berhasil' => $berhasil,
            'gagal'    => $gagal,
            'errors'   => $errors,
        ]);
    }

    public function syncProgress()
    {
        $total     = Dokumen::count();
        $synced    = Dokumen::whereNotNull('gdrive_synced_at')->count();
        $belumSync = Dokumen::whereNull('gdrive_synced_at')->pluck('id');
        $semuaIds  = Dokumen::pluck('id');

        return response()->json([
            'total'     => $total,
            'synced'    => $synced,
            'belum'     => $total - $synced,
            'belum_ids' => $belumSync,
            'semua_ids' => $semuaIds,
        ]);
    }

    public function syncProgressSurat(Surat $surat)
    {
        $belumSync = Dokumen::whereHas('permintaanOpd.permintaan', fn($q) => $q->where('surat_id', $surat->id))
            ->whereNull('gdrive_synced_at')
            ->pluck('id');

        return response()->json([
            'belum_ids' => $belumSync,
        ]);
    }

    public function resetSync()
    {
        Dokumen::query()->update([
            'gdrive_synced_at' => null,
            'gdrive_path'      => null,
        ]);

        Surat::query()->update([
            'gdrive_folder_id' => null,
        ]);

        return redirect()->route('google-drive.index')->with('success', 'Reset sync berhasil. Semua dokumen dianggap belum disinkron.');
    }

    public function resetSyncSurat(Surat $surat)
    {
        Dokumen::whereHas('permintaanOpd.permintaan', fn($q) => $q->where('surat_id', $surat->id))
            ->update([
                'gdrive_synced_at' => null,
                'gdrive_path'      => null,
            ]);

        $surat->update(['gdrive_folder_id' => null]);

        return redirect()->route('google-drive.index')->with('success', 'Reset sync untuk surat ' . $surat->nomor_surat . ' berhasil.');
    }
}
