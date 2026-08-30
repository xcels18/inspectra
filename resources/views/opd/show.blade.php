@extends('layouts.app')
@section('title', 'Detail OPD - ' . $opdNama)
@section('page-title', 'Detail Permintaan: ' . $opdNama)

@section('content')
<div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); border-radius:10px; position:relative;">
    <div style="position:absolute; top:0; right:0; bottom:0; left:0; background:radial-gradient(circle at top right, rgba(255,255,255,0.06), transparent 60%); pointer-events:none;"></div>
    <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3 position-relative z-1">
        <div>
            <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                <i class="bi bi-building"></i> {{ $opdNama }}
            </h5>
            <div style="font-size:0.75rem; color:#94a3b8; margin-top:2px;">Monitoring dokumen dan status permintaan OPD</div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form method="GET" action="{{ route('opd.show', urlencode($opdNama)) }}" class="d-flex align-items-center gap-2 flex-wrap">
                <select name="pemeriksaan_id" class="form-select form-select-sm border-0 shadow-sm" style="background:rgba(255,255,255,0.9); font-size:0.78rem; width:220px;" onchange="this.form.submit()">
                    <option value="">Semua Pemeriksaan</option>
                    @foreach($pemeriksaanList as $p)
                    <option value="{{ $p->id }}" {{ $filterPemeriksaan == $p->id ? 'selected' : '' }}>
                        {{ $p->nama }} ({{ $p->tahun }})
                    </option>
                    @endforeach
                </select>
                <select name="surat_id" class="form-select form-select-sm border-0 shadow-sm" style="background:rgba(255,255,255,0.9); font-size:0.78rem; width:220px;" onchange="this.form.submit()">
                    <option value="">Semua Surat</option>
                    @foreach($suratList as $s)
                    <option value="{{ $s->id }}" {{ $filterSurat == $s->id ? 'selected' : '' }}>
                        {{ $s->nomor_surat }}
                    </option>
                    @endforeach
                </select>
                @if($filterSurat || $filterPemeriksaan)
                <a href="{{ route('opd.show', urlencode($opdNama)) }}" class="btn btn-sm btn-light" style="font-size:0.78rem;"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
            @php
                $backQuery = [];
                if ($filterSurat) $backQuery['surat_id'] = $filterSurat;
                if ($filterPemeriksaan) $backQuery['pemeriksaan_id'] = $filterPemeriksaan;
                $backUrl = route('opd.index') . (count($backQuery) > 0 ? '?' . http_build_query($backQuery) : '');
            @endphp
            <a href="{{ $backUrl }}" class="btn btn-sm" style="background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.3); font-size:0.78rem;">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
</div>

@php
    $total   = $rows->count();
    $selesai = $rows->where('status', 'selesai')->count();
    $proses  = $rows->where('status', 'proses')->count();
    $belum   = $rows->where('status', 'belum')->count();
    $pct     = $total > 0 ? round(($selesai + $proses) / $total * 100) : 0;
@endphp

<div class="card border-0 shadow-sm mb-3" style="border-radius:10px;">
    <div class="card-body p-0">
        <div class="row g-0 align-items-center text-center">
            <div class="col-3 border-end py-2">
                <div class="text-muted" style="font-size:0.65rem; font-weight:600; text-transform:uppercase;">Belum</div>
                <div class="fw-bold text-danger" style="font-size:1.1rem; line-height:1.2;">{{ $belum }}</div>
            </div>
            <div class="col-3 border-end py-2">
                <div class="text-muted" style="font-size:0.65rem; font-weight:600; text-transform:uppercase;">Proses</div>
                <div class="fw-bold text-warning" style="font-size:1.1rem; line-height:1.2;">{{ $proses }}</div>
            </div>
            <div class="col-3 border-end py-2">
                <div class="text-muted" style="font-size:0.65rem; font-weight:600; text-transform:uppercase;">Selesai</div>
                <div class="fw-bold text-success" style="font-size:1.1rem; line-height:1.2;">{{ $selesai }}</div>
            </div>
            <div class="col-3 py-2 px-3 text-start">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="text-muted" style="font-size:0.65rem; font-weight:600; text-transform:uppercase;">Total: {{ $total }}</div>
                    <span class="fw-bold text-dark" style="font-size: 0.75rem;">{{ $pct }}%</span>
                </div>
                <div class="progress" style="height: 4px; border-radius:2px; background:#f1f5f9;">
                    <div class="progress-bar" style="width: {{ $pct }}%; background:#0b192c;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@forelse($groupedBySurat as $suratId => $suratRows)
