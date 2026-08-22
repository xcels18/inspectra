@extends('layouts.app')
@section('title', 'Monitoring OPD')

@section('content')
<div class="container-fluid py-3" style="max-width:1200px;">

    @php
        $totalOpd   = count($stats);
        $opdDenganData = collect($stats)->where('total', '>', 0)->count();
        $selesaiAll = collect($stats)->sum('selesai');
        $prosesAll  = collect($stats)->sum('proses');
        $belumAll   = collect($stats)->sum('belum');
        $totalAll   = collect($stats)->sum('total');
        $overallPct = $totalAll > 0 ? round(($selesaiAll + $prosesAll) / $totalAll * 100) : 0;
    @endphp

    {{-- Header --}}
    <div class="card shadow-sm mb-3 border-0" style="background:linear-gradient(135deg,#1e40af 0%,#1e3a8a 100%);">
        <div class="card-body py-3 px-4">
            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div>
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-building me-2"></i>Monitoring OPD</h5>
                    <div style="font-size:0.75rem; color:rgba(255,255,255,0.6); margin-top:2px;">Rekap progress permintaan data per OPD</div>
                </div>
                <form method="GET" action="{{ route('opd.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="input-group input-group-sm" style="min-width:320px; max-width:430px;">
                        <span class="input-group-text" style="background:rgba(255,255,255,0.15); color:#fff; border-color:rgba(255,255,255,0.3); font-size:0.78rem;">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <select id="suratFilterSelect" class="form-select form-select-sm"
                                style="background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.3); font-size:0.78rem;">
                            <option value="">Pilih Nomor Surat Dasar Monitoring</option>
                            @foreach($suratList as $s)
                            <option value="{{ $s->id }}" style="color:#000;" {{ in_array($s->id, $filterSuratIds ?? []) ? 'selected' : '' }}>
                                {{ $s->nomor_surat }} — {{ Str::limit($s->perihal, 50) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" class="btn btn-sm"
                            onclick="addSelectedSurat()"
                            style="background:#fff; color:#1e3a8a; border-color:#fff; font-size:0.78rem;">
                        <i class="bi bi-plus-circle me-1"></i>Tambah
                    </button>

                    <button type="submit" class="btn btn-sm"
                            style="background:#fff; color:#1e3a8a; border-color:#fff; font-size:0.78rem;">
                        <i class="bi bi-funnel me-1"></i>Terapkan
                    </button>

                    <a href="{{ route('opd.index') }}" class="btn btn-sm"
                       style="background:rgba(255,255,255,0.15); color:#fff; border-color:rgba(255,255,255,0.3); font-size:0.78rem;">
                        <i class="bi bi-x-lg me-1"></i>Reset
                    </a>

                    <div id="selectedSuratInputs">
                        @foreach(($filterSuratIds ?? []) as $sid)
                            <input type="hidden" name="surat_ids[]" value="{{ $sid }}">
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                <div class="card-body py-3 px-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.03em;">Total OPD</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:32px; height:32px; background:#eff6ff;">
                            <i class="bi bi-building text-primary" style="font-size:0.85rem;"></i>
                        </div>
                    </div>
                    <div class="fw-bold" style="font-size:1.6rem; color:#1e40af; line-height:1;">{{ $totalOpd }}</div>
                    <div class="text-muted mt-1" style="font-size:0.72rem;">{{ $opdDenganData }} aktif · {{ $totalAll }} total data</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                <div class="card-body py-3 px-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.03em;">Selesai</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:32px; height:32px; background:#f0fdf4;">
                            <i class="bi bi-check-circle text-success" style="font-size:0.85rem;"></i>
                        </div>
                    </div>
                    <div class="fw-bold" style="font-size:1.6rem; color:#16a34a; line-height:1;">{{ $selesaiAll }}</div>
                    <div class="text-muted mt-1" style="font-size:0.72rem;">dari {{ $totalAll }} data</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                <div class="card-body py-3 px-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.03em;">Proses</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:32px; height:32px; background:#fefce8;">
                            <i class="bi bi-hourglass-split" style="font-size:0.85rem; color:#ca8a04;"></i>
                        </div>
                    </div>
                    <div class="fw-bold" style="font-size:1.6rem; color:#ca8a04; line-height:1;">{{ $prosesAll }}</div>
                    <div class="text-muted mt-1" style="font-size:0.72rem;">sedang berjalan</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                <div class="card-body py-3 px-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.03em;">Progress</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:32px; height:32px; background:#eff6ff;">
                            <i class="bi bi-bar-chart text-primary" style="font-size:0.85rem;"></i>
                        </div>
                    </div>
                    <div class="fw-bold" style="font-size:1.6rem; color:#1e40af; line-height:1;">{{ $overallPct }}<span style="font-size:1rem;">%</span></div>
                    <div class="mt-2" style="height:5px; background:#e9ecef; border-radius:4px; overflow:hidden;">
                        <div style="height:100%; width:{{ $overallPct }}%; background:{{ $overallPct == 100 ? '#22c55e' : '#3b82f6' }}; border-radius:4px; transition:width 0.4s;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="d-flex align-items-center justify-content-between mb-3 gap-2 flex-wrap">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form method="GET" action="{{ route('opd.index') }}" class="d-flex align-items-center gap-2 flex-wrap mb-0">
                @foreach(($filterSuratIds ?? []) as $sid)
                    <input type="hidden" name="surat_ids[]" value="{{ $sid }}">
                @endforeach
                <div class="input-group input-group-sm" style="max-width:320px;">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchOpd" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Cari nama OPD..." autocomplete="off" style="font-size:0.82rem;">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>

                @if(!empty($search))
                    <a href="{{ route('opd.index') }}{{ !empty($filterSuratIds) ? '?'.http_build_query(['surat_ids' => $filterSuratIds]) : '' }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>

            <button type="button" class="btn btn-sm btn-danger" onclick="openPrintModal()">
                <i class="bi bi-file-earmark-pdf me-1"></i>Cetak PDF
            </button>
        </div>

        <div class="d-flex align-items-center gap-1">
            <span class="text-muted me-1" style="font-size:0.78rem;">Tampilan:</span>
            <button id="btnGrid" class="btn btn-sm btn-primary px-2 py-1" title="Grid" onclick="setView('grid')">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
            <button id="btnList" class="btn btn-sm btn-outline-secondary px-2 py-1" title="List" onclick="setView('list')">
                <i class="bi bi-list-ul"></i>
            </button>
        </div>
    </div>

    {{-- Grid View --}}
    <div id="viewGrid">
        <div class="row g-3" id="gridContainer">
            @forelse($stats as $stat)
            @php $pct = $stat['total'] > 0 ? round(($stat['selesai'] + $stat['proses']) / $stat['total'] * 100) : 0; @endphp
            <div class="col-md-6 col-lg-4 opd-item" data-opd="{{ strtolower($stat['opd']) }}">
                <div class="card h-100 border-0 shadow-sm" style="transition:box-shadow 0.15s; {{ $stat['total'] == 0 ? 'opacity:0.7;' : '' }}"
                     onmouseenter="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.1)'"
                     onmouseleave="this.style.boxShadow=''">
                    <div class="card-body pb-2">
                        {{-- OPD name --}}
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                            <div class="d-flex align-items-start gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:34px; height:34px; background:{{ $stat['total'] == 0 ? '#f3f4f6' : '#eff6ff' }};">
                                    <i class="bi bi-building {{ $stat['total'] == 0 ? 'text-secondary' : 'text-primary' }}" style="font-size:0.9rem;"></i>
                                </div>
                                <div class="fw-semibold lh-sm" style="font-size:0.82rem;">{{ $stat['opd'] }}</div>
                            </div>
                            @if($stat['total'] > 0)
                            <span class="badge bg-secondary flex-shrink-0" style="font-size:0.68rem;">{{ $stat['total'] }}</span>
                            @else
                            <span class="badge flex-shrink-0" style="font-size:0.68rem; background:#f3f4f6; color:#9ca3af;">Belum ada data</span>
                            @endif
                        </div>

                        {{-- Progress --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1" style="font-size:0.72rem;">
                                <span class="text-muted">Progress</span>
                                <span class="fw-bold {{ $pct == 100 ? 'text-success' : ($stat['total'] == 0 ? 'text-secondary' : 'text-primary') }}">{{ $pct }}%</span>
                            </div>
                            <div style="height:5px; background:#e9ecef; border-radius:4px; overflow:hidden;">
                                <div style="height:100%; width:{{ $pct }}%; background:{{ $pct == 100 ? '#22c55e' : '#3b82f6' }}; border-radius:4px;"></div>
                            </div>
                        </div>

                        {{-- Status badges --}}
                        @if($stat['total'] > 0)
                        <div class="d-flex gap-1 flex-wrap" style="font-size:0.7rem;">
                            <span style="background:#fee2e2; color:#dc2626; padding:2px 8px; border-radius:999px;">
                                {{ $stat['belum'] }} Belum
                            </span>
                            <span style="background:#fef9c3; color:#ca8a04; padding:2px 8px; border-radius:999px;">
                                {{ $stat['proses'] }} Proses
                            </span>
                            <span style="background:#dcfce7; color:#16a34a; padding:2px 8px; border-radius:999px;">
                                {{ $stat['selesai'] }} Selesai
                            </span>
                        </div>
                        @else
                        <div style="font-size:0.72rem; color:#9ca3af;">Tidak ada permintaan data</div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3">
                        @if($stat['total'] > 0)
                        <a href="{{ route('opd.show', urlencode($stat['opd'])) }}{{ !empty($filterSuratIds) ? '?surat_id='.($filterSuratIds[0] ?? '') : '' }}"
                           class="btn btn-sm w-100"
                           style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.78rem;">
                            <i class="bi bi-eye me-1"></i>Lihat Detail
                        </a>
                        @else
                        <button class="btn btn-sm w-100 disabled" style="background:#f3f4f6; color:#9ca3af; border:1px solid #e5e7eb; font-size:0.78rem;">
                            <i class="bi bi-dash me-1"></i>Belum Ada Data
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-building" style="font-size:2.5rem;"></i>
                <div class="mt-2" style="font-size:0.85rem;">Belum ada data permintaan</div>
            </div>
            @endforelse
        </div>
        <div id="gridEmpty" style="display:none;" class="text-center text-muted py-5">
            <i class="bi bi-search" style="font-size:2rem;"></i>
            <div class="mt-2" style="font-size:0.85rem;">OPD tidak ditemukan</div>
        </div>
    </div>

    {{-- List View --}}
    <div id="viewList" style="display:none;">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                @forelse($stats as $stat)
                @php $pct = $stat['total'] > 0 ? round(($stat['selesai'] + $stat['proses']) / $stat['total'] * 100) : 0; @endphp
                <div class="opd-item d-flex align-items-center gap-3 px-3 py-2 border-bottom"
                     data-opd="{{ strtolower($stat['opd']) }}"
                     style="transition:background 0.12s; {{ $stat['total'] == 0 ? 'opacity:0.65;' : '' }}"
                     onmouseenter="this.style.background='#f8f9fa'"
                     onmouseleave="this.style.background=''">
                    {{-- Icon --}}
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:30px; height:30px; background:{{ $stat['total'] == 0 ? '#f3f4f6' : '#eff6ff' }};">
                        <i class="bi bi-building {{ $stat['total'] == 0 ? 'text-secondary' : 'text-primary' }}" style="font-size:0.8rem;"></i>
                    </div>
                    {{-- Nama OPD --}}
                    <div class="fw-semibold" style="font-size:0.82rem; flex:1; min-width:0;">
                        {{ $stat['opd'] }}
                    </div>
                    {{-- Status pills --}}
                    <div class="d-none d-sm-flex gap-1 flex-shrink-0" style="font-size:0.68rem;">
                        @if($stat['total'] > 0)
                        <span style="background:#fee2e2; color:#dc2626; padding:1px 7px; border-radius:999px;">{{ $stat['belum'] }} Belum</span>
                        <span style="background:#fef9c3; color:#ca8a04; padding:1px 7px; border-radius:999px;">{{ $stat['proses'] }} Proses</span>
                        <span style="background:#dcfce7; color:#16a34a; padding:1px 7px; border-radius:999px;">{{ $stat['selesai'] }} Selesai</span>
                        @else
                        <span style="background:#f3f4f6; color:#9ca3af; padding:1px 7px; border-radius:999px;">Belum ada data</span>
                        @endif
                    </div>
                    {{-- Progress bar --}}
                    <div class="d-none d-md-block flex-shrink-0" style="width:100px;">
                        <div class="d-flex justify-content-between mb-1" style="font-size:0.65rem;">
                            <span class="text-muted">{{ $stat['synced'] ?? '' }}</span>
                            <span class="fw-bold {{ $pct == 100 ? 'text-success' : ($stat['total'] == 0 ? 'text-secondary' : 'text-primary') }}">{{ $pct }}%</span>
                        </div>
                        <div style="height:4px; background:#e9ecef; border-radius:4px; overflow:hidden;">
                            <div style="height:100%; width:{{ $pct }}%; background:{{ $pct == 100 ? '#22c55e' : '#3b82f6' }}; border-radius:4px;"></div>
                        </div>
                    </div>
                    {{-- Total badge --}}
                    <span class="badge flex-shrink-0" style="font-size:0.68rem; background:{{ $stat['total'] == 0 ? '#f3f4f6' : '' }}; color:{{ $stat['total'] == 0 ? '#9ca3af' : '' }};" class="{{ $stat['total'] > 0 ? 'bg-secondary' : '' }}">{{ $stat['total'] }}</span>
                    {{-- Tombol --}}
                    @if($stat['total'] > 0)
                    <a href="{{ route('opd.show', urlencode($stat['opd'])) }}{{ !empty($filterSuratIds) ? '?surat_id='.($filterSuratIds[0] ?? '') : '' }}"
                       class="btn btn-sm flex-shrink-0"
                       style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.72rem; padding:2px 10px;">
                        <i class="bi bi-eye me-1"></i>Detail
                    </a>
                    @else
                    <span class="btn btn-sm flex-shrink-0 disabled" style="background:#f3f4f6; color:#9ca3af; border:1px solid #e5e7eb; font-size:0.72rem; padding:2px 10px;">
                        <i class="bi bi-dash"></i>
                    </span>
                    @endif
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-building" style="font-size:2.5rem;"></i>
                    <div class="mt-2" style="font-size:0.85rem;">Belum ada data permintaan</div>
                </div>
                @endforelse
            </div>
        </div>
        <div id="listEmpty" style="display:none;" class="text-center text-muted py-5 card shadow-sm border-0">
            <i class="bi bi-search" style="font-size:2rem;"></i>
            <div class="mt-2" style="font-size:0.85rem;">OPD tidak ditemukan</div>
        </div>
    </div>

</div>

{{-- Modal Cetak PDF --}}
<div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="printForm" method="GET" action="{{ route('opd.print') }}" target="_blank" class="modal-content border-0 shadow">
            <div class="modal-header border-0 py-2 px-3" style="background:linear-gradient(135deg,#1e40af 0%,#1e3a8a 100%);">
                <h6 class="modal-title text-white fw-semibold mb-0">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Cetak Laporan Monitoring OPD
                </h6>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Judul Laporan <span class="text-danger">*</span></label>
                    <input type="text" name="judul_laporan" class="form-control" placeholder="Contoh: Laporan Monitoring OPD Triwulan I" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor Surat Dasar Perhitungan <span class="text-danger">*</span></label>
                    <div class="border rounded p-2" style="max-height:260px; overflow:auto;">
                        @foreach($suratList as $s)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="surat_ids[]" value="{{ $s->id }}" id="print_surat_{{ $s->id }}"
                                    {{ in_array($s->id, $filterSuratIds ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="print_surat_{{ $s->id }}" style="font-size:0.84rem;">
                                    <strong>{{ $s->nomor_surat }}</strong> — {{ Str::limit($s->perihal, 95) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-text">Centang minimal 1 nomor surat.</div>
                </div>

                <div class="mb-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="print_detail" name="detail" value="1">
                        <label class="form-check-label fw-semibold" for="print_detail">Tampilkan Detail Permintaan</label>
                    </div>
                    <div class="form-text">Jika aktif, PDF akan menampilkan rincian permintaan per status (Belum, Proses, Selesai).</div>
                </div>

                <input type="hidden" name="search" value="{{ $search ?? '' }}">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bi bi-download me-1"></i>Generate PDF
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function setView(mode) {
    const isGrid = mode === 'grid';
    document.getElementById('viewGrid').style.display = isGrid ? '' : 'none';
    document.getElementById('viewList').style.display = isGrid ? 'none' : '';
    document.getElementById('btnGrid').className = isGrid
        ? 'btn btn-sm btn-primary px-2 py-1'
        : 'btn btn-sm btn-outline-secondary px-2 py-1';
    document.getElementById('btnList').className = isGrid
        ? 'btn btn-sm btn-outline-secondary px-2 py-1'
        : 'btn btn-sm btn-primary px-2 py-1';
    localStorage.setItem('opdView', mode);
    filterOpd(document.getElementById('searchOpd').value);
}

function filterOpd(q) {
    q = q.toLowerCase().trim();
    let gridVisible = 0, listVisible = 0;

    document.querySelectorAll('#viewGrid .opd-item').forEach(function(el) {
        const show = el.dataset.opd.includes(q);
        el.style.display = show ? '' : 'none';
        if (show) gridVisible++;
    });
    document.querySelectorAll('#viewList .opd-item').forEach(function(el) {
        const show = el.dataset.opd.includes(q);
        el.style.display = show ? '' : 'none';
        if (show) listVisible++;
    });

    document.getElementById('gridEmpty').style.display = gridVisible === 0 ? '' : 'none';
    document.getElementById('listEmpty').style.display  = listVisible === 0 ? '' : 'none';
}

document.getElementById('searchOpd').addEventListener('input', function() {
    filterOpd(this.value);
});

function openPrintModal() {
    const modal = new bootstrap.Modal(document.getElementById('printModal'));
    modal.show();
}

function addSelectedSurat() {
    const select = document.getElementById('suratFilterSelect');
    const selectedValue = select.value;
    if (!selectedValue) return;

    const container = document.getElementById('selectedSuratInputs');
    const existing = container.querySelector(`input[name="surat_ids[]"][value="${selectedValue}"]`);
    if (existing) return;

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'surat_ids[]';
    input.value = selectedValue;
    container.appendChild(input);
}

// Restore saved view preference
const savedView = localStorage.getItem('opdView') || 'grid';
setView(savedView);
</script>
@endsection
