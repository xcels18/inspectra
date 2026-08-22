@extends('layouts.app')
@section('title', 'Daftar Surat')

@section('content')
<div class="container-fluid py-3" style="max-width:1200px;">

    {{-- Header --}}
    <div class="card shadow-sm mb-3 border-0 overflow-hidden" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); position:relative;">
        <!-- decorative overlay -->
        <div style="position:absolute; top:0; right:0; bottom:0; left:0; background:radial-gradient(circle at top right, rgba(255,255,255,0.06), transparent 60%); pointer-events:none;"></div>
        
        <div class="card-body py-3 px-4 position-relative z-1">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                    <i class="bi bi-envelope-paper"></i> Surat Permintaan Data BPK
                </h5>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    @php
                        $totalSurat  = $suratList->total();
                        $aktifCount  = \App\Models\Surat::where('status','aktif')->count();
                        $selesaiCount= \App\Models\Surat::where('status','selesai')->count();
                    @endphp
                    <div class="d-flex align-items-center gap-1 px-3 py-1 rounded-pill" style="background:rgba(255,255,255,0.1); font-size:0.75rem; color:#e2e8f0; border:1px solid rgba(255,255,255,0.15);">
                        <i class="bi bi-envelope me-1"></i>{{ $totalSurat }} surat
                    </div>
                    <div class="d-flex align-items-center gap-1 px-3 py-1 rounded-pill" style="background:rgba(59,130,246,0.2); font-size:0.75rem; color:#93c5fd; border:1px solid rgba(59,130,246,0.3);">
                        <i class="bi bi-lightning me-1"></i>{{ $aktifCount }} aktif
                    </div>
                    <div class="d-flex align-items-center gap-1 px-3 py-1 rounded-pill" style="background:rgba(16,185,129,0.2); font-size:0.75rem; color:#6ee7b7; border:1px solid rgba(16,185,129,0.3);">
                        <i class="bi bi-check-circle me-1"></i>{{ $selesaiCount }} selesai
                    </div>
                    @if(auth()->user()->isAdmin() || $pemeriksaanList->count() > 0)
                    <a href="{{ route('surat.create') }}" class="btn btn-sm fw-semibold shadow-sm"
                       style="background:#fff; color:#0b192c; font-size:0.78rem; margin-left:0.5rem; transition:transform 0.15s;" 
                       onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                        <i class="bi bi-plus-lg me-1"></i>Tambah
                    </a>
                    @endif
                </div>
            </div>

            <!-- Filters -->
            <div class="p-2 rounded-3" style="background:rgba(0,0,0,0.18); border:1px solid rgba(255,255,255,0.06);">
                <form method="GET" action="{{ route('surat.index') }}" class="row g-2 align-items-center m-0">
                    <div class="col-12 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text border-0" style="background:rgba(255,255,255,0.08); color:#cbd5e1;">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control form-control-sm border-0"
                                   placeholder="Cari nomor surat / perihal..."
                                   value="{{ request('search') }}"
                                   style="background:rgba(255,255,255,0.08); color:#fff; font-size:0.78rem; box-shadow:none;">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <select name="status" class="form-select form-select-sm border-0"
                                style="background:rgba(255,255,255,0.08); color:#fff; font-size:0.78rem; box-shadow:none;"
                                onchange="this.form.submit()">
                            <option value="semua" style="color:#000;" {{ request('status') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                            <option value="aktif"   style="color:#000;" {{ request('status') === 'aktif'   ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" style="color:#000;" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="arsip"   style="color:#000;" {{ request('status') === 'arsip'   ? 'selected' : '' }}>Arsip</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <select name="tahun" class="form-select form-select-sm border-0"
                                style="background:rgba(255,255,255,0.08); color:#fff; font-size:0.78rem; box-shadow:none;"
                                onchange="this.form.submit()">
                            <option value="" style="color:#000;">Semua Tahun</option>
                            @foreach($tahunList as $tahun)
                            <option value="{{ $tahun }}" style="color:#000;" {{ request('tahun') === $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-9 col-md-3">
                        <select name="pemeriksaan_id" class="form-select form-select-sm border-0"
                                style="background:rgba(255,255,255,0.08); color:#fff; font-size:0.78rem; box-shadow:none;"
                                onchange="this.form.submit()">
                            <option value="" style="color:#000;">Semua Pemeriksaan</option>
                            <option value="null" style="color:#000;" {{ request('pemeriksaan_id') === 'null' ? 'selected' : '' }}>Belum Dipetakan</option>
                            @foreach($pemeriksaanList as $p)
                            <option value="{{ $p->id }}" style="color:#000;" {{ request('pemeriksaan_id') == $p->id ? 'selected' : '' }}>{{ Str::limit($p->nama, 25) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-3 col-md-1 d-flex gap-1 justify-content-end">
                        <button type="submit" class="btn btn-sm flex-grow-1" style="background:rgba(255,255,255,0.15); color:#fff; font-size:0.78rem; border:1px solid rgba(255,255,255,0.1);">
                            Cari
                        </button>
                        @if(request('search') || request('status') || request('tahun') || request('pemeriksaan_id'))
                        <a href="{{ route('surat.index') }}" class="btn btn-sm" style="background:rgba(239,68,68,0.2); color:#fca5a5; font-size:0.78rem; border:1px solid rgba(239,68,68,0.3);" title="Reset Filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </form>
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
                        <div class="text-muted mb-2 lh-sm" style="font-size:0.77rem; min-height:2.4rem;">
                            {{ Str::limit($surat->perihal, 75) }}
                        </div>

                        {{-- Pemeriksaan --}}
                        <div class="mb-3">
                            @if($surat->pemeriksaan)
                                <a href="{{ route('pemeriksaan.show', $surat->pemeriksaan->id) }}" class="text-decoration-none text-truncate d-inline-block w-100" style="font-size:0.68rem; background:#f3f4f6; color:#4b5563; padding:2px 8px; border-radius:4px; border:1px solid #e5e7eb;">
                                    <i class="bi bi-folder2-open me-1"></i>{{ $surat->pemeriksaan->nama }}
                                </a>
                            @else
                                <span class="text-truncate d-inline-block w-100" style="font-size:0.68rem; background:#fffbeb; color:#d97706; padding:2px 8px; border-radius:4px; border:1px solid #fde68a;">
                                    <i class="bi bi-exclamation-circle me-1"></i>Belum Dipetakan
                                </span>
                            @endif
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
                            @php
                                $canEdit = auth()->user()->isAdmin() || ($surat->pemeriksaan && $surat->pemeriksaan->users->contains('id', auth()->id()));
                            @endphp
                            @if($canEdit)
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
                        <div class="text-muted text-truncate mb-1" style="font-size:0.75rem; max-width:420px;">{{ $surat->perihal }}</div>
                        <div class="d-flex align-items-center mt-1">
                            @if($surat->pemeriksaan)
                                <a href="{{ route('pemeriksaan.show', $surat->pemeriksaan->id) }}" class="text-decoration-none text-truncate" style="font-size:0.65rem; background:#f3f4f6; color:#4b5563; padding:1px 6px; border-radius:4px; max-width:250px;">
                                    <i class="bi bi-folder2-open me-1"></i>{{ $surat->pemeriksaan->nama }}
                                </a>
                            @else
                                <span class="text-truncate" style="font-size:0.65rem; background:#fffbeb; color:#d97706; padding:1px 6px; border-radius:4px;">
                                    <i class="bi bi-exclamation-circle me-1"></i>Belum Dipetakan
                                </span>
                            @endif
                        </div>
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
                        @php
                            $canEdit = auth()->user()->isAdmin() || ($surat->pemeriksaan && $surat->pemeriksaan->users->contains('id', auth()->id()));
                        @endphp
                        @if($canEdit)
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