@php
    $surat = $suratRows->first()->permintaan->surat;
    if (!$surat) { continue; }
    $itemSelesai = $suratRows->where('status','selesai')->count();
    $itemTotal   = $suratRows->count();
    $itemProses  = $suratRows->where('status','proses')->count();
    $itemPct     = $itemTotal > 0 ? round(($itemSelesai + $itemProses) / $itemTotal * 100) : 0;
@endphp
<div class="card mb-2 border-0 shadow-sm" style="border-radius:8px; overflow:hidden;">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3"
         style="cursor:pointer; border-bottom:1px solid #f1f5f9;"
         onclick="toggleSurat({{ $suratId }})"
         onmouseenter="this.style.background='#f8fafc'"
         onmouseleave="this.style.background=''">
        <div class="d-flex align-items-center gap-2">
            <span id="toggleIcon{{ $suratId }}" class="text-muted" style="font-size:0.75rem; width:14px; text-align:center;">
                <i class="bi bi-chevron-right"></i>
            </span>
            <div>
                <div class="fw-bold text-dark" style="font-size:0.85rem; line-height:1.2;">
                    <i class="bi bi-envelope-paper me-1 text-primary" style="font-size:0.8rem;"></i>
                    <a href="{{ route('surat.show', $surat) }}" class="text-decoration-none text-dark"
                       onclick="event.stopPropagation()">{{ $surat->nomor_surat }}</a>
                </div>
                <div class="text-muted text-truncate" style="font-size:0.7rem; max-width:400px;" title="{{ $surat->perihal }}">{{ $surat->perihal }}</div>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center" onclick="event.stopPropagation()">
            @if($surat->deadline)
            <span class="text-{{ $surat->deadline->isPast() ? 'danger' : 'muted' }}" style="font-size:0.7rem; font-weight:500;">
                <i class="bi bi-calendar-event me-1"></i>{{ $surat->deadline->format('d M Y') }}
            </span>
            @endif
            <div class="d-flex align-items-center gap-1" style="font-size:0.7rem; font-weight:600;">
                <span class="text-primary">{{ $itemSelesai + $itemProses }}</span><span class="text-muted">/ {{ $itemTotal }}</span>
            </div>
        </div>
    </div>
    <div class="progress" style="height:2px; border-radius:0; background:#f1f5f9;">
        <div class="progress-bar" style="width:{{ $itemPct }}%; background:#10b981;"></div>
    </div>
    <div id="suratBody{{ $suratId }}" class="card-body p-0" style="display:none;">
        @if(auth()->user()->isAdmin())
        <form method="POST" action="{{ route('permintaan-opd.bulk-update.post') }}" class="p-2 border-bottom bg-light">
            @csrf
            <input type="hidden" name="surat_id" value="{{ $suratId }}">
            <div class="bulk-checkbox-container" data-surat="{{ $suratId }}"></div>
            <div class="d-flex flex-wrap align-items-end gap-2">
                <div>
                    <label class="form-label mb-1" style="font-size:0.72rem;">Status massal</label>
                    <select name="status" class="form-select form-select-sm" required>
                        <option value="">Pilih status</option>
                        <option value="belum">Belum</option>
                        <option value="proses">Sedang Diproses</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <div class="flex-grow-1" style="min-width:220px;">
                    <label class="form-label mb-1" style="font-size:0.72rem;">Catatan (opsional)</label>
                    <input type="text" name="catatan" class="form-control form-control-sm" placeholder="Catatan untuk semua item terpilih">
                </div>
                <div class="text-muted" style="font-size:0.72rem;">
                    Terpilih: <span class="fw-semibold selected-count" data-surat="{{ $suratId }}">0</span>
                </div>
                <button type="submit" class="btn btn-sm btn-primary bulk-submit-btn" data-surat="{{ $suratId }}" disabled>
                    <i class="bi bi-check2-square me-1"></i>Terapkan
                </button>
            </div>
        </form>
        @endif
        <div class="table-responsive">
        <table class="table table-sm mb-0" style="font-size: 0.82rem; table-layout: fixed;">
            <colgroup>
                @if(auth()->user()->isAdmin())<col style="width: 34px;">@endif
                <col style="width: 32px;">
                <col>
                <col style="width: 110px;">
                <col style="width: 220px;">
                @if(auth()->user()->isAdmin())<col style="width: 80px;">@endif
            </colgroup>
            <thead style="background:#f8f9fa;">
                <tr>
                    @if(auth()->user()->isAdmin())
                    <th style="color:#6b7280; font-weight:500;" class="text-center">
                        <input type="checkbox" class="form-check-input surat-select-all" data-surat="{{ $suratId }}" title="Pilih semua">
                    </th>
                    @endif
                    <th style="color:#9ca3af; font-weight:500;">#</th>
                    <th style="color:#6b7280; font-weight:500;">Data yang Diminta</th>
                    <th style="color:#6b7280; font-weight:500;">Status</th>
                    <th style="color:#6b7280; font-weight:500;">Dokumen</th>
                    @if(auth()->user()->isAdmin())<th style="color:#6b7280; font-weight:500;">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @php
                    $sortedRows = $suratRows->sortBy(function ($r) {
                        $judul = trim((string) ($r->permintaan->judul_permintaan ?? ''));
                        $prefix = mb_substr($judul, 0, 5);
                        $digits = preg_replace('/\D+/', '', $prefix);

                        if ($digits !== '') {
                            return (int) $digits;
                        }

                        return (int) ($r->permintaan->nomor_urut ?? PHP_INT_MAX);
                    });

                    $groupedByJudul = $sortedRows->groupBy(fn($r) => $r->permintaan->judulPermintaan?->id ?? 0);
                    $globalNo = 1;
                @endphp
                @foreach($groupedByJudul as $judulId => $judulRows)
                @php
                    $judulLabel = $judulRows->first()->permintaan->judulPermintaan?->judul;
                    $hasJudul   = !empty($judulLabel);
                @endphp
                @if($hasJudul)
                <tr>
                    <td colspan="{{ auth()->user()->isAdmin() ? 6 : 4 }}"
                        style="background:#f0f4ff; padding:5px 12px; border-top:2px solid #c7d2fe; border-bottom:1px solid #e0e7ff;">
                        <span style="font-size:0.7rem; font-weight:700; color:#1d4ed8; text-transform:uppercase; letter-spacing:0.05em;">
                            <i class="bi bi-folder2-open me-1"></i>{{ $judulLabel }}
                        </span>
                    </td>
                </tr>
                @endif
                @foreach($judulRows as $row)
                <tr>
                    @if(auth()->user()->isAdmin())
                    <td class="text-center" style="vertical-align:top; padding-top:8px;">
                        <input type="checkbox"
                               class="form-check-input bulk-item-checkbox"
                               data-surat="{{ $suratId }}"
                               value="{{ $row->id }}">
                    </td>
                    @endif
                    <td class="text-muted text-center" style="vertical-align:top; padding-top:8px; width:32px;">{{ $globalNo++ }}</td>
                    <td style="word-break:break-word; vertical-align:top;">
                        <div class="{{ $row->status === 'selesai' ? 'text-muted' : 'fw-medium' }}" style="font-size:0.82rem;">
                            {{ $row->permintaan->judul_permintaan }}
                        </div>
                        @if($row->catatan)
                        <div class="text-muted fst-italic" style="font-size:0.7rem; margin-top:2px;">
                            <i class="bi bi-chat-text me-1"></i>{{ $row->catatan }}
                        </div>
                        @endif
                    </td>
                    <td>
                        @if(auth()->user()->isAdmin())
                        <select class="form-select form-select-sm js-quick-status-select border-0 text-white fw-bold shadow-sm"
                                data-opd-id="{{ $row->id }}"
                                style="font-size:0.68rem; padding: 2px 24px 2px 8px; border-radius: 12px; cursor: pointer;
                                       background-color: {{ $row->status === 'selesai' ? '#16a34a' : ($row->status === 'proses' ? '#d97706' : '#dc2626') }};"
                                onchange="quickUpdateStatus(this)">
                            <option value="belum" class="text-dark bg-white" {{ $row->status === 'belum' ? 'selected' : '' }}>🔴 Belum Ada</option>
                            <option value="proses" class="text-dark bg-white" {{ $row->status === 'proses' ? 'selected' : '' }}>🟡 Proses</option>
                            <option value="selesai" class="text-dark bg-white" {{ $row->status === 'selesai' ? 'selected' : '' }}>🟢 Selesai</option>
                        </select>
                        @else
                        <span class="badge bg-{{ $row->status_badge }}" style="font-size:0.7rem;">{{ $row->status_label }}</span>
                        @endif
                        <div class="js-selesai-at-date text-muted" style="font-size:0.65rem; margin-top:2px;">{{ $row->selesai_at ? $row->selesai_at->format('d/m/Y') : '' }}</div>
                    </td>
                    <td style="word-break:break-word; vertical-align:top;">
                        @if($row->dokumen->count() > 0)
                        @foreach($row->dokumen as $dok)
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <button type="button" class="btn btn-xs p-0 border-0 bg-transparent text-primary me-1" style="font-size:0.78rem;"
                                    onclick="openGlobalPreview('{{ route('dokumen.preview', $dok) }}', '{{ route('dokumen.download', $dok) }}', '{{ addslashes($dok->nama_file) }}')"
                                    title="Preview Dokumen">
                                <i class="bi bi-eye text-primary"></i>
                            </button>
                            <a href="javascript:void(0)"
                               onclick="openGlobalPreview('{{ route('dokumen.preview', $dok) }}', '{{ route('dokumen.download', $dok) }}', '{{ addslashes($dok->nama_file) }}')"
                               class="text-decoration-none flex-grow-1 text-truncate text-primary fw-medium"
                               style="font-size:0.75rem; cursor:pointer;"
                               title="{{ $dok->nama_file }} (Klik untuk Live Preview)">
                                {{ Str::limit($dok->nama_file, 20) }}
                            </a>
                            @if(auth()->user()->isAdmin())
                            <form action="{{ route('dokumen.destroy', $dok) }}" method="POST" class="mb-0" onsubmit="return confirm('Hapus dokumen ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-link p-0 text-danger" style="font-size:0.65rem; line-height:1;" title="Hapus">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                        @endforeach
                        @else
                        <span class="text-muted" style="font-size:0.75rem;">Belum ada</span>
                        @endif
                    </td>
                    @if(auth()->user()->isAdmin())
                    <td class="text-center" style="vertical-align:top; padding-top:6px;">
                        <div class="d-flex gap-1 justify-content-center">
                            <button class="btn btn-xs py-0 px-1"
                                style="background:#f9fafb; color:#6b7280; border:1px solid #e5e7eb; font-size:0.68rem;"
                                data-bs-toggle="modal" data-bs-target="#modalUploadOpd"
                                data-opd-id="{{ $row->id }}"
                                data-opd-judul="{{ $row->permintaan->judul_permintaan }}"
                                title="Upload Bukti">
                                <i class="bi bi-upload"></i>
                            </button>
                            <button class="btn btn-xs py-0 px-1"
                                style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; font-size:0.68rem;"
                                data-bs-toggle="modal" data-bs-target="#modalArsipOpd"
                                data-opd-id="{{ $row->id }}"
                                data-opd-judul="{{ $row->permintaan->judul_permintaan }}"
                                title="Pilih dari Arsip">
                                <i class="bi bi-archive"></i>
                            </button>
                            
                                <i class="bi bi-upload"></i>
                            </button>
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@empty
<div class="text-center text-muted py-5">
    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
    <div class="mt-2">Tidak ada permintaan data untuk OPD ini</div>
