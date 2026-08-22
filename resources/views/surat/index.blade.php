@extends('layouts.app')
@section('title', 'Daftar Surat')

@section('content')
<div class="container-fluid py-3" style="max-width:1200px;">

    {{-- Header --}}
    <div class="card shadow-sm mb-3 border-0" style="background:linear-gradient(135deg,#1e40af 0%,#1e3a8a 100%);">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center g-2">
                <div class="col-auto">
                    <h5 class="mb-0 fw-bold text-white">
                        <i class="bi bi-envelope-paper me-2"></i>Surat Permintaan Data BPK
                    </h5>
                </div>
                <div class="col">
                    <form method="GET" action="{{ route('surat.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="input-group input-group-sm" style="max-width:260px;">
                            <span class="input-group-text" style="background:rgba(255,255,255,0.15); color:#fff; border-color:rgba(255,255,255,0.3);">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control form-control-sm"
                                   placeholder="Nomor / perihal..."
                                   value="{{ request('search') }}"
                                   style="background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.3); font-size:0.78rem;">
                        </div>
                        <select name="status" class="form-select form-select-sm"
                                style="max-width:130px; background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.3); font-size:0.78rem;"
                                onchange="this.form.submit()">
                            <option value="" style="color:#000;">Semua Status</option>
                            <option value="aktif"   style="color:#000;" {{ request('status') === 'aktif'   ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" style="color:#000;" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="arsip"   style="color:#000;" {{ request('status') === 'arsip'   ? 'selected' : '' }}>Arsip</option>
                        </select>
                        <select name="tahun" class="form-select form-select-sm"
                                style="max-width:110px; background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.3); font-size:0.78rem;"
                                onchange="this.form.submit()">
                            <option value="" style="color:#000;">Semua Tahun</option>
                            @foreach($tahunList as $tahun)
                            <option value="{{ $tahun }}" style="color:#000;" {{ request('tahun') === $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm" style="background:rgba(255,255,255,0.2); color:#fff; border-color:rgba(255,255,255,0.3); font-size:0.78rem;">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                        @if(request('search') || request('status') || request('tahun'))
                        <a href="{{ route('surat.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.1); color:#fff; border-color:rgba(255,255,255,0.3); font-size:0.78rem;">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </form>
                </div>
                <div class="col-auto d-flex align-items-center gap-2">
                    @php
                        $totalSurat  = $suratList->total();
                        $aktifCount  = \App\Models\Surat::where('status','aktif')->count();
                        $selesaiCount= \App\Models\Surat::where('status','selesai')->count();
                    @endphp
                    <div class="d-flex align-items-center gap-1 px-3 py-1 rounded-pill" style="background:rgba(255,255,255,0.15); font-size:0.78rem; color:#fff;">
                        <i class="bi bi-envelope me-1"></i>{{ $totalSurat }} surat
                    </div>
                    <div class="d-flex align-items-center gap-1 px-3 py-1 rounded-pill" style="background:rgba(99,102,241,0.4); font-size:0.78rem; color:#c7d2fe;">
                        <i class="bi bi-lightning me-1"></i>{{ $aktifCount }} aktif
                    </div>
                    <div class="d-flex align-items-center gap-1 px-3 py-1 rounded-pill" style="background:rgba(52,211,153,0.25); font-size:0.78rem; color:#a7f3d0;">
                        <i class="bi bi-check-circle me-1"></i>{{ $selesaiCount }} selesai
                    </div>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('surat.create') }}" class="btn btn-sm fw-semibold"
                       style="background:#fff; color:#1e40af; font-size:0.78rem;">
                        <i class="bi bi-plus-lg me-1"></i>Tambah
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="d-flex align-items-center justify-content-between mb-3 gap-2">
        <div class="text-muted" style="font-size:0.78rem;">
            Menampilkan {{ $suratList->firstItem() }}–{{ $suratList->lastItem() }} dari {{ $suratList->total() }} surat
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
        <div class="row g-3">
            @forelse($suratList as $surat)
            @php
                $pct        = $surat->opd_progress;
                $statusColor = match($surat->status) {
                    'aktif'   => ['bg'=>'#eff6ff','border'=>'#bfdbfe','text'=>'#1d4ed8','label'=>'Aktif'],
                    'selesai' => ['bg'=>'#f0fdf4','border'=>'#bbf7d0','text'=>'#15803d','label'=>'Selesai'],
                    'arsip'   => ['bg'=>'#f9fafb','border'=>'#e5e7eb','text'=>'#6b7280','label'=>'Arsip'],
                    default   => ['bg'=>'#f9fafb','border'=>'#e5e7eb','text'=>'#6b7280','label'=>$surat->status],
                };
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm" style="transition:box-shadow 0.15s;"
                     onmouseenter="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.1)'"
                     onmouseleave="this.style.boxShadow=''">
                    <div class="card-body pb-2">
                        {{-- Header --}}
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <div class="d-flex align-items-start gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:34px; height:34px; background:#eff6ff;">
                                    <i class="bi bi-envelope text-primary" style="font-size:0.9rem;"></i>
                                </div>
                                <div>
                                    <a href="{{ route('surat.show', $surat) }}"
                                       class="fw-semibold text-decoration-none text-dark lh-sm d-block"
                                       style="font-size:0.82rem;">
                                        {{ $surat->nomor_surat }}
                                        @if($surat->file_surat)
                                        <i class="bi bi-paperclip text-muted ms-1" title="Ada file surat" style="font-size:0.7rem;"></i>
                                        @endif
                                    </a>
                                    <span style="font-size:0.68rem; background:{{ $statusColor['bg'] }}; color:{{ $statusColor['text'] }}; border:1px solid {{ $statusColor['border'] }}; padding:1px 7px; border-radius:999px;">
                                        {{ $statusColor['label'] }}
                                    </span>
                                </div>
                            </div>
                            <span class="text-muted flex-shrink-0" style="font-size:0.7rem;">{{ $surat->tahun_anggaran }}</span>
                        </div>

                        {{-- Perihal --}}
                        <div class="text-muted mb-3 lh-sm" style="font-size:0.77rem;">
                            {{ Str::limit($surat->perihal, 80) }}
                        </div>

                        {{-- Meta --}}
                        <div class="d-flex align-items-center gap-3 mb-3" style="font-size:0.7rem; color:#9ca3af;">
                            <span><i class="bi bi-calendar me-1"></i>{{ $surat->tanggal_terima->format('d/m/Y') }}</span>
                            @if($surat->deadline)
                            <span class="{{ $surat->deadline->isPast() && $surat->status !== 'selesai' ? 'text-danger fw-semibold' : '' }}">
                                <i class="bi bi-alarm me-1"></i>{{ $surat->deadline->format('d/m/Y') }}
                            </span>
                            @endif
                        </div>

                        {{-- Progress --}}
                        <div>
                            <div class="d-flex justify-content-between mb-1" style="font-size:0.7rem;">
                                <span class="text-muted">{{ $surat->opd_selesai + $surat->opd_proses }}/{{ $surat->opd_total }} OPD</span>
                                <span class="fw-bold {{ $pct == 100 ? 'text-success' : 'text-primary' }}">{{ $pct }}%</span>
                            </div>
                            <div style="height:5px; background:#e9ecef; border-radius:4px; overflow:hidden;">
                                <div style="height:100%; width:{{ $pct }}%; background:{{ $pct == 100 ? '#22c55e' : '#3b82f6' }}; border-radius:4px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3">
                        <div class="d-flex gap-1">
                            <a href="{{ route('surat.show', $surat) }}"
                               class="btn btn-sm flex-grow-1"
                               style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.78rem;">
                                <i class="bi bi-eye me-1"></i>Detail
                            </a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('surat.edit', $surat) }}"
                               class="btn btn-sm"
                               style="background:#f9fafb; color:#6b7280; border:1px solid #e5e7eb; font-size:0.78rem;"
                               title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('surat.destroy', $surat) }}" method="POST"
                                  onsubmit="return confirm('Hapus surat ini beserta semua data permintaannya?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm"
                                        style="background:#fff5f5; color:#dc2626; border:1px solid #fecaca; font-size:0.78rem;"
                                        title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-inbox" style="font-size:2.5rem;"></i>
                <div class="mt-2" style="font-size:0.85rem;">Tidak ada surat ditemukan</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- List View --}}
    <div id="viewList" style="display:none;">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                @forelse($suratList as $surat)
                @php
                    $pct = $surat->opd_progress;
                    $statusColor = match($surat->status) {
                        'aktif'   => ['bg'=>'#eff6ff','border'=>'#bfdbfe','text'=>'#1d4ed8','label'=>'Aktif'],
                        'selesai' => ['bg'=>'#f0fdf4','border'=>'#bbf7d0','text'=>'#15803d','label'=>'Selesai'],
                        'arsip'   => ['bg'=>'#f9fafb','border'=>'#e5e7eb','text'=>'#6b7280','label'=>'Arsip'],
                        default   => ['bg'=>'#f9fafb','border'=>'#e5e7eb','text'=>'#6b7280','label'=>$surat->status],
                    };
                @endphp
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom"
                     style="transition:background 0.12s;"
                     onmouseenter="this.style.background='#f8f9fa'"
                     onmouseleave="this.style.background=''">
                    {{-- Icon --}}
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:30px; height:30px; background:#eff6ff;">
                        <i class="bi bi-envelope text-primary" style="font-size:0.8rem;"></i>
                    </div>

                    {{-- Nomor & perihal --}}
                    <div style="flex:1; min-width:0;">
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('surat.show', $surat) }}"
                               class="fw-semibold text-decoration-none text-dark"
                               style="font-size:0.82rem;">{{ $surat->nomor_surat }}</a>
                            @if($surat->file_surat)
                            <i class="bi bi-paperclip text-muted" style="font-size:0.7rem;" title="Ada file surat"></i>
                            @endif
                            <span style="font-size:0.65rem; background:{{ $statusColor['bg'] }}; color:{{ $statusColor['text'] }}; border:1px solid {{ $statusColor['border'] }}; padding:1px 7px; border-radius:999px;">
                                {{ $statusColor['label'] }}
                            </span>
                        </div>
                        <div class="text-muted text-truncate" style="font-size:0.75rem; max-width:420px;">{{ $surat->perihal }}</div>
                    </div>

                    {{-- Tahun & tanggal --}}
                    <div class="d-none d-md-flex flex-column align-items-end flex-shrink-0" style="font-size:0.7rem; color:#9ca3af;">
                        <span>{{ $surat->tahun_anggaran }}</span>
                        <span>{{ $surat->tanggal_terima->format('d/m/Y') }}</span>
                    </div>

                    {{-- Progress --}}
                    <div class="d-none d-lg-block flex-shrink-0" style="width:100px;">
                        <div class="d-flex justify-content-between mb-1" style="font-size:0.65rem;">
                            <span class="text-muted">{{ $surat->opd_selesai + $surat->opd_proses }}/{{ $surat->opd_total }}</span>
                            <span class="fw-bold {{ $pct == 100 ? 'text-success' : 'text-primary' }}">{{ $pct }}%</span>
                        </div>
                        <div style="height:4px; background:#e9ecef; border-radius:4px; overflow:hidden;">
                            <div style="height:100%; width:{{ $pct }}%; background:{{ $pct == 100 ? '#22c55e' : '#3b82f6' }}; border-radius:4px;"></div>
                        </div>
                    </div>

                    {{-- Aksi --}}
                    <div class="d-flex gap-1 flex-shrink-0">
                        <a href="{{ route('surat.show', $surat) }}"
                           class="btn btn-sm"
                           style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.72rem; padding:2px 10px;">
                            <i class="bi bi-eye me-1"></i>Detail
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('surat.edit', $surat) }}"
                           class="btn btn-sm"
                           style="background:#f9fafb; color:#6b7280; border:1px solid #e5e7eb; font-size:0.72rem; padding:2px 8px;"
                           title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('surat.destroy', $surat) }}" method="POST"
                              onsubmit="return confirm('Hapus surat ini beserta semua data permintaannya?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm"
                                    style="background:#fff5f5; color:#dc2626; border:1px solid #fecaca; font-size:0.72rem; padding:2px 8px;"
                                    title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox" style="font-size:2.5rem;"></i>
                    <div class="mt-2" style="font-size:0.85rem;">Tidak ada surat ditemukan</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if($suratList->hasPages())
    <div class="mt-3 d-flex justify-content-between align-items-center">
        <small class="text-muted" style="font-size:0.75rem;">
            Halaman {{ $suratList->currentPage() }} dari {{ $suratList->lastPage() }}
        </small>
        {{ $suratList->links('pagination::bootstrap-5') }}
    </div>
    @endif

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
    localStorage.setItem('suratView', mode);
}

const savedView = localStorage.getItem('suratView') || 'list';
setView(savedView);
</script>
@endsection
