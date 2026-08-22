@extends('layouts.app')

@section('title', 'Google Drive Sync')

@section('content')
<div class="container-fluid py-3" style="max-width:1200px;">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 mb-3" role="alert" style="font-size:0.83rem;">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert" style="font-size:0.83rem;">
            <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="card shadow-sm mb-3 border-0" style="background:linear-gradient(135deg,#1a73e8 0%,#0d47a1 100%);">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center g-2">
                <div class="col-auto">
                    <h5 class="mb-0 fw-bold text-white">
                        <i class="bi bi-google me-2"></i>Google Drive Sync
                    </h5>
                </div>
                <div class="col">
                    <form action="{{ route('google-drive.set-root-folder') }}" method="POST" class="d-flex align-items-center gap-2">
                        @csrf
                        <div class="input-group input-group-sm" style="max-width:300px;">
                            <span class="input-group-text" style="font-size:0.78rem; background:rgba(255,255,255,0.15); color:#fff; border-color:rgba(255,255,255,0.3);">
                                <i class="bi bi-folder2 me-1"></i>Root
                            </span>
                            <input type="text" name="root_folder_name"
                                   class="form-control form-control-sm"
                                   style="font-size:0.78rem; background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.3);"
                                   value="{{ $rootFolderName }}" required
                                   placeholder="Nama folder root">
                            <button type="submit" class="btn btn-sm" style="background:rgba(255,255,255,0.2); color:#fff; border-color:rgba(255,255,255,0.3); font-size:0.78rem;">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-auto d-flex align-items-center gap-2 flex-wrap">
                    {{-- Statistik --}}
                    <div class="d-flex align-items-center gap-1 px-3 py-1 rounded-pill" style="background:rgba(255,255,255,0.15); font-size:0.78rem; color:#fff;">
                        <i class="bi bi-files me-1"></i>{{ $totalDokumen }} total
                    </div>
                    <div class="d-flex align-items-center gap-1 px-3 py-1 rounded-pill" style="background:rgba(52,211,153,0.25); font-size:0.78rem; color:#a7f3d0;">
                        <i class="bi bi-cloud-check me-1"></i>{{ $sudahSync }} tersync
                    </div>
                    <div class="d-flex align-items-center gap-1 px-3 py-1 rounded-pill" style="background:rgba(251,191,36,0.2); font-size:0.78rem; color:#fde68a;">
                        <i class="bi bi-cloud-slash me-1"></i>{{ $belumSync }} belum
                    </div>
                    {{-- Overall progress bar --}}
                    @if($totalDokumen > 0)
                    <div style="width:90px;">
                        @php $overallPct = round($sudahSync / $totalDokumen * 100); @endphp
                        <div style="height:5px; background:rgba(255,255,255,0.2); border-radius:4px; overflow:hidden;">
                            <div style="height:100%; width:{{ $overallPct }}%; background:#34d399; border-radius:4px; transition:width 0.4s;"></div>
                        </div>
                        <div style="font-size:0.68rem; color:rgba(255,255,255,0.7); text-align:right; margin-top:2px;">{{ $overallPct }}%</div>
                    </div>
                    @endif
                    {{-- Tombol aksi --}}
                    <button class="btn btn-sm fw-semibold" id="btnSyncAll"
                            style="background:#fff; color:#1a73e8; font-size:0.78rem;"
                            {{ $belumSync == 0 ? 'disabled' : '' }}>
                        <i class="bi bi-cloud-arrow-up me-1"></i>Sync Semua
                    </button>
                    <form id="resetAllSyncForm" action="{{ route('google-drive.reset-sync') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="button" class="btn btn-sm"
                                style="background:rgba(239,68,68,0.2); color:#fca5a5; border:1px solid rgba(239,68,68,0.4); font-size:0.78rem;"
                                onclick="openResetConfirmModal()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Semua
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Daftar Surat --}}
    <div class="card shadow-sm border-0">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background:#f8f9fa; border-bottom:1px solid #e9ecef;">
            <span class="fw-semibold text-muted" style="font-size:0.82rem;">
                <i class="bi bi-envelope me-1"></i>Daftar Surat
                <span class="badge bg-secondary ms-1" style="font-size:0.68rem;">{{ $suratStatsPaginated->total() }}</span>
            </span>
            <span class="text-muted" style="font-size:0.75rem;">
                Halaman {{ $suratStatsPaginated->currentPage() }} dari {{ $suratStatsPaginated->lastPage() }}
            </span>
        </div>
        <div class="card-body p-0">
            @forelse($suratStatsPaginated as $stat)
            @php
                $surat    = $stat['surat'];
                $total    = $stat['total'];
                $synced   = $stat['synced'];
                $unsynced = $stat['unsynced'];
                $struct   = $stat['structure'];
                $pct      = $total > 0 ? round($synced / $total * 100) : 0;
            @endphp

            {{-- Baris surat --}}
            <div class="border-bottom surat-row" id="row{{ $surat->id }}">

                {{-- Header baris --}}
                <div class="d-flex align-items-center gap-2 px-3 py-2"
                     style="cursor:pointer; transition:background 0.15s;"
                     onclick="toggleDokumen({{ $surat->id }})"
                     onmouseenter="this.style.background='#f8f9fa'"
                     onmouseleave="this.style.background=''">

                    {{-- Toggle icon --}}
                    <span id="toggleIcon{{ $surat->id }}" class="text-muted" style="font-size:0.75rem; min-width:14px;">
                        <i class="bi bi-chevron-right"></i>
                    </span>

                    {{-- Nomor & perihal --}}
                    <div style="min-width:0; flex:1;">
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            <span class="fw-semibold" style="font-size:0.83rem;">{{ $surat->nomor_surat }}</span>
                            @if($surat->gdrive_folder_id)
                                <a href="https://drive.google.com/drive/folders/{{ $surat->gdrive_folder_id }}"
                                   target="_blank" class="text-muted" style="font-size:0.72rem;"
                                   title="Buka di Google Drive" onclick="event.stopPropagation()">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            @endif
                        </div>
                        <div class="text-muted text-truncate" style="font-size:0.75rem; max-width:360px;">{{ $surat->perihal }}</div>
                    </div>

                    {{-- Struktur folder --}}
                    @php
                        $structAfterNomor = array_values(array_filter($struct, fn($l) => $l !== 'nomor_surat'));
                        $levelLabels = ['opd' => 'OPD', 'judul_permintaan' => 'List Perm.'];
                    @endphp
                    <div class="d-none d-lg-flex align-items-center gap-1">
                        <span style="font-size:0.6rem; font-weight:600; background:#4b5563; color:#fff; padding:1px 6px; border-radius:3px;">ROOT</span>
                        <i class="bi bi-chevron-right text-muted" style="font-size:0.45rem;"></i>
                        <span style="font-size:0.6rem; font-weight:600; background:#1a73e8; color:#fff; padding:1px 6px; border-radius:3px;">No.Surat</span>
                        @foreach($structAfterNomor as $lvl)
                            <i class="bi bi-chevron-right text-muted" style="font-size:0.45rem;"></i>
                            <span style="font-size:0.6rem; font-weight:500; background:#bfdbfe; color:#1e40af; padding:1px 6px; border-radius:3px;">
                                {{ $levelLabels[$lvl] ?? $lvl }}
                            </span>
                        @endforeach
                        <i class="bi bi-chevron-right text-muted" style="font-size:0.45rem;"></i>
                        <span style="font-size:0.6rem; background:#f3f4f6; color:#6b7280; padding:1px 6px; border-radius:3px; border:1px solid #e5e7eb;">
                            <i class="bi bi-file-earmark" style="font-size:0.5rem;"></i> File
                        </span>
                    </div>

                    {{-- Progress --}}
                    <div style="width:100px; flex-shrink:0;">
                        <div class="d-flex justify-content-between" style="font-size:0.68rem;">
                            <span class="{{ $pct == 100 ? 'text-success' : 'text-muted' }}">{{ $synced }}/{{ $total }}</span>
                            <span class="fw-semibold {{ $pct == 100 ? 'text-success' : 'text-primary' }}">{{ $pct }}%</span>
                        </div>
                        <div style="height:4px; background:#e9ecef; border-radius:4px; overflow:hidden;">
                            <div style="height:100%; width:{{ $pct }}%; background:{{ $pct == 100 ? '#22c55e' : '#3b82f6' }}; border-radius:4px;"></div>
                        </div>
                    </div>

                    {{-- Status badge --}}
                    @if($pct == 100)
                        <span class="badge bg-success-subtle text-success border border-success-subtle d-none d-sm-inline" style="font-size:0.68rem; white-space:nowrap;">
                            <i class="bi bi-check-all"></i> Tersync
                        </span>
                    @elseif($synced > 0)
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle d-none d-sm-inline" style="font-size:0.68rem; white-space:nowrap;">
                            Sebagian
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border d-none d-sm-inline" style="font-size:0.68rem; white-space:nowrap;">
                            Belum
                        </span>
                    @endif

                    {{-- Tombol aksi --}}
                    <div class="d-flex gap-1 flex-shrink-0" onclick="event.stopPropagation()">
                        @if($unsynced > 0)
                        <button class="btn btn-sm btn-outline-success py-0 px-2 btn-sync-surat"
                                data-surat-id="{{ $surat->id }}"
                                data-url="{{ route('google-drive.sync-surat', $surat->id) }}"
                                data-progress-url="{{ route('google-drive.progress-surat', $surat->id) }}"
                                style="font-size:0.72rem;" title="Sync dokumen belum tersync">
                            <i class="bi bi-cloud-arrow-up me-1"></i>{{ $unsynced }}
                        </button>
                        @endif
                        @if($synced > 0)
                        <form action="{{ route('google-drive.reset-sync-surat', $surat->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"
                                    style="font-size:0.72rem;" title="Reset sync surat ini"
                                    onclick="return confirm('Reset sync untuk surat {{ addslashes($surat->nomor_surat) }}?')">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </form>
                        @endif
                        <button class="btn btn-sm btn-outline-secondary py-0 px-2"
                                data-bs-toggle="modal"
                                data-bs-target="#settingModal{{ $surat->id }}"
                                style="font-size:0.72rem;" title="Pengaturan folder">
                            <i class="bi bi-gear"></i>
                        </button>
                    </div>
                </div>

                {{-- Progress bar sync inline --}}
                <div id="syncProgress{{ $surat->id }}" style="display:none;">
                    <div class="px-3 pb-2">
                        <div class="d-flex justify-content-between mb-1" style="font-size:0.72rem;">
                            <span id="syncProgressLabel{{ $surat->id }}" class="text-success fw-semibold"></span>
                            <span id="syncProgressPct{{ $surat->id }}" class="fw-bold text-primary"></span>
                        </div>
                        <div style="height:5px; background:#e0f2fe; border-radius:4px; overflow:hidden;">
                            <div id="syncProgressBar{{ $surat->id }}"
                                 style="height:100%; width:0%; background:#3b82f6; border-radius:4px; transition:width 0.2s;"></div>
                        </div>
                    </div>
                </div>

                {{-- List dokumen (collapsed by default) --}}
                <div id="dokumenList{{ $surat->id }}" style="display:none;">
                    @if($total > 0)
                    <div style="background:#fdfdfe; border-top:1px solid #f0f0f0;">
                        <table class="table table-sm mb-0" style="font-size:0.76rem;">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th class="ps-4" style="width:32px; color:#9ca3af; font-weight:500;">#</th>
                                    <th style="color:#6b7280; font-weight:500;">Nama File</th>
                                    <th class="d-none d-md-table-cell" style="color:#6b7280; font-weight:500;">OPD</th>
                                    <th class="d-none d-lg-table-cell" style="color:#6b7280; font-weight:500;">Judul Permintaan</th>
                                    <th style="width:80px; color:#6b7280; font-weight:500;">Status</th>
                                    <th class="d-none d-sm-table-cell" style="width:110px; color:#6b7280; font-weight:500;">Waktu Sync</th>
                                    <th style="width:70px; color:#6b7280; font-weight:500;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($surat->judulPermintaan as $judul)
                                    @foreach($judul->permintaanData as $perm)
                                        @foreach($perm->permintaanOpd as $opd)
                                            @foreach($opd->dokumen as $dok)
                                            <tr style="{{ $dok->gdrive_synced_at ? '' : 'background:#fffbeb;' }}">
                                                <td class="ps-4 text-muted">{{ $no++ }}</td>
                                                <td>
                                                    <i class="bi bi-file-earmark me-1 text-muted"></i>
                                                    <span title="{{ $dok->nama_file }}">{{ Str::limit($dok->nama_file, 40) }}</span>
                                                </td>
                                                <td class="text-muted d-none d-md-table-cell">{{ $opd->opd }}</td>
                                                <td class="text-muted d-none d-lg-table-cell" style="max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $perm->judul_permintaan }}">
                                                    {{ $perm->judul_permintaan }}
                                                </td>
                                                <td>
                                                    @if($dok->gdrive_synced_at)
                                                        <span class="text-success" style="font-size:0.7rem;"><i class="bi bi-cloud-check"></i> Sync</span>
                                                    @else
                                                        <span class="text-warning" style="font-size:0.7rem;"><i class="bi bi-cloud-slash"></i> Belum</span>
                                                    @endif
                                                </td>
                                                <td class="text-muted d-none d-sm-table-cell">{{ $dok->gdrive_synced_at ? $dok->gdrive_synced_at->format('d/m/y H:i') : '-' }}</td>
                                                <td>
                                                    @if(!$dok->gdrive_synced_at)
                                                    <form action="{{ route('dokumen.destroy', $dok) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Hapus dokumen ini dari daftar? File lokal juga akan dihapus.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:0.68rem;" title="Hapus dokumen">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                    @else
                                                    <span class="text-muted" style="font-size:0.68rem;">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="px-4 py-2 text-muted" style="font-size:0.78rem; background:#fdfdfe; border-top:1px solid #f0f0f0;">
                        <i class="bi bi-inbox me-1"></i>Belum ada dokumen.
                    </div>
                    @endif
                </div>
            </div>

            {{-- Modal Setting --}}
            <div class="modal fade" id="settingModal{{ $surat->id }}" tabindex="-1">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <form action="{{ route('google-drive.set-structure', $surat->id) }}" method="POST">
                            @csrf
                            <div class="modal-header py-2">
                                <h6 class="modal-title fw-bold" style="font-size:0.88rem;">
                                    <i class="bi bi-gear me-2"></i>Pengaturan Folder — {{ $surat->nomor_surat }}
                                </h6>
                                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted mb-2" style="font-size:0.82rem;">
                                    Susunan folder dimulai dari <strong>ROOT / Nomor Surat</strong>, kemudian pilih level berikutnya:
                                </p>
                                <div class="d-flex align-items-center gap-1 mb-3 flex-wrap" style="font-size:0.8rem;">
                                    <span class="badge bg-secondary px-2 py-1">ROOT</span>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                    <span class="badge bg-primary px-2 py-1">Nomor Surat</span>
                                    <i class="bi bi-chevron-right text-muted" id="arrow{{ $surat->id }}"></i>
                                    <span id="previewLevels{{ $surat->id }}" class="d-flex gap-1 flex-wrap"></span>
                                    <i class="bi bi-chevron-right text-muted" id="arrowFile{{ $surat->id }}"></i>
                                    <span class="badge bg-light text-dark border px-2 py-1">📄 File</span>
                                </div>
                                <label class="form-label fw-semibold mb-1" style="font-size:0.82rem;">Urutan level setelah Nomor Surat:</label>
                                <small class="text-muted d-block mb-2" style="font-size:0.75rem;">Centang dan urutkan dengan drag.</small>
                                <div id="sortable{{ $surat->id }}" class="border rounded p-2" style="min-height:60px; background:#fafafa;">
                                    @php
                                        $allLevels     = ['opd' => 'OPD', 'judul_permintaan' => 'List Permintaan'];
                                        $currentStruct = array_filter($stat['structure'], fn($l) => $l !== 'nomor_surat');
                                        $currentStruct = array_values($currentStruct);
                                        $otherLevels   = array_diff(array_keys($allLevels), $currentStruct);
                                        $orderedLevels = array_merge($currentStruct, array_values($otherLevels));
                                    @endphp
                                    @foreach($orderedLevels as $lvlKey)
                                    <div class="d-flex align-items-center gap-2 p-2 mb-1 bg-white border rounded drag-item"
                                         style="cursor:grab; user-select:none;" data-level="{{ $lvlKey }}">
                                        <i class="bi bi-grip-vertical text-muted"></i>
                                        <input type="checkbox" class="form-check-input level-check"
                                               name="structure[]" value="{{ $lvlKey }}"
                                               {{ in_array($lvlKey, $currentStruct) ? 'checked' : '' }}
                                               data-surat="{{ $surat->id }}"
                                               id="chk{{ $surat->id }}_{{ $lvlKey }}">
                                        <label class="form-check-label mb-0"
                                               for="chk{{ $surat->id }}_{{ $lvlKey }}"
                                               style="font-size:0.82rem; cursor:pointer;">
                                            {{ $allLevels[$lvlKey] }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="structure[]" value="nomor_surat">
                                <hr class="my-3">
                                <label class="form-label fw-semibold mb-1" style="font-size:0.82rem;">
                                    <i class="bi bi-link-45deg me-1"></i>URL Folder Nomor Surat di Google Drive
                                    <span class="text-muted fw-normal">(opsional)</span>
                                </label>
                                <input type="text" name="folder_url" class="form-control form-control-sm"
                                       value="{{ $surat->gdrive_folder_id ? 'https://drive.google.com/drive/folders/' . $surat->gdrive_folder_id : '' }}"
                                       placeholder="https://drive.google.com/drive/folders/..."
                                       style="font-size:0.82rem;">
                                <small class="text-muted" style="font-size:0.72rem;">Isi jika folder sudah ada di Drive — untuk menghindari pembuatan folder duplikat.</small>
                            </div>
                            <div class="modal-footer py-2">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-save me-1"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                <div class="mt-2" style="font-size:0.85rem;">Belum ada surat.</div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($suratStatsPaginated->hasPages())
        <div class="card-footer py-2 px-3 d-flex align-items-center justify-content-between" style="background:#f8f9fa;">
            <small class="text-muted" style="font-size:0.75rem;">
                Menampilkan {{ $suratStatsPaginated->firstItem() }}–{{ $suratStatsPaginated->lastItem() }}
                dari {{ $suratStatsPaginated->total() }} surat
            </small>
            <div>
                {{ $suratStatsPaginated->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>

</div>

{{-- Modal Konfirmasi Reset Semua (Custom Theme) --}}
<div class="modal fade" id="resetConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 py-2 px-3" style="background:linear-gradient(135deg,#1a73e8 0%,#0d47a1 100%);">
                <h6 class="modal-title text-white fw-semibold mb-0" id="resetConfirmTitle" style="font-size:0.86rem;">
                    <i class="bi bi-shield-exclamation me-2"></i>Konfirmasi Reset
                </h6>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3 pb-2 px-3">
                <div class="small text-muted mb-2" id="resetConfirmStepLabel">Langkah 1 dari 3</div>
                <div id="resetConfirmMessage" style="font-size:0.84rem; color:#374151;">
                    Anda akan mereset semua status sinkronisasi.
                </div>
                <div id="resetKeywordWrap" class="mt-3" style="display:none;">
                    <label class="form-label fw-semibold mb-1" style="font-size:0.8rem;">Ketik kata kunci <code>RESET</code> untuk melanjutkan</label>
                    <input type="text" id="resetKeywordInput" class="form-control form-control-sm" placeholder="Ketik: RESET">
                    <div id="resetKeywordError" class="text-danger mt-1" style="font-size:0.74rem; display:none;">
                        Kata kunci tidak sesuai. Harus: RESET
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="resetConfirmNextBtn" class="btn btn-sm btn-primary" onclick="handleResetConfirmStep()">
                    Lanjut
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Overlay sync semua --}}
<div id="overlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
     style="background:rgba(0,0,0,0.55); z-index:9999; display:none !important;">
    <div class="bg-white rounded-3 shadow-lg p-4 text-center" style="min-width:340px; max-width:480px;">
        <div class="mb-3">
            <i class="bi bi-cloud-arrow-up text-primary" style="font-size:2rem;"></i>
        </div>
        <h6 class="fw-bold mb-1" id="overlayTitle">Menyinkron...</h6>
        <p class="text-muted mb-3" id="overlaySub" style="font-size:0.82rem;"></p>
        <div style="height:8px; background:#e9ecef; border-radius:6px; overflow:hidden;" class="mb-2">
            <div id="overlayBar" style="height:100%; width:0%; background:#3b82f6; border-radius:6px; transition:width 0.2s;"></div>
        </div>
        <div class="d-flex justify-content-between px-1" style="font-size:0.78rem;">
            <span id="overlayDone" class="text-success fw-semibold"></span>
            <span id="overlayTotal" class="text-muted"></span>
            <span id="overlayFail" class="text-danger"></span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

let resetConfirmStep = 1;
let resetConfirmModalInstance = null;

function openResetConfirmModal() {
    resetConfirmStep = 1;
    if (!resetConfirmModalInstance) {
        resetConfirmModalInstance = new bootstrap.Modal(document.getElementById('resetConfirmModal'));
    }
    renderResetConfirmStep();
    resetConfirmModalInstance.show();
}

function renderResetConfirmStep() {
    const stepLabel = document.getElementById('resetConfirmStepLabel');
    const messageEl = document.getElementById('resetConfirmMessage');
    const keywordWrap = document.getElementById('resetKeywordWrap');
    const keywordInput = document.getElementById('resetKeywordInput');
    const keywordError = document.getElementById('resetKeywordError');
    const nextBtn = document.getElementById('resetConfirmNextBtn');

    keywordError.style.display = 'none';

    if (resetConfirmStep === 1) {
        stepLabel.textContent = 'Langkah 1 dari 3';
        messageEl.textContent = 'Anda akan mereset semua status sinkronisasi. Lanjutkan?';
        keywordWrap.style.display = 'none';
        nextBtn.textContent = 'Lanjut';
    } else if (resetConfirmStep === 2) {
        stepLabel.textContent = 'Langkah 2 dari 3';
        messageEl.textContent = 'Tindakan ini akan menandai SEMUA dokumen sebagai belum sync. Pastikan Anda benar-benar yakin.';
        keywordWrap.style.display = 'none';
        nextBtn.textContent = 'Lanjut';
    } else {
        stepLabel.textContent = 'Langkah 3 dari 3';
        messageEl.textContent = 'Konfirmasi terakhir diperlukan untuk keamanan.';
        keywordWrap.style.display = '';
        keywordInput.value = '';
        nextBtn.textContent = 'Reset Sekarang';
        setTimeout(function () { keywordInput.focus(); }, 100);
    }
}

function handleResetConfirmStep() {
    if (resetConfirmStep < 3) {
        resetConfirmStep += 1;
        renderResetConfirmStep();
        return;
    }

    const keywordInput = document.getElementById('resetKeywordInput');
    const keywordError = document.getElementById('resetKeywordError');

    if (keywordInput.value !== 'RESET') {
        keywordError.style.display = '';
        return;
    }

    resetConfirmModalInstance.hide();
    document.getElementById('resetAllSyncForm').submit();
}

// Toggle collapse dokumen per surat
function toggleDokumen(suratId) {
    const list     = document.getElementById('dokumenList' + suratId);
    const icon     = document.getElementById('toggleIcon' + suratId);
    const expanded = list.style.display !== 'none';
    list.style.display  = expanded ? 'none' : 'block';
    icon.innerHTML      = expanded
        ? '<i class="bi bi-chevron-right"></i>'
        : '<i class="bi bi-chevron-down text-primary"></i>';
}

// Preview urutan level di modal
function updatePreview(suratId) {
    const container = document.getElementById('sortable' + suratId);
    const previewEl = document.getElementById('previewLevels' + suratId);
    const arrowEl   = document.getElementById('arrow' + suratId);

    const items   = container.querySelectorAll('.drag-item');
    const checked = [];
    items.forEach(function(item) {
        const chk = item.querySelector('input[type=checkbox]');
        if (chk && chk.checked) {
            checked.push(chk.nextElementSibling.textContent.trim());
        }
    });

    previewEl.innerHTML = '';
    arrowEl.style.display = checked.length === 0 ? 'none' : '';
    checked.forEach(function(label, i) {
        const badge = document.createElement('span');
        badge.className = 'badge bg-info-subtle text-info border border-info-subtle px-2 py-1';
        badge.style.fontSize = '0.7rem';
        badge.textContent = label;
        previewEl.appendChild(badge);
        if (i < checked.length - 1) {
            const arr = document.createElement('i');
            arr.className = 'bi bi-chevron-right text-muted';
            arr.style.fontSize = '0.55rem';
            previewEl.appendChild(arr);
        }
    });
}

// Init preview & drag untuk setiap modal
@foreach($suratStatsPaginated as $stat)
@php $sid = $stat['surat']->id; @endphp
(function() {
    const suratId   = {{ $sid }};
    const container = document.getElementById('sortable' + suratId);
    if (!container) return;

    updatePreview(suratId);
    container.querySelectorAll('input[type=checkbox]').forEach(function(chk) {
        chk.addEventListener('change', function() { updatePreview(suratId); });
    });

    let dragging = null;
    container.querySelectorAll('.drag-item').forEach(function(item) {
        item.setAttribute('draggable', 'true');
        item.addEventListener('dragstart', function() { dragging = item; item.style.opacity = '0.4'; });
        item.addEventListener('dragend',   function() { item.style.opacity = '1'; dragging = null; updatePreview(suratId); });
        item.addEventListener('dragover',  function(e) {
            e.preventDefault();
            if (!dragging || dragging === item) return;
            const mid = item.getBoundingClientRect().top + item.getBoundingClientRect().height / 2;
            container.insertBefore(dragging, e.clientY < mid ? item : item.nextSibling);
        });
    });
})();
@endforeach

function showErrorModal(errors) {
    const existing = document.getElementById('errorSyncModal');
    if (existing) existing.remove();

    const html = `
    <div class="modal fade" id="errorSyncModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2 bg-danger text-white">
                    <h6 class="modal-title fw-bold mb-0" style="font-size:0.88rem;">
                        <i class="bi bi-exclamation-triangle me-2"></i>Detail Error Upload (${errors.length} file gagal)
                    </h6>
                    <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <ul class="list-group list-group-flush" style="font-size:0.8rem; max-height:400px; overflow-y:auto;">
                        ${errors.map(function(e) {
                            return '<li class="list-group-item py-2 px-3"><i class="bi bi-file-earmark-x text-danger me-2"></i>' + e + '</li>';
                        }).join('')}
                    </ul>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', html);
    const modal = new bootstrap.Modal(document.getElementById('errorSyncModal'));
    modal.show();
}

// Sync per surat (one-by-one dengan progress bar inline)
document.querySelectorAll('.btn-sync-surat').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const suratId     = btn.dataset.suratId;
        const progressUrl = btn.dataset.progressUrl;
        const progressEl  = document.getElementById('syncProgress' + suratId);
        const barEl       = document.getElementById('syncProgressBar' + suratId);
        const labelEl     = document.getElementById('syncProgressLabel' + suratId);
        const pctEl       = document.getElementById('syncProgressPct' + suratId);

        btn.disabled = true;

        fetch(progressUrl)
            .then(function(r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function(info) {
                const ids = info.belum_ids;
                if (!ids || ids.length === 0) { location.reload(); return; }

                progressEl.style.display = 'block';
                barEl.style.width = '0%';
                labelEl.textContent = 'Memulai upload...';
                pctEl.textContent = '0%';

                let done = 0, fail = 0, errors = [];

                function next(idx) {
                    if (idx >= ids.length) {
                        barEl.style.width = '100%';
                        pctEl.textContent = '100%';
                        labelEl.textContent = done + ' berhasil' + (fail > 0 ? ', ' + fail + ' gagal' : '');
                        if (errors.length > 0) {
                            setTimeout(function() { showErrorModal(errors); location.reload(); }, 1500);
                        } else {
                            setTimeout(function() { location.reload(); }, 1500);
                        }
                        return;
                    }
                    const url = '{{ url('/google-drive/sync-one') }}/' + ids[idx];
                    fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                        body: JSON.stringify({})
                    })
                    .then(function(r) { return r.ok ? r.json() : Promise.reject('HTTP ' + r.status); })
                    .then(function(data) {
                        if (data.success) {
                            done++;
                        } else {
                            fail++;
                            errors.push(data.message || 'Error tidak diketahui (ID: ' + ids[idx] + ')');
                        }
                    })
                    .catch(function(err) {
                        fail++;
                        errors.push('Request gagal (ID: ' + ids[idx] + '): ' + (err.message || err));
                    })
                    .finally(function() {
                        const pct = Math.round((idx + 1) / ids.length * 100);
                        barEl.style.width   = pct + '%';
                        pctEl.textContent   = pct + '%';
                        labelEl.textContent = (idx + 1) + ' / ' + ids.length + ' dokumen' + (fail > 0 ? ' (' + fail + ' gagal)' : '');
                        next(idx + 1);
                    });
                }
                next(0);
            })
            .catch(function(err) {
                btn.disabled = false;
                alert('Gagal memulai sync: ' + (err.message || err));
            });
    });
});

// Sync semua
document.getElementById('btnSyncAll').addEventListener('click', function () {
    if (!confirm('Sync semua dokumen yang belum tersinkron?')) return;

    fetch('{{ route('google-drive.progress') }}')
        .then(function(r) { return r.json(); })
        .then(function(info) {
            const ids = info.belum_ids;
            if (ids.length === 0) { alert('Semua dokumen sudah tersinkron.'); return; }

            const overlay = document.getElementById('overlay');
            const bar     = document.getElementById('overlayBar');
            const donEl   = document.getElementById('overlayDone');
            const failEl  = document.getElementById('overlayFail');
            const totalEl = document.getElementById('overlayTotal');

            document.getElementById('overlayTitle').textContent = 'Menyinkron ke Google Drive...';
            document.getElementById('overlaySub').textContent   = 'Jangan tutup halaman ini.';
            overlay.style.display = 'flex';
            totalEl.textContent   = ids.length + ' dokumen';

            let done = 0, fail = 0, errors = [];

            function next(idx) {
                if (idx >= ids.length) {
                    bar.style.width = '100%';
                    document.getElementById('overlayTitle').textContent = fail === 0 ? 'Selesai!' : 'Selesai dengan ' + fail + ' gagal';
                    setTimeout(function() {
                        overlay.style.display = 'none';
                        if (errors.length > 0) showErrorModal(errors);
                        location.reload();
                    }, 2000);
                    return;
                }
                const url = '{{ url('/google-drive/sync-one') }}/' + ids[idx];
                fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                    body: JSON.stringify({})
                })
                .then(function(r) { return r.ok ? r.json() : Promise.reject('HTTP ' + r.status); })
                .then(function(data) {
                    if (data.success) {
                        done++;
                    } else {
                        fail++;
                        errors.push(data.message || 'Error tidak diketahui (ID: ' + ids[idx] + ')');
                    }
                })
                .catch(function(err) {
                    fail++;
                    errors.push('Request gagal (ID: ' + ids[idx] + '): ' + (err.message || err));
                })
                .finally(function() {
                    const pct = Math.round((idx + 1) / ids.length * 100);
                    bar.style.width    = pct + '%';
                    donEl.textContent  = done + ' berhasil';
                    failEl.textContent = fail > 0 ? fail + ' gagal' : '';
                    next(idx + 1);
                });
            }
            next(0);
        });
});
</script>
@endsection