</div>
@endforelse

@if(auth()->user()->isAdmin())
<div class="modal fade" id="modalUploadOpd" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-upload me-2"></i>Upload Bukti Dokumen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="permintaan_opd_id" id="upload-opd-id">
                <div class="modal-body">
                    <div class="mb-3 small text-muted">Data: <span id="upload-opd-judul" class="fw-semibold text-dark"></span></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File <span class="text-danger">*</span></label>
                        <input type="file" name="file[]" class="form-control" multiple required>
                        <div class="form-text">Maks. 100 file sekaligus, ukuran per file maks. 500MB.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Keterangan dokumen (opsional)">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="rename-toggle" name="rename_enabled" value="1">
                            <label class="form-check-label fw-semibold" for="rename-toggle">Ubah nama file otomatis</label>
                        </div>
                        <div class="form-text">Jika aktif, nama file akan mengikuti nama list permintaan data + custom text + nomor urut.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Custom Text Nama File</label>
                        <input type="text" name="rename_custom" id="rename-custom" class="form-control" placeholder="Contoh: Triwulan-1" disabled>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-upload"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalArsipOpd" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden; background: #f8fafc;">
            <div class="modal-header border-0 bg-white px-4 py-3" style="box-shadow: 0 4px 12px rgba(0,0,0,0.03); z-index: 10;">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-1" style="font-size: 1.1rem;"><i class="bi bi-collection me-2" style="color: #3b82f6;"></i>Pilih Dokumen dari Arsip</h5>
                    <div style="font-size: 0.8rem; color: #64748b;">Gunakan kembali dokumen yang pernah diunggah sebelumnya.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-size: 0.8em;"></button>
            </div>
            
            <form action="{{ route('dokumen.reuse') }}" method="POST" class="d-flex flex-column" style="height: 100%;">
                @csrf
                <input type="hidden" name="permintaan_opd_id" id="arsip-opd-id">
                
                <div class="modal-body p-0" style="background: #f8fafc; overflow-y: auto; max-height: 65vh;">
                    
                    <!-- Search & Filter Header -->
                    <div class="bg-white px-4 py-3 border-bottom" style="position: sticky; top: 0; z-index: 5;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold text-dark" style="font-size: 0.85rem;">
                                Target OPD: <span id="arsip-opd-judul" class="text-primary"></span>
                            </div>
                        </div>
                        
                        <!-- Search Bar -->
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%);"></i>
                            <input type="text" id="arsip-search" class="form-control bg-light border-0 shadow-none" 
                                   placeholder="Cari nama dokumen, OPD, atau pemeriksaan..." 
                                   style="padding-left: 38px; border-radius: 10px; font-size: 0.85rem;">
                        </div>
                    </div>
                    
                    <!-- Cards Container -->
                    <div class="p-4" id="arsip-cards-container" style="min-height: 300px;">
                        <div class="text-center text-muted py-5">
                            <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                            <div style="font-size: 0.85rem;">Memuat arsip...</div>
                        </div>
                    </div>
                    
                </div>
                
                <div class="modal-footer border-top-0 bg-white shadow-sm px-4 py-3 d-flex justify-content-between align-items-center">
                    <div class="form-check m-0">
                        <input class="form-check-input" type="checkbox" id="arsip-select-all">
                        <label class="form-check-label text-muted" for="arsip-select-all" style="font-size: 0.8rem; cursor: pointer;">
                            Pilih Semua (<span id="arsip-selected-count">0</span>)
                        </label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal" style="font-size: 0.85rem; border-radius: 8px;">Batal</button>
                        <button type="submit" class="btn btn-primary fw-semibold shadow-sm" id="btn-submit-arsip" disabled style="font-size: 0.85rem; border-radius: 8px;">
                            <i class="bi bi-link-45deg me-1"></i> Tautkan Terpilih
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endif
@endsection

