@extends('layouts.app')

@section('title', 'Backup Dokumen ZIP')
@section('page-title', 'Backup Dokumen')

@section('content')
<div class="container-fluid py-3" style="max-width:1200px;">
    <div class="card shadow-sm mb-3 border-0" style="background:linear-gradient(135deg,#1a73e8 0%,#0d47a1 100%);">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center g-2">
                <div class="col-auto">
                    <h5 class="mb-0 fw-bold text-white">
                        <i class="bi bi-file-earmark-zip me-2"></i>Backup Dokumen ZIP
                    </h5>
                </div>
                <div class="col text-white-50" style="font-size:0.82rem;">
                    Pilih nomor surat, atur struktur folder, lalu proses backup ke file ZIP.
                </div>
            </div>
        </div>
    </div>

    <form id="backupZipForm" action="{{ route('backup-dokumen.download') }}" method="POST">
        @csrf

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center" style="background:#f8f9fa;">
                <span class="fw-semibold text-muted" style="font-size:0.82rem;">
                    <i class="bi bi-folder2-open me-1"></i>Struktur Folder ZIP
                </span>
                <span class="text-muted" style="font-size:0.72rem;">Drag untuk ubah urutan, centang untuk aktifkan level</span>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-1 mb-3 flex-wrap" style="font-size:0.8rem;">
                    <span class="badge bg-secondary px-2 py-1">ROOT</span>
                    <i class="bi bi-chevron-right text-muted"></i>
                    <span id="previewLevels" class="d-flex gap-1 flex-wrap"></span>
                    <i class="bi bi-chevron-right text-muted" id="arrowFile"></i>
                    <span class="badge bg-light text-dark border px-2 py-1">📄 File</span>
                </div>

                <div id="sortableStructure" class="border rounded p-2" style="min-height:60px; background:#fafafa;">
                    @foreach($structureOptions as $key => $label)
                    <div class="d-flex align-items-center gap-2 p-2 mb-1 bg-white border rounded drag-item"
                         style="cursor:grab; user-select:none;" data-level="{{ $key }}">
                        <i class="bi bi-grip-vertical text-muted"></i>
                        <input type="checkbox" class="form-check-input level-check"
                               name="structure[]" value="{{ $key }}"
                               {{ in_array($key, $defaultStructure, true) ? 'checked' : '' }}
                               id="chk_{{ $key }}">
                        <label class="form-check-label mb-0" for="chk_{{ $key }}" style="font-size:0.82rem; cursor:pointer;">
                            {{ $label }}
                        </label>
                    </div>
                    @endforeach
                </div>
                <small class="text-muted d-block mt-2" style="font-size:0.72rem;">`Nomor Surat` wajib dipakai sebagai level pertama.</small>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background:#f8f9fa; border-bottom:1px solid #e9ecef;">
                <span class="fw-semibold text-muted" style="font-size:0.82rem;">
                    <i class="bi bi-envelope me-1"></i>Daftar Surat
                    <span class="badge bg-secondary ms-1" style="font-size:0.68rem;">{{ $suratStats->count() }}</span>
                </span>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" id="btnSelectAll" style="font-size:0.72rem;">
                        Pilih Semua
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" id="btnUnselectAll" style="font-size:0.72rem;">
                        Batal Semua
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                @forelse($suratStats as $row)
                @php
                    $surat = $row['surat'];
                    $dokCount = $row['dokumen_count'];
                @endphp
                <label class="d-flex align-items-center gap-2 px-3 py-2 border-bottom" style="cursor:pointer;">
                    <input type="checkbox" class="form-check-input surat-check" name="surat_ids[]" value="{{ $surat->id }}">
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:0.82rem;">{{ $surat->nomor_surat }}</div>
                        <div class="text-muted" style="font-size:0.74rem;">{{ $surat->perihal }}</div>
                    </div>
                    <span class="badge bg-light text-dark border" style="font-size:0.68rem;">
                        <i class="bi bi-file-earmark"></i> {{ $dokCount }} dokumen
                    </span>
                </label>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size:2rem;"></i>
                    <div class="mt-2" style="font-size:0.85rem;">Belum ada data surat.</div>
                </div>
                @endforelse
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="text-muted" style="font-size:0.78rem;">
                    Surat terpilih: <span id="selectedCount" class="fw-semibold">0</span>
                </div>
                <button type="submit" id="btnSubmitBackup" class="btn btn-primary btn-sm fw-semibold">
                    <i class="bi bi-file-earmark-zip me-1"></i>Proses Backup ZIP
                </button>
            </div>
        </div>
    </form>
</div>

