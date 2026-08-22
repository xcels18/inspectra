@extends('layouts.app')
@section('title', 'Detail OPD - ' . $opdNama)
@section('page-title', 'Detail Permintaan: ' . $opdNama)

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="{{ route('opd.index') }}{{ $filterSurat ? '?surat_id='.$filterSurat : '' }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
    <form method="GET" action="{{ route('opd.show', urlencode($opdNama)) }}" class="d-flex align-items-center gap-2">
        <label class="form-label mb-0 fw-semibold text-nowrap small">Filter Surat:</label>
        <select name="surat_id" class="form-select form-select-sm" style="max-width: 350px;" onchange="this.form.submit()">
            <option value="">Semua Surat</option>
            @foreach($suratList as $s)
            <option value="{{ $s->id }}" {{ $filterSurat == $s->id ? 'selected' : '' }}>
                {{ $s->nomor_surat }} — {{ $s->perihal }}
            </option>
            @endforeach
        </select>
        @if($filterSurat)
        <a href="{{ route('opd.show', urlencode($opdNama)) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        @endif
    </form>
</div>

@php
    $total   = $rows->count();
    $selesai = $rows->where('status', 'selesai')->count();
    $proses  = $rows->where('status', 'proses')->count();
    $belum   = $rows->where('status', 'belum')->count();
    $pct     = $total > 0 ? round(($selesai + $proses) / $total * 100) : 0;
@endphp

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="fw-semibold mb-1"><i class="bi bi-building text-primary me-2"></i>{{ $opdNama }}</div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">{{ $belum }} Belum</span>
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">{{ $proses }} Proses</span>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">{{ $selesai }} Selesai</span>
                    <span class="badge bg-secondary">{{ $total }} Total</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem;">
                    <span class="text-muted">Progress Keseluruhan</span>
                    <span class="fw-semibold">{{ $pct }}%</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" style="width: {{ $pct }}%"></div>
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
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-1"
         style="cursor:pointer;"
         onclick="toggleSurat({{ $suratId }})"
         onmouseenter="this.style.background='#f8f9fa'"
         onmouseleave="this.style.background=''">
        <div class="d-flex align-items-center gap-2">
            <span id="toggleIcon{{ $suratId }}" class="text-muted" style="font-size:0.75rem;">
                <i class="bi bi-chevron-right"></i>
            </span>
            <div>
                <div class="fw-semibold" style="font-size: 0.88rem;">
                    <i class="bi bi-envelope me-1 text-primary"></i>
                    <a href="{{ route('surat.show', $surat) }}" class="text-decoration-none text-dark"
                       onclick="event.stopPropagation()">{{ $surat->nomor_surat }}</a>
                </div>
                <div class="text-muted" style="font-size: 0.78rem; padding-left: 1.4rem;">{{ $surat->perihal }}</div>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-1 align-items-center" onclick="event.stopPropagation()">
            @if($surat->deadline)
            <span class="badge {{ $surat->deadline->isPast() ? 'bg-danger' : 'bg-light text-dark border' }}" style="font-size: 0.7rem;">
                <i class="bi bi-calendar-event me-1"></i>Deadline: {{ $surat->deadline->format('d/m/Y') }}
            </span>
            @endif
            <span class="badge bg-secondary" style="font-size: 0.7rem;">{{ $itemSelesai + $itemProses }}/{{ $itemTotal }} progress</span>
        </div>
    </div>
    <div class="progress" style="height: 4px; border-radius: 0;">
        <div class="progress-bar bg-success" style="width: {{ $itemPct }}%"></div>
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
        : '<i class="bi bi-chevron-down text-primary"></i>';
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
</script>
@endsection