@section('scripts')
<script>
function toggleSurat(id) {
    const body = document.getElementById('suratBody' + id);
    const icon = document.getElementById('toggleIcon' + id);
    const expanded = body.style.display !== 'none';
    body.style.display = expanded ? 'none' : '';
    icon.innerHTML = expanded
        ? '<i class="bi bi-chevron-right"></i>'
        : '<i class="bi bi-chevron-down text-dark"></i>';
}

const modalUploadOpd = document.getElementById('modalUploadOpd');
if (modalUploadOpd) {
    modalUploadOpd.addEventListener('show.bs.modal', function(e) {
        document.getElementById('upload-opd-id').value = e.relatedTarget.dataset.opdId;
        document.getElementById('upload-opd-judul').textContent = e.relatedTarget.dataset.opdJudul;

        const renameToggle = document.getElementById('rename-toggle');
        const renameCustom = document.getElementById('rename-custom');
        if (renameToggle && renameCustom) {
            renameToggle.checked = false;
            renameCustom.value = '';
            renameCustom.disabled = true;
        }
    });
}

const renameToggle = document.getElementById('rename-toggle');
const renameCustom = document.getElementById('rename-custom');
if (renameToggle && renameCustom) {
    renameToggle.addEventListener('change', function() {
        renameCustom.disabled = !renameToggle.checked;
        if (!renameToggle.checked) renameCustom.value = '';
    });
}

