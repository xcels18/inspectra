@extends('layouts.app')
@section('title', 'Monitoring OPD')

@section('content')
<div class="container-fluid py-3" style="max-width:1200px;">

    @php
        $totalOpd      = count($stats);
        $opdDenganData = collect($stats)->where('total', '>', 0)->count();
        $selesaiAll    = collect($stats)->sum('selesai');
        $prosesAll     = collect($stats)->sum('proses');
        $belumAll      = collect($stats)->sum('belum');
        $totalAll      = collect($stats)->sum('total');
        $overallPct    = $totalAll > 0 ? round(($selesaiAll + $prosesAll) / $totalAll * 100) : 0;
    @endphp

    {{-- Header --}}
    <div class="card border-0 mb-3 overflow-hidden" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); border-radius:12px; position:relative;">
        <div style="position:absolute;top:0;right:0;bottom:0;left:0;background:radial-gradient(circle at top right,rgba(255,255,255,0.07),transparent 60%);pointer-events:none;"></div>
        <div class="card-body py-3 px-4 position-relative" style="z-index:1;">

            {{-- Title row --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2" style="font-size:0.95rem;">
                        <i class="bi bi-building"></i> Monitoring OPD
                    </h5>
                    <div style="font-size:0.72rem;color:#94a3b8;margin-top:2px;">Rekap progress permintaan data per instansi</div>
                </div>
                {{-- Stats chips --}}
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="px-2 py-1 rounded-pill" style="background:rgba(255,255,255,0.1);font-size:0.72rem;color:#e2e8f0;border:1px solid rgba(255,255,255,0.12);">
                        <i class="bi bi-building me-1"></i>{{ $totalOpd }} instansi
                    </span>
                    <span class="px-2 py-1 rounded-pill" style="background:rgba(34,197,94,0.2);font-size:0.72rem;color:#86efac;border:1px solid rgba(34,197,94,0.3);">
                        <i class="bi bi-check-circle me-1"></i>{{ $selesaiAll }} selesai
                    </span>
                    <span class="px-2 py-1 rounded-pill" style="background:rgba(234,179,8,0.2);font-size:0.72rem;color:#fde047;border:1px solid rgba(234,179,8,0.3);">
                        <i class="bi bi-hourglass-split me-1"></i>{{ $prosesAll }} proses
                    </span>
                    <span class="px-2 py-1 rounded-pill" style="background:rgba(239,68,68,0.2);font-size:0.72rem;color:#fca5a5;border:1px solid rgba(239,68,68,0.3);">
                        <i class="bi bi-clock me-1"></i>{{ $belumAll }} belum
                    </span>
                    {{-- Overall progress --}}
                    <div class="d-flex align-items-center gap-2 px-3 py-1 rounded-pill" style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.12);">
                        <span style="font-size:0.72rem;color:#94a3b8;">Overall</span>
                        <div style="width:60px;height:4px;background:rgba(255,255,255,0.15);border-radius:4px;overflow:hidden;">
                            <div style="height:100%;width:{{ $overallPct }}%;background:{{ $overallPct==100?'#22c55e':'#60a5fa' }};border-radius:4px;"></div>
                        </div>
                        <span class="fw-bold" style="font-size:0.75rem;color:{{ $overallPct==100?'#4ade80':'#93c5fd' }};">{{ $overallPct }}%</span>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="p-2 rounded-3" style="background:rgba(0,0,0,0.18);border:1px solid rgba(255,255,255,0.06);">
                <form method="GET" action="{{ route('opd.index') }}" class="row g-2 align-items-center m-0">
                    {{-- Pemeriksaan --}}
                    <div class="col-12 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text border-0" style="background:rgba(255,255,255,0.08);color:#cbd5e1;">
                                <i class="bi bi-folder2-open"></i>
                            </span>
                            <select name="pemeriksaan_id" class="form-select form-select-sm border-0"
                                    style="background:rgba(255,255,255,0.08);color:#fff;font-size:0.78rem;box-shadow:none;"
                                    onchange="this.form.submit()">
                                <option value="" style="color:#000;">Semua Pemeriksaan</option>
                                <option value="null" style="color:#000;" {{ request('pemeriksaan_id')==='null'?'selected':'' }}>Belum Dipetakan</option>
                                @foreach($pemeriksaanList as $p)
                                <option value="{{ $p->id }}" style="color:#000;" {{ request('pemeriksaan_id')==$p->id?'selected':'' }}>
                                    {{ Str::limit($p->nama, 35) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Surat --}}
                    <div class="col-12 col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text border-0" style="background:rgba(255,255,255,0.08);color:#cbd5e1;">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <select id="suratFilterSelect" class="form-select form-select-sm border-0"
                                    style="background:rgba(255,255,255,0.08);color:#fff;font-size:0.78rem;box-shadow:none;">
                                <option value="" style="color:#000;">Tambah filter surat (multi-surat)...</option>
                                @foreach($suratList as $s)
                                <option value="{{ $s->id }}" style="color:#000;" {{ in_array($s->id,$filterSuratIds??[])?'selected':'' }}>
                                    {{ $s->nomor_surat }} — {{ Str::limit($s->perihal, 45) }}
                                </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-sm"
                                    onclick="addSelectedSurat()"
                                    style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.1);font-size:0.78rem;">
                                <i class="bi bi-plus-lg me-1"></i>Tambah
                            </button>
                        </div>
                    </div>
                    {{-- Actions --}}
                    <div class="col-12 col-md-3 d-flex gap-1 justify-content-end">
                        <button type="submit" class="btn btn-sm flex-grow-1"
                                style="background:rgba(255,255,255,0.15);color:#fff;font-size:0.78rem;border:1px solid rgba(255,255,255,0.1);">
                            Terapkan
                        </button>
                        <a href="{{ route('opd.index') }}" class="btn btn-sm"
                           style="background:rgba(239,68,68,0.2);color:#fca5a5;font-size:0.78rem;border:1px solid rgba(239,68,68,0.3);" title="Reset">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        <div id="selectedSuratInputs">
                            @foreach(($filterSuratIds??[]) as $sid)
                                <input type="hidden" name="surat_ids[]" value="{{ $sid }}">
                            @endforeach
                        </div>
                    </div>
                </form>
                {{-- Selected surat tags --}}
                @if(!empty($filterSuratIds))
                <div class="d-flex flex-wrap gap-1 mt-2 px-1">
                    @foreach($suratList->whereIn('id',$filterSuratIds) as $s)
                    <span class="px-2 py-1 rounded-pill" style="background:rgba(59,130,246,0.2);border:1px solid rgba(59,130,246,0.3);font-size:0.68rem;color:#93c5fd;">
                        <i class="bi bi-envelope me-1"></i>{{ $s->nomor_surat }}
                    </span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="d-flex align-items-center justify-content-between mb-3 gap-2 flex-wrap">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form method="GET" action="{{ route('opd.index') }}" class="d-flex align-items-center gap-2 mb-0">
                @foreach(($filterSuratIds??[]) as $sid)
                    <input type="hidden" name="surat_ids[]" value="{{ $sid }}">
                @endforeach
                @if(request('pemeriksaan_id'))
                    <input type="hidden" name="pemeriksaan_id" value="{{ request('pemeriksaan_id') }}">
                @endif
                <div class="input-group input-group-sm" style="max-width:280px;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted" style="font-size:0.8rem;"></i></span>
                    <input type="text" id="searchOpd" name="search" value="{{ $search??'' }}"
                           class="form-control border-start-0 ps-0"
                           placeholder="Cari nama OPD..." autocomplete="off"
                           style="font-size:0.8rem;">
                    <button type="submit" class="btn btn-sm" style="background:#0b192c;color:#fff;font-size:0.78rem;border:0;">Cari</button>
                </div>
                @if(!empty($search))
                <a href="{{ route('opd.index') }}{{ !empty($filterSuratIds)?'?'.http_build_query(['surat_ids'=>$filterSuratIds]):'' }}"
                   class="btn btn-sm btn-outline-secondary" style="font-size:0.75rem;">Reset</a>
                @endif
            </form>
        </div>
        <div class="d-flex align-items-center gap-1">
            <span class="text-muted me-1" style="font-size:0.75rem;">Tampilan:</span>
            <button id="btnGrid" class="btn btn-sm px-2 py-1" title="Grid" onclick="setView('grid')"
                    style="background:#0b192c;color:#fff;border:0;font-size:0.78rem;">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
            <button id="btnList" class="btn btn-sm px-2 py-1 btn-outline-secondary" title="List" onclick="setView('list')"
                    style="font-size:0.78rem;">
                <i class="bi bi-list-ul"></i>
            </button>
        </div>
    </div>

    {{-- Grid View --}}
    <div id="viewGrid">
        <div class="row g-2" id="gridContainer">
            @forelse($stats as $stat)
            @php $pct = $stat['total']>0 ? round(($stat['selesai']+$stat['proses'])/$stat['total']*100) : 0; @endphp
            <div class="col-md-6 col-lg-4 opd-item" data-opd="{{ strtolower($stat['opd']) }}">
                <div class="card h-100 border-0 shadow-sm" style="border-radius:10px;transition:box-shadow 0.15s;{{ $stat['total']==0?'opacity:0.65;':'' }}"
                     onmouseenter="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.1)'"
                     onmouseleave="this.style.boxShadow=''">
                    <div class="card-body pb-2">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <div class="d-flex align-items-start gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:30px;height:30px;background:{{ $stat['total']==0?'#f3f4f6':'#eff6ff' }};">
                                    <i class="bi bi-building {{ $stat['total']==0?'text-secondary':'text-primary' }}" style="font-size:0.78rem;"></i>
                                </div>
                                <div class="fw-semibold lh-sm" style="font-size:0.8rem;">{{ $stat['opd'] }}</div>
                            </div>
                            @if($stat['total']>0)
                            <span class="badge bg-secondary flex-shrink-0" style="font-size:0.65rem;">{{ $stat['total'] }}</span>
                            @else
                            <span class="badge flex-shrink-0" style="font-size:0.65rem;background:#f3f4f6;color:#9ca3af;">Belum ada</span>
                            @endif
                        </div>

                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1" style="font-size:0.68rem;">
                                <span class="text-muted">Progress</span>
                                <span class="fw-bold {{ $pct==100?'text-success':($stat['total']==0?'text-secondary':'text-primary') }}">{{ $pct }}%</span>
                            </div>
                            <div style="height:3px;background:#e9ecef;border-radius:4px;overflow:hidden;">
                                <div style="height:100%;width:{{ $pct }}%;background:{{ $pct==100?'#22c55e':'#3b82f6' }};border-radius:4px;"></div>
                            </div>
                        </div>

                        @if($stat['total']>0)
                        <div class="d-flex gap-1 flex-wrap" style="font-size:0.65rem;">
                            <span style="background:#fee2e2;color:#dc2626;padding:1px 7px;border-radius:999px;">{{ $stat['belum'] }} Belum</span>
                            <span style="background:#fef9c3;color:#ca8a04;padding:1px 7px;border-radius:999px;">{{ $stat['proses'] }} Proses</span>
                            <span style="background:#dcfce7;color:#16a34a;padding:1px 7px;border-radius:999px;">{{ $stat['selesai'] }} Selesai</span>
                        </div>
                        @else
                        <div style="font-size:0.7rem;color:#9ca3af;">Tidak ada permintaan data</div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3">
                        @if($stat['total']>0)
                        <a href="{{ route('opd.show', urlencode($stat['opd'])) }}{{ !empty($filterSuratIds)?'?surat_id='.($filterSuratIds[0]??''):'' }}"
                           class="btn btn-sm w-100"
                           style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:0.75rem;">
                            <i class="bi bi-eye me-1"></i>Lihat Detail
                        </a>
                        @else
                        <button class="btn btn-sm w-100 disabled" style="background:#f3f4f6;color:#9ca3af;border:1px solid #e5e7eb;font-size:0.75rem;">
                            <i class="bi bi-dash me-1"></i>Belum Ada Data
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-building d-block mb-2" style="font-size:2.5rem;opacity:0.3;"></i>
                <div style="font-size:0.85rem;">Belum ada data permintaan</div>
            </div>
            @endforelse
        </div>
        <div id="gridEmpty" style="display:none;" class="text-center text-muted py-5 card border-0 shadow-sm mt-2" style="border-radius:10px;">
            <i class="bi bi-search d-block mb-2" style="font-size:2rem;opacity:0.3;"></i>
            <div style="font-size:0.85rem;">OPD tidak ditemukan</div>
        </div>
    </div>

    {{-- List View --}}
    <div id="viewList" style="display:none;">
        <div class="card shadow-sm border-0" style="border-radius:10px;">
            <div class="card-body p-0">
                @forelse($stats as $stat)
                @php $pct = $stat['total']>0 ? round(($stat['selesai']+$stat['proses'])/$stat['total']*100) : 0; @endphp
                <div class="opd-item d-flex align-items-center gap-3 px-3 py-2 border-bottom"
                     data-opd="{{ strtolower($stat['opd']) }}"
                     style="transition:background 0.1s;{{ $stat['total']==0?'opacity:0.6;':'' }}"
                     onmouseenter="this.style.background='#f8fafc'"
                     onmouseleave="this.style.background=''">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:28px;height:28px;background:{{ $stat['total']==0?'#f3f4f6':'#eff6ff' }};">
                        <i class="bi bi-building {{ $stat['total']==0?'text-secondary':'text-primary' }}" style="font-size:0.72rem;"></i>
                    </div>
                    <div class="fw-semibold flex-grow-1" style="font-size:0.8rem;min-width:0;">{{ $stat['opd'] }}</div>
                    <div class="d-none d-sm-flex gap-1 flex-shrink-0" style="font-size:0.65rem;">
                        @if($stat['total']>0)
                        <span style="background:#fee2e2;color:#dc2626;padding:1px 7px;border-radius:999px;">{{ $stat['belum'] }} Belum</span>
                        <span style="background:#fef9c3;color:#ca8a04;padding:1px 7px;border-radius:999px;">{{ $stat['proses'] }} Proses</span>
                        <span style="background:#dcfce7;color:#16a34a;padding:1px 7px;border-radius:999px;">{{ $stat['selesai'] }} Selesai</span>
                        @else
                        <span style="background:#f3f4f6;color:#9ca3af;padding:1px 7px;border-radius:999px;">Belum ada data</span>
                        @endif
                    </div>
                    <div class="d-none d-md-block flex-shrink-0" style="width:90px;">
                        <div class="d-flex justify-content-between mb-1" style="font-size:0.62rem;">
                            <span class="text-muted"></span>
                            <span class="fw-bold {{ $pct==100?'text-success':($stat['total']==0?'text-secondary':'text-primary') }}">{{ $pct }}%</span>
                        </div>
                        <div style="height:3px;background:#e9ecef;border-radius:4px;overflow:hidden;">
                            <div style="height:100%;width:{{ $pct }}%;background:{{ $pct==100?'#22c55e':'#3b82f6' }};border-radius:4px;"></div>
                        </div>
                    </div>
                    <span class="badge flex-shrink-0 bg-secondary" style="font-size:0.65rem;">{{ $stat['total'] }}</span>
                    @if($stat['total']>0)
                    <a href="{{ route('opd.show', urlencode($stat['opd'])) }}{{ !empty($filterSuratIds)?'?surat_id='.($filterSuratIds[0]??''):'' }}"
                       class="btn btn-sm flex-shrink-0"
                       style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:0.7rem;padding:2px 10px;">
                        <i class="bi bi-eye me-1"></i>Detail
                    </a>
                    @else
                    <span class="btn btn-sm flex-shrink-0 disabled" style="background:#f3f4f6;color:#9ca3af;border:1px solid #e5e7eb;font-size:0.7rem;padding:2px 10px;">
                        <i class="bi bi-dash"></i>
                    </span>
                    @endif
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-building d-block mb-2" style="font-size:2.5rem;opacity:0.3;"></i>
                    <div style="font-size:0.85rem;">Belum ada data permintaan</div>
                </div>
                @endforelse
            </div>
        </div>
        <div id="listEmpty" style="display:none;" class="text-center text-muted py-5 card shadow-sm border-0 mt-2">
            <i class="bi bi-search d-block mb-2" style="font-size:2rem;opacity:0.3;"></i>
            <div style="font-size:0.85rem;">OPD tidak ditemukan</div>
        </div>
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
        ? 'btn btn-sm px-2 py-1'
        : 'btn btn-sm px-2 py-1 btn-outline-secondary';
    document.getElementById('btnGrid').style.cssText = isGrid
        ? 'background:#0b192c;color:#fff;border:0;font-size:0.78rem;'
        : 'font-size:0.78rem;';
    document.getElementById('btnList').className = isGrid
        ? 'btn btn-sm px-2 py-1 btn-outline-secondary'
        : 'btn btn-sm px-2 py-1';
    document.getElementById('btnList').style.cssText = isGrid
        ? 'font-size:0.78rem;'
        : 'background:#0b192c;color:#fff;border:0;font-size:0.78rem;';
    localStorage.setItem('opdView', mode);
    filterOpd(document.getElementById('searchOpd').value);
}

function filterOpd(q) {
    q = q.toLowerCase().trim();
    let gridVisible = 0, listVisible = 0;
    document.querySelectorAll('#viewGrid .opd-item').forEach(el => {
        const show = el.dataset.opd.includes(q);
        el.style.display = show ? '' : 'none';
        if (show) gridVisible++;
    });
    document.querySelectorAll('#viewList .opd-item').forEach(el => {
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

    const savedView = localStorage.getItem('opdView') || 'grid';
    setView(savedView);
</script>
@endsection
