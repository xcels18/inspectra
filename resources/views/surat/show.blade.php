@php $daftarOpd = App\Models\PermintaanData::opsiOpd(); @endphp
@extends('layouts.app')
@section('title', 'Detail Surat')
@section('page-title', 'Detail Surat Permintaan')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
.ts-wrapper.multi .ts-control { min-height: 34px; font-size: 0.82rem; }
.ts-dropdown { font-size: 0.82rem; }
.toggle-chevron { transition: transform 0.2s ease; display: inline-block; }
.toggle-chevron.rotate-180 { transform: rotate(180deg); }
.items-loading { color:#6b7280; font-size:0.8rem; padding:1rem 1.5rem; }
</style>
@endsection

@section('content')
<div class="d-flex gap-2 mb-3">
    <a href="{{ route('surat.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('surat.export-excel', $surat) }}" class="btn btn-sm btn-outline-success ms-auto">
        <i class="bi bi-file-earmark-excel"></i> Cetak Laporan Excel
    </a>
    <a href="{{ route('surat.edit', $surat) }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-pencil"></i> Edit Surat
    </a>
    @endif
</div>

@php
    $opdPct = $opdTotal > 0 ? round((($opdSelesai + $opdProses) / $opdTotal) * 100) : 0;
    $deadlineDays = $surat->deadline ? now()->startOfDay()->diffInDays($surat->deadline->startOfDay(), false) : null;
@endphp

<div class="card mb-3" style="border:none; box-shadow: 0 1px 6px rgba(0,0,0,0.07); border-radius: 10px; overflow:hidden;">
    <div style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); padding: 1.1rem 1.5rem; position:relative;">
        <div style="position:absolute; top:0; right:0; bottom:0; left:0; background:radial-gradient(circle at top right, rgba(255,255,255,0.06), transparent 60%); pointer-events:none;"></div>
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap position-relative z-1">
            <div>
                <div style="font-size:0.72rem; color:#94a3b8; letter-spacing:0.06em; text-transform:uppercase; margin-bottom:2px;">Nomor Surat</div>
                <div style="font-size:1.15rem; font-weight:700; color:#fff;">{{ $surat->nomor_surat }}</div>
                <div style="font-size:0.82rem; color:#e2e8f0; margin-top:3px;">{{ $surat->perihal }}</div>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <span class="badge bg-white" style="color:#0b192c; font-size:0.75rem; padding:5px 12px;">{{ $surat->tahun_anggaran }}</span>
                <span class="badge bg-{{ $surat->status_badge }}" style="font-size:0.75rem; padding:5px 12px;">{{ $surat->status_label }}</span>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="row g-0">
            <div class="col-lg-8" style="border-right: 1px solid #f3f4f6;">
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div style="font-size:0.72rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:3px;">Tanggal Surat</div>
                            <div style="font-size:0.85rem; font-weight:600; color:#1f2937;">{{ $surat->tanggal_surat->format('d M Y') }}</div>
                        </div>
                        <div class="col-sm-4">
                            <div style="font-size:0.72rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:3px;">Tanggal Terima</div>
                            <div style="font-size:0.85rem; font-weight:600; color:#1f2937;">{{ $surat->tanggal_terima->format('d M Y') }}</div>
                        </div>
                        <div class="col-sm-4">
                            <div style="font-size:0.72rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:3px;">Deadline</div>
                            @if($surat->deadline)
                                @php
                                    $dlColor = $deadlineDays < 0 ? '#dc2626' : ($deadlineDays <= 7 ? '#d97706' : '#16a34a');
                                    $dlLabel = $deadlineDays < 0 ? abs($deadlineDays).' hari lalu' : ($deadlineDays === 0 ? 'Hari ini' : $deadlineDays.' hari lagi');
                                @endphp
                                <div style="font-size:0.85rem; font-weight:600; color:#1f2937;">{{ $surat->deadline->format('d M Y') }}</div>
                                <div style="font-size:0.7rem; color:{{ $dlColor }}; font-weight:500;">{{ $dlLabel }}</div>
                            @else
                                <div style="font-size:0.85rem; color:#9ca3af;">—</div>
                            @endif
                        </div>
                        <div class="col-sm-4">
                            <div style="font-size:0.72rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:3px;">Diinput oleh</div>
                            <div style="font-size:0.85rem; color:#1f2937;"><i class="bi bi-person me-1"></i>{{ $surat->pembuat->name }}</div>
                        </div>
                        @if($surat->file_surat)
                        <div class="col-sm-4">
                            <div style="font-size:0.72rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:3px;">File Surat</div>
                            <a href="{{ route('surat.download-file', $surat) }}" target="_blank" style="font-size:0.82rem;" class="text-decoration-none">
                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i>Lihat PDF
                            </a>
                        </div>
                        @endif
                    </div>
                    @if($surat->keterangan)
                    <div class="mt-3 p-3 rounded-2" style="background:#f8fafc; border-left: 3px solid #2563eb;">
                        <div style="font-size:0.72rem; color:#6b7280; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:4px;"><i class="bi bi-info-circle me-1"></i>Keterangan</div>
                        <div style="font-size:0.85rem; color:#374151; white-space: pre-line;">{{ $surat->keterangan }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="p-4 text-center h-100 d-flex flex-column justify-content-center">
                    <div style="font-size:0.72rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:10px;">Progress Pengumpulan</div>
                    <div style="font-size:2.5rem; font-weight:800; color:{{ $opdPct === 100 ? '#16a34a' : '#1a3a6b' }}; line-height:1;">{{ $opdPct }}%</div>
                    <div style="height:8px; background:#f3f4f6; border-radius:99px; overflow:hidden; margin:10px 0;">
                        <div style="height:100%; width:{{ $opdPct }}%; background:{{ $opdPct === 100 ? '#22c55e' : '#2563eb' }}; border-radius:99px; transition:width 0.5s;"></div>
                    </div>
                    <div style="font-size:0.75rem; color:#6b7280; margin-bottom:14px;">{{ $opdSelesai }} dari {{ $opdTotal }} penugasan OPD selesai</div>
                    <div class="d-flex gap-2 justify-content-center">
                        <div class="rounded-2 px-2 py-1 text-center flex-fill" style="background:#fee2e2;">
                            <div style="font-size:1rem; font-weight:700; color:#dc2626;">{{ $opdBelum }}</div>
                            <div style="font-size:0.65rem; color:#9ca3af;">Belum</div>
                        </div>
                        <div class="rounded-2 px-2 py-1 text-center flex-fill" style="background:#fef3c7;">
                            <div style="font-size:1rem; font-weight:700; color:#d97706;">{{ $opdProses }}</div>
                            <div style="font-size:0.65rem; color:#9ca3af;">Proses</div>
                        </div>
                        <div class="rounded-2 px-2 py-1 text-center flex-fill" style="background:#dcfce7;">
                            <div style="font-size:1rem; font-weight:700; color:#16a34a;">{{ $opdSelesai }}</div>
                            <div style="font-size:0.65rem; color:#9ca3af;">Selesai</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toolbar pencarian list data --}}