document.querySelectorAll('.surat-select-all').forEach(function(selectAllEl) {
    const suratId = selectAllEl.dataset.surat;
    const itemCheckboxes = Array.from(document.querySelectorAll('.bulk-item-checkbox[data-surat="' + suratId + '"]'));
    const selectedCountEl = document.querySelector('.selected-count[data-surat="' + suratId + '"]');
    const submitBtn = document.querySelector('.bulk-submit-btn[data-surat="' + suratId + '"]');
    const hiddenContainer = document.querySelector('.bulk-checkbox-container[data-surat="' + suratId + '"]');

    function refreshState() {
        const checked = itemCheckboxes.filter(cb => cb.checked);
        const checkedCount = checked.length;

        if (selectedCountEl) selectedCountEl.textContent = checkedCount;
        if (submitBtn) submitBtn.disabled = checkedCount === 0;
        selectAllEl.checked = itemCheckboxes.length > 0 && checkedCount === itemCheckboxes.length;
        selectAllEl.indeterminate = checkedCount > 0 && checkedCount < itemCheckboxes.length;

        if (hiddenContainer) {
            hiddenContainer.innerHTML = checked
                .map(cb => '<input type="hidden" name="permintaan_opd_ids[]" value="' + cb.value + '">')
                .join('');
        }
    }

    selectAllEl.addEventListener('change', function() {
        itemCheckboxes.forEach(function(cb) { cb.checked = selectAllEl.checked; });
        refreshState();
    });

    itemCheckboxes.forEach(function(cb) {
        cb.addEventListener('change', refreshState);
    });

    refreshState();
});

