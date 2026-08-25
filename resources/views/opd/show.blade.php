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
                        <span class="badge bg-{{ $row->status_badge }}" style="font-size:0.7rem;">{{ $row->status_label }}</span>
                        @if($row->selesai_at)
                        <div class="text-muted" style="font-size:0.65rem; margin-top:2px;">{{ $row->selesai_at->format('d/m/Y') }}</div>
                        @endif
                    </td>
                    <td style="word-break:break-word; vertical-align:top;">
                        @if($row->dokumen->count() > 0)
                        @foreach($row->dokumen as $dok)
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <a href="{{ route('dokumen.download', $dok) }}" class="text-decoration-none flex-grow-1" style="font-size:0.75rem;">
                                <i class="bi bi-file-earmark me-1 text-muted"></i>{{ Str::limit($dok->nama_file, 22) }}
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
                                style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.68rem;"
                                data-bs-toggle="modal" data-bs-target="#modalUbahStatus"
                                data-opd-id="{{ $row->id }}"
                                data-opd-judul="{{ $row->permintaan->judul_permintaan }}"
                                data-status="{{ $row->status }}"
                                data-catatan="{{ $row->catatan }}"
                                title="Ubah Status">
                                <i class="bi bi-pencil-square"></i>
                            </button>
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
<div class="modal fade" id="modalUbahStatus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Ubah Status</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUbahStatus" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3 small text-muted">Data: <span id="ubah-status-judul" class="fw-semibold text-dark"></span></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" id="ubah-status-value" class="form-select" required>
                            <option value="belum">Belum</option>
                            <option value="proses">Sedang Diproses</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="catatan" id="ubah-status-catatan" class="form-control" rows="2" placeholder="Catatan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-archive me-2"></i>Pilih Dokumen dari Arsip</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dokumen.reuse') }}" method="POST">
                @csrf
                <input type="hidden" name="permintaan_opd_id" id="arsip-opd-id">
                <div class="modal-body">
                    <div class="mb-3 small text-muted">Data: <span id="arsip-opd-judul" class="fw-semibold text-dark"></span></div>
                    
                    <div class="mb-3">
                        <input type="text" id="arsip-search" class="form-control form-control-sm" placeholder="Cari nama dokumen atau pemeriksaan...">
                    </div>

                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover" style="font-size: 0.8rem;">
                            <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th style="width: 30px;">
                                        <input type="checkbox" class="form-check-input" id="arsip-select-all">
                                    </th>
                                    <th>Nama File</th>
                                    <th>Pemeriksaan</th>
                                    <th>Tanggal</th>
                                    <th>Ukuran</th>
                                </tr>
                            </thead>
                            <tbody id="arsip-table-body">
                                <tr><td colspan="5" class="text-center text-muted">Memuat arsip...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btn-submit-arsip" disabled><i class="bi bi-link-45deg"></i> Tautkan Terpilih</button>
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

const modalUbahStatus = document.getElementById('modalUbahStatus');
if (modalUbahStatus) {
    modalUbahStatus.addEventListener('show.bs.modal', function(e) {
        const btn = e.relatedTarget;
        document.getElementById('formUbahStatus').action = '/permintaan-opd/' + btn.dataset.opdId;
        document.getElementById('ubah-status-judul').textContent = btn.dataset.opdJudul;
        document.getElementById('ubah-status-value').value = btn.dataset.status || 'belum';
        document.getElementById('ubah-status-catatan').value = btn.dataset.catatan || '';
    });
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
        
        const tbody = document.getElementById('arsip-table-body');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Memuat arsip...</td></tr>';
        
        const opdName = encodeURIComponent('{{ $opdNama }}');
        fetch(`/opd/${opdName}/arsip`)
            .then(res => res.json())
            .then(data => {
                arsipData = data;
                renderArsipTable();
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Gagal memuat arsip.</td></tr>';
            });
    });
}

function renderArsipTable(search = '') {
    const tbody = document.getElementById('arsip-table-body');
    const filtered = arsipData.filter(d => 
        d.nama_file.toLowerCase().includes(search.toLowerCase()) || 
        d.pemeriksaan.toLowerCase().includes(search.toLowerCase())
    );
    
    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Tidak ada dokumen arsip yang cocok.</td></tr>';
        return;
    }
    
    tbody.innerHTML = filtered.map(d => `
        <tr>
            <td>
                <input type="checkbox" class="form-check-input arsip-checkbox" name="dokumen_ids[]" value="${d.id}" onchange="checkArsipSelection()">
            </td>
            <td style="word-break: break-all;">${d.nama_file}</td>
            <td>${d.pemeriksaan}</td>
            <td>${d.tanggal}</td>
            <td>${d.ukuran}</td>
        </tr>
    `).join('');
    
    checkArsipSelection();
}

document.getElementById('arsip-search')?.addEventListener('input', function(e) {
    renderArsipTable(e.target.value);
});

document.getElementById('arsip-select-all')?.addEventListener('change', function(e) {
    document.querySelectorAll('.arsip-checkbox').forEach(cb => {
        cb.checked = e.target.checked;
    });
    checkArsipSelection();
});

function checkArsipSelection() {
    const checked = document.querySelectorAll('.arsip-checkbox:checked').length;
    const btn = document.getElementById('btn-submit-arsip');
    if(btn) btn.disabled = checked === 0;
}

</script>
@endsection