<div class="card mb-2 border-0 shadow-sm" style="border-radius:8px;">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="input-group input-group-sm" style="max-width:320px;">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchListData" class="form-control" placeholder="Cari list data..." style="font-size:0.82rem;" autocomplete="off">
            </div>
            <div id="searchInfo" class="text-muted d-none" style="font-size:0.78rem;"></div>
        </div>
    </div>
</div>

<div class="card" style="border:0; box-shadow:0 1px 4px rgba(0,0,0,0.07); border-radius:10px; overflow:hidden;">
    <div class="card-header d-flex justify-content-between align-items-center"
         style="background:#f8f9fa; border-bottom:1px solid #e9ecef; padding:0.65rem 1rem;">
        <span class="fw-semibold" style="font-size:0.85rem; color:#374151;">
            <i class="bi bi-list-check me-2 text-primary"></i>Daftar Permintaan Data
        </span>
        <div class="d-flex gap-1 align-items-center">
            @if(auth()->user()->isAdmin())
            <button class="btn btn-sm py-0 px-2" type="button" id="btnBulkAssignOpd"
                    style="background:#ecfeff; color:#0e7490; border:1px solid #a5f3fc; font-size:0.75rem;"
                    data-bs-toggle="modal" data-bs-target="#modalBulkAssignOpd" disabled>
                <i class="bi bi-diagram-3 me-1"></i>Tandai ke OPD (<span id="bulkSelectedCount">0</span>)
            </button>
            <button class="btn btn-sm py-0 px-2" type="button" id="btnBulkUpdateStatus"
                    style="background:#fef3c7; color:#b45309; border:1px solid #fde68a; font-size:0.75rem;"
                    data-bs-toggle="modal" data-bs-target="#modalBulkUpdateStatus" disabled>
                <i class="bi bi-check2-square me-1"></i>Ubah Status Massal (<span class="js-bulk-status-selected-count">0</span>)
            </button>
            @endif
            <button class="btn btn-sm py-0 px-2" id="btnBukaSemuaJudul" onclick="toggleSemuaJudul(true)"
                    style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.75rem;">
                <i class="bi bi-chevron-down me-1"></i>Buka Semua
            </button>
            <button class="btn btn-sm py-0 px-2" id="btnTutupSemuaJudul" onclick="toggleSemuaJudul(false)"
                    style="background:#f9fafb; color:#6b7280; border:1px solid #e5e7eb; font-size:0.75rem; display:none;">
                <i class="bi bi-chevron-up me-1"></i>Tutup Semua
            </button>
            @if(auth()->user()->isAdmin())
            <button class="btn btn-sm py-0 px-2" data-bs-toggle="modal" data-bs-target="#modalTambahJudul"
                    style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; font-size:0.75rem;">
                <i class="bi bi-plus-lg me-1"></i>Tambah Judul
            </button>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        @forelse($surat->judulPermintaan as $judul)
        @php $judulCollapseId = 'judul-collapse-' . $judul->id; @endphp
        <div class="border-bottom judul-block" data-judul-id="{{ $judul->id }}">
            {{-- Header Judul --}}
            <div class="d-flex justify-content-between align-items-center px-3 py-2 judul-header"
                 style="background:#f0f4ff; cursor:pointer; border-top:{{ $loop->first ? '0' : '2px solid #c7d2fe' }};"
                 data-judul-id="{{ $judul->id }}"
                 data-judul-url="{{ route('judul-permintaan.items', $judul) }}"
                 role="button"
                 tabindex="0"
                 aria-expanded="false"
                 aria-controls="{{ $judulCollapseId }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-chevron-right judul-chevron" style="font-size:0.7rem; color:#1d4ed8; transition:transform 0.2s;"></i>
                    <i class="bi bi-folder2-open" style="font-size:0.8rem; color:#1d4ed8;"></i>
                    <span class="fw-bold" style="font-size:0.82rem; color:#1d4ed8; text-transform:uppercase; letter-spacing:0.04em;">
                        {{ $loop->iteration }}. {{ $judul->judul }}
                    </span>
                    <span class="judul-count-badge" style="font-size:0.65rem; background:#e0e7ff; color:#3730a3; padding:1px 7px; border-radius:999px;">
                        {{ $judul->permintaanData()->count() }} item
                    </span>
                </div>
                @if(auth()->user()->isAdmin())
                <div class="d-flex gap-1" onclick="event.stopPropagation()">
                    <button class="btn btn-sm py-0 px-2"
                            style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.7rem;"
                            data-bs-toggle="modal" data-bs-target="#modalTambahList"
                            data-judul-id="{{ $judul->id }}"
                            data-judul-nama="{{ $judul->judul }}">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                    <button class="btn btn-sm py-0 px-2"
                            style="background:#f9fafb; color:#6b7280; border:1px solid #e5e7eb; font-size:0.7rem;"
                            data-bs-toggle="modal" data-bs-target="#modalEditJudul"
                            data-judul-id="{{ $judul->id }}"
                            data-judul-nama="{{ $judul->judul }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('judul-permintaan.destroy', $judul) }}" method="POST" class="mb-0"
                          onsubmit="return confirm('Hapus judul beserta semua isinya?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm py-0 px-2"
                                style="background:#fff5f5; color:#dc2626; border:1px solid #fecaca; font-size:0.7rem;">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>

            {{-- Container items --}}
            <div class="judul-items-wrap" id="{{ $judulCollapseId }}" style="display:none;">
                <div class="ps-3 items-list-container">
                    @if($isTimBpk)
                        @php
                            $judulItems = $judulInitialItems->get($judul->id, collect());
                            $items = new \Illuminate\Pagination\LengthAwarePaginator(
                                $judulItems->values(),
                                $judulItems->count(),
                                max(1, $judulItems->count()),
                                1,
                                ['path' => request()->url(), 'pageName' => 'page']
                            );
                            $isAdmin = false;
                            $daftarOpd = \App\Models\PermintaanData::opsiOpd();
                            $users = \App\Models\User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'role']);
                        @endphp
                        @include('surat._judul_items', ['items' => $items, 'isAdmin' => $isAdmin, 'daftarOpd' => $daftarOpd, 'users' => $users, 'judulPermintaan' => $judul])
                    @endif
                </div>
                <div class="items-pagination px-3 pb-2 pt-1 d-flex align-items-center justify-content-between gap-2" style="{{ $isAdmin ? 'display:none;' : 'display:none !important;' }}"></div>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox" style="font-size:2rem;"></i>
            <div class="mt-2" style="font-size:0.85rem;">Belum ada item permintaan</div>
        </div>
        @endforelse
    </div>