<div id="overlayBackup" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
     style="background:rgba(0,0,0,0.55); z-index:9999; display:none !important;">
    <div class="bg-white rounded-3 shadow-lg p-4 text-center" style="min-width:340px; max-width:480px;">
        <div class="mb-3">
            <i class="bi bi-file-earmark-zip text-primary" style="font-size:2rem;"></i>
        </div>
        <h6 class="fw-bold mb-1">Memproses Backup ZIP...</h6>
        <p class="text-muted mb-3" style="font-size:0.82rem;">Mohon tunggu, jangan tutup halaman ini.</p>
        <div style="height:8px; background:#e9ecef; border-radius:6px; overflow:hidden;" class="mb-2">
            <div id="backupBar" style="height:100%; width:0%; background:#3b82f6; border-radius:6px; transition:width 0.3s;"></div>
        </div>
        <div id="backupPct" class="text-primary fw-semibold" style="font-size:0.8rem;">0%</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const checks = Array.from(document.querySelectorAll('.surat-check'));
    const selectedCountEl = document.getElementById('selectedCount');
    const btnSelectAll = document.getElementById('btnSelectAll');
    const btnUnselectAll = document.getElementById('btnUnselectAll');
    const form = document.getElementById('backupZipForm');
    const overlay = document.getElementById('overlayBackup');
    const bar = document.getElementById('backupBar');
    const pct = document.getElementById('backupPct');

    function updateSelectedCount() {
        const count = checks.filter(c => c.checked).length;
        selectedCountEl.textContent = String(count);
    }

    checks.forEach(c => c.addEventListener('change', updateSelectedCount));

    btnSelectAll.addEventListener('click', function () {
        checks.forEach(c => c.checked = true);
        updateSelectedCount();
    });

    btnUnselectAll.addEventListener('click', function () {
        checks.forEach(c => c.checked = false);
        updateSelectedCount();
    });

    const sortable = document.getElementById('sortableStructure');
    const preview = document.getElementById('previewLevels');
    const arrowFile = document.getElementById('arrowFile');

    function updatePreview() {
        const labels = [];
        sortable.querySelectorAll('.drag-item').forEach(function (item) {
            const chk = item.querySelector('.level-check');
            const lbl = item.querySelector('label');
            if (chk && chk.checked && lbl) {
                labels.push(lbl.textContent.trim());
            }
        });

        preview.innerHTML = '';
        labels.forEach(function (label, idx) {
            const badge = document.createElement('span');
            badge.className = 'badge bg-info-subtle text-info border border-info-subtle px-2 py-1';
            badge.style.fontSize = '0.7rem';
            badge.textContent = label;
            preview.appendChild(badge);

            if (idx < labels.length - 1) {
                const arr = document.createElement('i');
                arr.className = 'bi bi-chevron-right text-muted';
                arr.style.fontSize = '0.55rem';
                preview.appendChild(arr);
            }
        });

        arrowFile.style.display = labels.length > 0 ? '' : 'none';
    }

    sortable.querySelectorAll('.level-check').forEach(function (chk) {
        chk.addEventListener('change', function () {
            if (chk.value === 'nomor_surat' && !chk.checked) {
                chk.checked = true;
                return;
            }
            updatePreview();
        });
    });

    let dragging = null;
    sortable.querySelectorAll('.drag-item').forEach(function (item) {
        item.setAttribute('draggable', 'true');
        item.addEventListener('dragstart', function () {
            dragging = item;
            item.style.opacity = '0.4';
        });
        item.addEventListener('dragend', function () {
            item.style.opacity = '1';
            dragging = null;
            updatePreview();
        });
        item.addEventListener('dragover', function (e) {
            e.preventDefault();
            if (!dragging || dragging === item) return;
            const mid = item.getBoundingClientRect().top + item.getBoundingClientRect().height / 2;
            sortable.insertBefore(dragging, e.clientY < mid ? item : item.nextSibling);
        });
    });

    updatePreview();
    updateSelectedCount();

form.addEventListener('submit', function (e) {
        const count = checks.filter(c => c.checked).length;
        if (count === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 surat untuk dibackup.');
            return;
        }

        overlay.style.display = 'flex';
        let prog = 0;
        bar.style.width = '0%';
        pct.textContent = '0%';
        let timer = null;

        function updateProgress() {
            prog = Math.min(prog + 8, 92);
            bar.style.width = prog + '%';
            pct.textContent = prog + '%';
        }

        timer = setInterval(updateProgress, 250);

        // Auto-close setelah download ~10s atau tab unload
        setTimeout(() => {
            if (overlay.style.display === 'flex') {
                overlay.style.display = 'none';
                if (timer) clearInterval(timer);
            }
        }, 10000);

        // Close on tab visibility change / unload
        const hideModal = () => {
            overlay.style.display = 'none';
            if (timer) clearInterval(timer);
        };

        window.addEventListener('visibilitychange', hideModal);
        window.addEventListener('pagehide', hideModal);
        window.addEventListener('beforeunload', hideModal);
    });
})();
</script>
@endsection