const modalArsipOpd = document.getElementById('modalArsipOpd');
let arsipData = [];

if (modalArsipOpd) {
    modalArsipOpd.addEventListener('show.bs.modal', function(e) {
        document.getElementById('arsip-opd-id').value = e.relatedTarget.dataset.opdId;
        document.getElementById('arsip-opd-judul').textContent = e.relatedTarget.dataset.opdJudul;
        
        const container = document.getElementById('arsip-cards-container');
        container.innerHTML = `
            <div class="text-center text-muted py-5">
                <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                <div style="font-size: 0.85rem;">Memuat arsip...</div>
            </div>`;
        
        const opdName = encodeURIComponent('{{ $opdNama }}');
        fetch(`/opd/${opdName}/arsip`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => {
                        let errMsg = 'Server error ' + res.status;
                        try {
                            const json = JSON.parse(text);
                            errMsg = json.error || json.message || errMsg;
                        } catch (e) {
                            errMsg += ' (HTML Error Returned)';
                            console.error('Server returned HTML:', text);
                        }
                        throw new Error(errMsg);
                    });
                }
                return res.json();
            })
            .then(data => {
                if (!Array.isArray(data)) {
                    throw new Error('Format data arsip tidak valid.');
                }
                arsipData = data;
                document.getElementById('arsip-search').value = '';
                document.getElementById('arsip-select-all').checked = false;
                renderArsipCards();
            })
            .catch(err => {
                container.innerHTML = `
                    <div class="text-center text-danger py-5">
                        <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                        <div class="mt-2" style="font-size: 0.85rem;">Gagal memuat daftar arsip. <br><small>${err.message}</small></div>
                    </div>`;
            });
    });
}