</div>

{{-- Modal Upload Dokumen --}}
@if(auth()->user()->isAdmin())
<div class="modal fade" id="modalUpload" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-upload me-2"></i>Upload Dokumen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUpload" action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="permintaan_opd_id" id="upload-opd-id">
                <div class="modal-body">
                    <div class="mb-2 small text-muted">OPD: <span id="upload-opd-nama" class="fw-semibold text-dark"></span></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File <span class="text-danger">*</span></label>
                        <input type="file" name="file[]" id="uploadFileInput" class="form-control" multiple required>
                        <div class="form-text">Maks. 100 file sekaligus, ukuran per file maks. 500MB.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Keterangan dokumen (opsional)">
                    </div>

                    {{-- Progress bar (tersembunyi saat idle) --}}
                    <div id="uploadProgressWrap" style="display:none;">
                        <div class="d-flex justify-content-between mb-1" style="font-size:0.78rem;">
                            <span class="fw-semibold text-primary"><i class="bi bi-cloud-arrow-up me-1"></i>Mengupload...</span>
                            <span id="uploadPct" class="fw-semibold">0%</span>
                        </div>
                        <div class="progress mb-1" style="height:10px; border-radius:6px;">
                            <div id="uploadBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                 style="width:0%; transition: width 0.3s;"></div>
                        </div>
                        <div id="uploadInfo" class="text-muted" style="font-size:0.72rem;">Mohon tunggu, jangan tutup modal ini.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" id="btnBatalUpload" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSubmitUpload">
                        <i class="bi bi-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Modal Update Status --}}
<div class="modal fade" id="modalUpdateStatus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Update Permintaan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUpdateStatus" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Permintaan</label>
                        <input type="text" name="judul_permintaan" id="update-judul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" id="update-deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" id="update-status" class="form-select" required>
                                <option value="belum">Belum</option>
                                <option value="proses">Sedang Diproses</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">OPD</label>
                        <select name="opd[]" id="update-opd" class="form-select" multiple>
                            @foreach($daftarOpd as $opd)
                            <option value="{{ $opd }}">{{ $opd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Penanggung Jawab</label>
                        <select name="penanggung_jawab" id="update-pj" class="form-select">
                            <option value="">- Pilih PJ -</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role_label }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="catatan" id="update-catatan" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tambah Judul --}}
<div class="modal fade" id="modalTambahJudul" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Judul Permintaan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('judul-permintaan.store') }}" method="POST">
                @csrf
                <input type="hidden" name="surat_id" value="{{ $surat->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" placeholder="Judul permintaan data..." required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Judul --}}
<div class="modal fade" id="modalEditJudul" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Judul Permintaan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditJudul" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="edit-judul-nama" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tambah List Data --}}
<div class="modal fade" id="modalTambahList" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah List Data</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('permintaan.store') }}" method="POST">
                @csrf
                <input type="hidden" name="surat_id" value="{{ $surat->id }}">
                <input type="hidden" name="judul_permintaan_id" id="tambah-list-judul-id">
                <div class="modal-body">
                    <div class="mb-2">
                        <div class="text-muted small">Judul: <span id="tambah-list-judul-nama" class="fw-semibold text-dark"></span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Data yang Diminta <span class="text-danger">*</span></label>
                        <input type="text" name="judul_permintaan" class="form-control" placeholder="Nama data yang diminta..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">OPD <span class="text-danger">*</span></label>
                        <select name="opd[]" id="select-opd-tambah" class="form-select" multiple required>
                            @foreach($daftarOpd as $opd)
                            <option value="{{ $opd }}">{{ $opd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Deskripsi (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Modal Bulk Assign OPD --}}
<div class="modal fade" id="modalBulkAssignOpd" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-diagram-3 me-2"></i>Tandai Massal ke OPD</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formBulkAssignOpd" action="{{ route('permintaan.bulk-assign-opd') }}" method="POST">
                @csrf
                <input type="hidden" name="surat_id" value="{{ $surat->id }}">
                <div id="bulkPermintaanIdsContainer"></div>
                <div class="modal-body">
                    <div class="mb-2 small text-muted">
                        Item terpilih: <span class="fw-semibold text-dark" id="bulkSelectedCountModal">0</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih OPD <span class="text-danger">*</span></label>
                        <select name="opd[]" id="bulk-opd" class="form-select" multiple required>
                            @foreach($daftarOpd as $opd)
                            <option value="{{ $opd }}">{{ $opd }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Bisa pilih beberapa OPD sekaligus.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-save"></i> Simpan Penandaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Bulk Update Status --}}
<div class="modal fade" id="modalBulkUpdateStatus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-check2-square me-2"></i>Ubah Status Massal</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formBulkUpdateStatus" action="{{ route('permintaan-opd.bulk-update.post') }}" method="POST">
                @csrf
                @method('PUT')
                <div id="bulkStatusPermintaanIdsContainer"></div>
                <div class="modal-body">
                    <div class="mb-3 small text-muted">
                        Item terpilih: <span class="fw-semibold text-dark js-bulk-status-selected-count-modal">0</span> item
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Baru <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="">-- Pilih Status Baru --</option>
                            <option value="belum">🔴 Belum Ada</option>
                            <option value="proses">🟡 Sedang Diproses</option>
                            <option value="selesai">🟢 Selesai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan (Opsional)</label>
                        <input type="text" name="catatan" class="form-control" placeholder="Catatan untuk semua item terpilih">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm fw-bold"><i class="bi bi-check-lg me-1"></i>Terapkan ke Semua</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
const tsOpts = { plugins: ['remove_button'], maxOptions: null, placeholder: 'Pilih OPD...' };
const tsUpdateOpd = new TomSelect('#update-opd', tsOpts);
const tsTambahOpd = new TomSelect('#select-opd-tambah', tsOpts);
let tsBulkOpd = null;
if (document.getElementById('bulk-opd')) {
    tsBulkOpd = new TomSelect('#bulk-opd', tsOpts);
}

// ── Lazy load per judul ──────────────────────────────────────────────────────
const judulState = {}; // { judulId: { loaded: bool, page: int, lastPage: int } }

function setupBulkSelectionHandlers(scope = document) {
    const checkboxes = scope.querySelectorAll('.js-bulk-item-checkbox');
    checkboxes.forEach(function (cb) {
        if (cb.dataset.bulkBound === '1') return;
        cb.dataset.bulkBound = '1';
        cb.addEventListener('change', updateBulkSelectionUI);
    });
}

function getSelectedPermintaanIds() {
    return Array.from(document.querySelectorAll('.js-bulk-item-checkbox:checked')).map(cb => cb.value);
}

function updateBulkSelectionUI() {
    const selectedIds = getSelectedPermintaanIds();
    const count = selectedIds.length;
    const btnBulk = document.getElementById('btnBulkAssignOpd');
    const btnBulkStatus = document.getElementById('btnBulkUpdateStatus');
    const countBtn = document.getElementById('bulkSelectedCount');
    const countModal = document.getElementById('bulkSelectedCountModal');

    document.querySelectorAll('.js-bulk-status-selected-count').forEach(el => el.textContent = String(count));
    document.querySelectorAll('.js-bulk-status-selected-count-modal').forEach(el => el.textContent = String(count));

    if (countBtn) countBtn.textContent = String(count);
    if (countModal) countModal.textContent = String(count);
    if (btnBulk) btnBulk.disabled = count === 0;
    if (btnBulkStatus) btnBulkStatus.disabled = count === 0;
}

function loadJudulItems(judulId, page, search = '') {
    const block    = document.querySelector(`.judul-block[data-judul-id="${judulId}"]`);
    const header   = block.querySelector('.judul-header');
    const wrap     = block.querySelector('.judul-items-wrap');
    const listEl   = wrap.querySelector('.items-list-container');
    const pagEl    = wrap.querySelector('.items-pagination');
    const url      = header.dataset.judulUrl;

    listEl.innerHTML = '<div class="items-loading"><span class="spinner-border spinner-border-sm me-2"></span>Memuat data...</div>';

    const params = new URLSearchParams({ page: String(page) });
    if (search && search.trim() !== '') {
        params.set('search', search.trim());
    }

    return fetch(`${url}?${params.toString()}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(res => {
            listEl.innerHTML = res.html;
            judulState[judulId] = { loaded: true, page: res.current_page, lastPage: res.last_page, total: res.total };

            // Pagination controls
            if (res.last_page > 1) {
                pagEl.style.removeProperty('display');
                renderPagination(pagEl, judulId, res, search);
            } else {
                pagEl.style.display = 'none';
            }

            // Info di badge
            const info = block.querySelector('.judul-count-badge');
            if (info) info.textContent = `${res.total} item`;

            // re-bind toggle chevron pada item OPD yang baru di-render
            listEl.querySelectorAll('[data-bs-toggle="collapse"]').forEach(bindToggleChevron);

            // bind checkbox bulk selection pada item hasil lazy-load
            setupBulkSelectionHandlers(listEl);
            updateBulkSelectionUI();


        })
        .catch(() => {
            listEl.innerHTML = '<div class="items-loading text-danger">Gagal memuat data.</div>';
            throw new Error('Gagal memuat data judul');
        });
}

function renderPagination(pagEl, judulId, res, search = '') {
    const from = res.from ?? '-';
    const to   = res.to   ?? '-';

    pagEl.innerHTML = `
        <span style="font-size:0.75rem; color:#6b7280;">Menampilkan ${from}–${to} dari ${res.total} item</span>
        <div class="d-flex gap-1">
            <button type="button" class="btn btn-sm py-0 px-2 js-page-prev" style="font-size:0.72rem; background:#f9fafb; border:1px solid #e5e7eb;"
                ${res.current_page <= 1 ? 'disabled' : ''}>
                <i class="bi bi-chevron-left"></i>
            </button>
            <span style="font-size:0.75rem; padding:2px 8px; background:#eff6ff; color:#1d4ed8; border-radius:4px; border:1px solid #bfdbfe;">
                ${res.current_page} / ${res.last_page}
            </span>
            <button type="button" class="btn btn-sm py-0 px-2 js-page-next" style="font-size:0.72rem; background:#f9fafb; border:1px solid #e5e7eb;"
                ${res.current_page >= res.last_page ? 'disabled' : ''}>
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>`;

    const prevBtn = pagEl.querySelector('.js-page-prev');
    const nextBtn = pagEl.querySelector('.js-page-next');

    if (prevBtn) {
        prevBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (res.current_page > 1) {
                loadJudulItems(judulId, res.current_page - 1, search);
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (res.current_page < res.last_page) {
                loadJudulItems(judulId, res.current_page + 1, search);
            }
        });
    }
}

// ── Toggle judul (accordion custom) ─────────────────────────────────────────
function toggleJudulByHeader(headerEl) {
    if (!headerEl) return;
    const judulId = headerEl.dataset.judulId;
    const block   = headerEl.closest('.judul-block');
    const wrap    = block ? block.querySelector('.judul-items-wrap') : null;
    const chevron = headerEl.querySelector('.judul-chevron');
    if (!wrap) return;

    const isOpen  = wrap.style.display !== 'none';
    let openAccordions = JSON.parse(sessionStorage.getItem('openAccordions_surat_{{ $surat->id }}') || '[]');

    if (isOpen) {
        wrap.style.display = 'none';
        if (chevron) chevron.style.transform = 'rotate(0deg)';
        headerEl.setAttribute('aria-expanded', 'false');
        openAccordions = openAccordions.filter(id => id !== judulId);
    } else {
        wrap.style.display = '';
        if (chevron) chevron.style.transform = 'rotate(90deg)';
        headerEl.setAttribute('aria-expanded', 'true');
        if (!openAccordions.includes(judulId)) {
            openAccordions.push(judulId);
        }

        const listEl = wrap.querySelector('.items-list-container');
        const hasRenderedItem = !!(listEl && listEl.querySelector('[data-item-id]'));

        // Jika belum pernah load ATAU wrapper sudah dibuka tapi list masih kosong,
        // paksa reload untuk mengatasi kasus BPK preview yang tidak render item.
        if (!judulState[judulId]?.loaded || !hasRenderedItem) {
            loadJudulItems(judulId, 1);
        }
    }
    sessionStorage.setItem('openAccordions_surat_{{ $surat->id }}', JSON.stringify(openAccordions));
}

document.querySelectorAll('.judul-header').forEach(function(header) {
    header.addEventListener('click', function(e) {
        if (e.target.closest('button, a, form, input, select, textarea, .modal, [data-bs-toggle]')) {
            return;
        }
        toggleJudulByHeader(this);
    });

    header.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggleJudulByHeader(this);
        }
    });
});

// Restore accordion state
window.addEventListener('load', function() {
    let openAccordions = JSON.parse(sessionStorage.getItem('openAccordions_surat_{{ $surat->id }}') || '[]');
    openAccordions.forEach(judulId => {
        const headerEl = document.querySelector(`.judul-header[data-judul-id="${judulId}"]`);
        if (headerEl) {
            const block   = headerEl.closest('.judul-block');
            const wrap    = block ? block.querySelector('.judul-items-wrap') : null;
            if (wrap && wrap.style.display === 'none') {
                toggleJudulByHeader(headerEl);
            }
        }
    });
});

function toggleSemuaJudul(buka) {
    let openAccordions = [];
    document.querySelectorAll('.judul-block').forEach(function(block) {
        const judulId = block.dataset.judulId;
        const wrap    = block.querySelector('.judul-items-wrap');
        const chevron = block.querySelector('.judul-chevron');
        if (buka) {
            wrap.style.display = '';
            chevron.style.transform = 'rotate(90deg)';
            openAccordions.push(judulId);
            if (!@json($isTimBpk) && !judulState[judulId]?.loaded) {
                loadJudulItems(judulId, 1);
            }
        } else {
            wrap.style.display = 'none';
            chevron.style.transform = 'rotate(0deg)';
        }
    });
    sessionStorage.setItem('openAccordions_surat_{{ $surat->id }}', JSON.stringify(openAccordions));
    document.getElementById('btnBukaSemuaJudul').style.display = buka ? 'none' : '';
    document.getElementById('btnTutupSemuaJudul').style.display = buka ? '' : 'none';
}

// ── Toggle chevron OPD collapse ──────────────────────────────────────────────
function bindToggleChevron(btn) {
    const targetId = btn.dataset.bsTarget;
    if (!targetId) return;
    const target = document.querySelector(targetId);
    if (!target) return;
    target.addEventListener('show.bs.collapse', function() {
        const ch = btn.querySelector('.toggle-chevron');
        if (ch) ch.classList.add('rotate-180');
    });
    target.addEventListener('hide.bs.collapse', function() {
        const ch = btn.querySelector('.toggle-chevron');
        if (ch) ch.classList.remove('rotate-180');
    });
}
document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(bindToggleChevron);
setupBulkSelectionHandlers(document);
updateBulkSelectionUI();

// ── Search list data ─────────────────────────────────────────────────────────
let searchTimeout;
document.getElementById('searchListData').addEventListener('input', function() {
    if (@json($isTimBpk)) return;
    clearTimeout(searchTimeout);
    const q = this.value.trim().toLowerCase();
    searchTimeout = setTimeout(function() {
        let pending = 0;
        let totalVisible = 0;
        const blocks = document.querySelectorAll('.judul-block');

        if (!q) {
            blocks.forEach(function(block) {
                const listEl = block.querySelector('.items-list-container');
                const cnt = filterInBlock(listEl, '');
                totalVisible += cnt;
            });
            const info = document.getElementById('searchInfo');
            info.classList.add('d-none');
            return;
        }

        blocks.forEach(function(block) {
            const wrap   = block.querySelector('.judul-items-wrap');
            const chevron = block.querySelector('.judul-chevron');
            const judulId = block.dataset.judulId;

            wrap.style.display = '';
            chevron.style.transform = 'rotate(90deg)';
            pending++;

            const done = function() {
                const listEl = block.querySelector('.items-list-container');
                totalVisible += listEl.querySelectorAll('[data-item-id]').length;
                pending--;
                if (pending === 0) {
                    const info = document.getElementById('searchInfo');
                    info.textContent = `${totalVisible} hasil ditemukan`;
                    info.classList.remove('d-none');
                }
            };

            loadJudulItems(judulId, 1, q)
                .then(done)
                .catch(done);
        });
    }, 300);
});

function filterInBlock(listEl, q) {
    let visible = 0;
    listEl.querySelectorAll('[data-item-id]').forEach(function(row) {
        const text = row.textContent.toLowerCase();
        const show = !q || text.includes(q);
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    return visible;
}

// ── Modal Upload ─────────────────────────────────────────────────────────────
const formUpload = document.getElementById('formUpload');
if (formUpload) {
    formUpload.addEventListener('submit', function(e) {
        e.preventDefault();
        const fileInput = document.getElementById('uploadFileInput');
        if (!fileInput.files.length) return;
        const progressWrap = document.getElementById('uploadProgressWrap');
        const bar          = document.getElementById('uploadBar');
        const pctEl        = document.getElementById('uploadPct');
        const infoEl       = document.getElementById('uploadInfo');
        const btnSubmit    = document.getElementById('btnSubmitUpload');
        const btnBatal     = document.getElementById('btnBatalUpload');
        progressWrap.style.display = 'block';
        btnSubmit.disabled = true;
        btnBatal.disabled  = true;
        bar.style.width    = '0%';
        pctEl.textContent  = '0%';
        infoEl.textContent = 'Mengupload ' + fileInput.files.length + ' file...';
        const formData = new FormData(formUpload);
        const xhr      = new XMLHttpRequest();
        xhr.upload.addEventListener('progress', function(ev) {
            if (ev.lengthComputable) {
                const pct = Math.round(ev.loaded / ev.total * 100);
                bar.style.width   = pct + '%';
                pctEl.textContent = pct + '%';
                if (pct >= 100) infoEl.textContent = 'Memproses di server...';
            }
        });
        xhr.addEventListener('load', function() {
            if (xhr.status === 200 || xhr.status === 302) {
                bar.style.width    = '100%';
                pctEl.textContent  = '100%';
                bar.classList.remove('progress-bar-animated');
                bar.classList.add('bg-success');
                infoEl.textContent = 'Upload selesai! Halaman akan direfresh...';
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                infoEl.textContent = 'Upload gagal (HTTP ' + xhr.status + '). Coba lagi.';
                bar.classList.add('bg-danger');
                btnSubmit.disabled = false;
                btnBatal.disabled  = false;
            }
        });
        xhr.addEventListener('error', function() {
            infoEl.textContent = 'Koneksi terputus. Coba lagi.';
            bar.classList.add('bg-danger');
            btnSubmit.disabled = false;
            btnBatal.disabled  = false;
        });
        xhr.open('POST', formUpload.action);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    });
}

const modalUploadEl = document.getElementById('modalUpload');
if (modalUploadEl) {
    modalUploadEl.addEventListener('hidden.bs.modal', function() {
        document.getElementById('uploadProgressWrap').style.display = 'none';
        document.getElementById('uploadBar').style.width = '0%';
        document.getElementById('uploadBar').className   = 'progress-bar progress-bar-striped progress-bar-animated bg-primary';
        document.getElementById('uploadPct').textContent = '0%';
        document.getElementById('btnSubmitUpload').disabled = false;
        document.getElementById('btnBatalUpload').disabled  = false;
        formUpload.reset();
    });
    modalUploadEl.addEventListener('show.bs.modal', function(e) {
        document.getElementById('upload-opd-id').value = e.relatedTarget.dataset.opdId;
        document.getElementById('upload-opd-nama').textContent = e.relatedTarget.dataset.opdNama;
    });
}

// ── Modal Update Status ───────────────────────────────────────────────────────
const modalUpdateStatus = document.getElementById('modalUpdateStatus');
if (modalUpdateStatus) {
    modalUpdateStatus.addEventListener('show.bs.modal', function(e) {
        const btn = e.relatedTarget;
        document.getElementById('formUpdateStatus').action = '/permintaan/' + btn.dataset.id;
        document.getElementById('update-judul').value = btn.dataset.judul || '';
        document.getElementById('update-deskripsi').value = btn.dataset.deskripsi || '';
        document.getElementById('update-status').value = btn.dataset.status || 'belum';
        document.getElementById('update-catatan').value = btn.dataset.catatan || '';
        document.getElementById('update-pj').value = btn.dataset.pj || '';
        const opdValues = JSON.parse(btn.dataset.opd || '[]');
        tsUpdateOpd.clear(true);
        tsUpdateOpd.setValue(opdValues, true);
    });
}

// ── Modal Tambah List ─────────────────────────────────────────────────────────
const modalTambahList = document.getElementById('modalTambahList');
if (modalTambahList) {
    modalTambahList.addEventListener('show.bs.modal', function(e) {
        document.getElementById('tambah-list-judul-id').value = e.relatedTarget.dataset.judulId;
        document.getElementById('tambah-list-judul-nama').textContent = e.relatedTarget.dataset.judulNama;
    });
}

// ── Modal Edit Judul ──────────────────────────────────────────────────────────
const modalEditJudul = document.getElementById('modalEditJudul');
if (modalEditJudul) {
    modalEditJudul.addEventListener('show.bs.modal', function(e) {
        const btn = e.relatedTarget;
        document.getElementById('formEditJudul').action = '/judul-permintaan/' + btn.dataset.judulId;
        document.getElementById('edit-judul-nama').value = btn.dataset.judulNama;
    });
}

// ── Modal Bulk Assign OPD ───────────────────────────────────────────────────
const modalBulkAssignOpd = document.getElementById('modalBulkAssignOpd');
if (modalBulkAssignOpd) {
    modalBulkAssignOpd.addEventListener('show.bs.modal', function() {
        const selectedIds = getSelectedPermintaanIds();
        const container = document.getElementById('bulkPermintaanIdsContainer');
        const countModal = document.getElementById('bulkSelectedCountModal');

        if (container) {
            container.innerHTML = selectedIds.map(id => `<input type="hidden" name="permintaan_ids[]" value="${id}">`).join('');
        }
        if (countModal) countModal.textContent = String(selectedIds.length);
    });
}

const formBulkAssignOpd = document.getElementById('formBulkAssignOpd');
if (formBulkAssignOpd) {
    formBulkAssignOpd.addEventListener('submit', function(e) {
        const selectedIds = getSelectedPermintaanIds();
        if (selectedIds.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 item permintaan data.');
            return;
        }

        const container = document.getElementById('bulkPermintaanIdsContainer');
        if (container) {
            container.innerHTML = selectedIds.map(id => `<input type="hidden" name="permintaan_ids[]" value="${id}">`).join('');
        }
    });
}

// ── Modal Bulk Update Status ──────────────────────────────────────────────────
const modalBulkUpdateStatus = document.getElementById('modalBulkUpdateStatus');
if (modalBulkUpdateStatus) {
    modalBulkUpdateStatus.addEventListener('show.bs.modal', function() {
        const selectedIds = getSelectedPermintaanIds();
        const container = document.getElementById('bulkStatusPermintaanIdsContainer');
        if (container) {
            container.innerHTML = selectedIds.map(id => `<input type="hidden" name="permintaan_ids[]" value="${id}">`).join('');
        }
    });
}

const formBulkUpdateStatus = document.getElementById('formBulkUpdateStatus');
if (formBulkUpdateStatus) {
    formBulkUpdateStatus.addEventListener('submit', function(e) {
        const selectedIds = getSelectedPermintaanIds();
        if (selectedIds.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 item permintaan data.');
            return;
        }
        const container = document.getElementById('bulkStatusPermintaanIdsContainer');
        if (container) {
            container.innerHTML = selectedIds.map(id => `<input type="hidden" name="permintaan_ids[]" value="${id}">`).join('');
        }
    });
}

function quickUpdateStatus(selectEl) {
    const opdId = selectEl.dataset.opdId;
    const newStatus = selectEl.value;
    selectEl.style.backgroundColor = newStatus === 'selesai' ? '#16a34a' : (newStatus === 'proses' ? '#d97706' : '#dc2626');

    fetch(`/permintaan-opd/${opdId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            _method: 'PUT',
            status: newStatus
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const container = selectEl.closest('td');
            if (container) {
                const dateEl = container.querySelector('.js-selesai-at-date');
                if (dateEl) dateEl.textContent = data.selesai_at || '';
            }
        } else {
            alert('Gagal memperbarui status');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan saat memperbarui status');
    });
}
</script>
@endsection