function getIconClass(ext) {
    if(ext === 'pdf') return 'icon-pdf bi-file-earmark-pdf-fill';
    if(ext === 'doc' || ext === 'docx') return 'icon-doc bi-file-earmark-word-fill';
    if(ext === 'xls' || ext === 'xlsx') return 'icon-xls bi-file-earmark-excel-fill';
    if(['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'icon-img bi-image-fill';
    return 'icon-default bi-file-earmark-fill';
}

function renderArsipCards(search = '') {
    const container = document.getElementById('arsip-cards-container');
    const searchLower = search.toLowerCase();
    
    const filtered = arsipData.filter(d => 
        (d.nama_file || '').toLowerCase().includes(searchLower) || 
        (d.opd || '').toLowerCase().includes(searchLower) ||
        (d.judul_permintaan || '').toLowerCase().includes(searchLower) ||
        (d.pemeriksaan || '').toLowerCase().includes(searchLower)
    );
    
    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-5">
                <div class="mb-3">
                    <div style="background: #e2e8f0; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <i class="bi bi-search" style="font-size: 1.5rem; color: #94a3b8;"></i>
                    </div>
                </div>
                <h6 class="fw-semibold text-dark mb-1">Pencarian Tidak Ditemukan</h6>
                <div style="font-size: 0.8rem;">Coba gunakan kata kunci lain untuk mencari dokumen.</div>
            </div>`;
        return;
    }
    
    container.innerHTML = filtered.map(d => `
        <div class="arsip-card d-flex align-items-center gap-2 p-2 mb-2 border rounded bg-white" 
             style="cursor: pointer; transition: all 0.2s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.02);" 
             onclick="toggleArsipCard('${d.id}')" id="card-arsip-${d.id}">
            
            <div class="form-check m-0 flex-shrink-0 ms-1">
                <input class="form-check-input arsip-checkbox" type="checkbox" name="dokumen_ids[]" value="${d.id}" id="chk-arsip-${d.id}" onclick="event.stopPropagation(); checkArsipSelection(); syncCardStyle('${d.id}')" style="transform: scale(0.9);">
            </div>
            
            <div style="width: 34px; height: 34px; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;" class="${getIconClass(d.ext).split(' ')[0]}">
                <i class="bi ${getIconClass(d.ext).split(' ')[1]}" style="font-size: 1.1rem;"></i>
            </div>
            
            <div class="flex-grow-1 min-w-0 ms-1" style="min-width: 0;">
                <div class="fw-semibold text-dark text-truncate mb-1" style="font-size: 0.8rem;" title="${d.nama_file}">${d.nama_file}</div>
                
                <div class="d-flex flex-wrap text-muted" style="font-size: 0.7rem; row-gap: 2px; column-gap: 12px; margin-bottom: 4px;">
                    <span class="text-truncate" style="max-width: 140px;" title="${d.opd}">
                        <i class="bi bi-building me-1"></i>${d.opd}
                    </span>
                    <span class="text-nowrap">
                        <i class="bi bi-clock me-1"></i>${d.tanggal}
                    </span>
                    <span class="text-nowrap">
                        <i class="bi bi-hdd me-1"></i>${d.ukuran}
                    </span>
                </div>
                
                <div class="d-flex flex-wrap align-items-center" style="gap: 6px; font-size: 0.65rem;">
                    <span class="badge" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-weight: 500; white-space: normal; text-align: left; line-height: 1.2; padding: 3px 6px;">
                        <i class="bi bi-shield-check me-1"></i>${d.pemeriksaan}
                    </span>
                    <span class="text-muted text-truncate" style="max-width: 100%;" title="${d.judul_permintaan}">
                        Asal: <span class="fst-italic">${d.judul_permintaan}</span>
                    </span>
                </div>
            </div>
        </div>
    `).join('');
    
    checkArsipSelection();
}

function toggleArsipCard(id) {
    const chk = document.getElementById('chk-arsip-' + id);
    if(chk) {
        chk.checked = !chk.checked;
        syncCardStyle(id);
        checkArsipSelection();
    }
}

function syncCardStyle(id) {
    const chk = document.getElementById('chk-arsip-' + id);
    const card = document.getElementById('card-arsip-' + id);
    if(chk && card) {
        if(chk.checked) {
            card.style.borderColor = '#3b82f6';
            card.style.backgroundColor = '#eff6ff';
            card.style.boxShadow = '0 0 0 2px rgba(59,130,246,0.2)';
        } else {
            card.style.borderColor = '#dee2e6';
            card.style.backgroundColor = '#fff';
            card.style.boxShadow = '0 2px 4px rgba(0,0,0,0.02)';
        }
    }
}

document.getElementById('arsip-search')?.addEventListener('input', function(e) {
    renderArsipCards(e.target.value);
});

document.getElementById('arsip-select-all')?.addEventListener('change', function(e) {
    document.querySelectorAll('.arsip-checkbox').forEach(cb => {
        cb.checked = e.target.checked;
        syncCardStyle(cb.value);
    });
    checkArsipSelection();
});

function checkArsipSelection() {
    const checkboxes = document.querySelectorAll('.arsip-checkbox');
    const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
    
    const btn = document.getElementById('btn-submit-arsip');
    if(btn) btn.disabled = checkedCount === 0;
    
    const countEl = document.getElementById('arsip-selected-count');
    if(countEl) countEl.textContent = checkedCount;
    
    const selectAll = document.getElementById('arsip-select-all');
    if(selectAll) {
        selectAll.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
        selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
    }
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
