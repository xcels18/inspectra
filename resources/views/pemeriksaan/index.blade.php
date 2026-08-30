@extends('layouts.app')

@section('title', 'Daftar Pemeriksaan')

@section('content')
<div class="container-fluid py-3" style="max-width:1100px;">

    {{-- Header --}}
    <div class="card border-0 mb-3 overflow-hidden" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); border-radius:12px; position:relative;">
        <div style="position:absolute; top:0; right:0; bottom:0; left:0; background:radial-gradient(circle at top right, rgba(255,255,255,0.07), transparent 60%); pointer-events:none;"></div>
        <div class="card-body py-3 px-4 position-relative" style="z-index:1;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2" style="font-size:0.85rem;">
                        <i class="bi bi-folder2-open"></i> Daftar Pemeriksaan
                    </h5>
                    <div style="font-size:0.72rem; color:#94a3b8; margin-top:2px;">Kelola semua pemeriksaan dan surat permintaan data</div>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    @php
                        $totalPemeriksaan = $pemeriksaans->total();
                        $aktifCount  = \App\Models\Pemeriksaan::where('status','aktif')->count();
                        $selesaiCount = \App\Models\Pemeriksaan::where('status','selesai')->count();
                    @endphp
                    <span class="px-2 py-1 rounded-pill" style="background:rgba(255,255,255,0.1); font-size:0.72rem; color:#e2e8f0; border:1px solid rgba(255,255,255,0.12);">
                        <i class="bi bi-folder me-1"></i>{{ $totalPemeriksaan }} total
                    </span>
                    <span class="px-2 py-1 rounded-pill" style="background:rgba(59,130,246,0.2); font-size:0.72rem; color:#93c5fd; border:1px solid rgba(59,130,246,0.3);">
                        <i class="bi bi-lightning me-1"></i>{{ $aktifCount }} aktif
                    </span>
                    <span class="px-2 py-1 rounded-pill" style="background:rgba(16,185,129,0.2); font-size:0.72rem; color:#6ee7b7; border:1px solid rgba(16,185,129,0.3);">
                        <i class="bi bi-check-circle me-1"></i>{{ $selesaiCount }} selesai
                    </span>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('pemeriksaan.create') }}" class="btn btn-sm fw-semibold"
                       style="background:#fff; color:#0b192c; font-size:0.75rem; border:0;">
                        <i class="bi bi-plus-lg me-1"></i>Tambah
                    </a>
                    @endif
                </div>
            </div>

            {{-- Filters --}}
            <div class="p-2 rounded-3" style="background:rgba(0,0,0,0.18); border:1px solid rgba(255,255,255,0.06);">
                <form method="GET" action="{{ route('pemeriksaan.index') }}" class="row g-2 align-items-center m-0">
                    <div class="col-12 col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text border-0" style="background:rgba(255,255,255,0.08); color:#cbd5e1;">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control form-control-sm border-0"
                                   placeholder="Cari nama / keterangan..."
                                   value="{{ request('search') }}"
                                   style="background:rgba(255,255,255,0.08); color:#fff; font-size:0.78rem; box-shadow:none;">
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="status" class="form-select form-select-sm border-0"
                                style="background:rgba(255,255,255,0.08); color:#fff; font-size:0.78rem; box-shadow:none;"
                                onchange="this.form.submit()">
                            <option value="" style="color:#000;">Semua Status</option>
                            <option value="aktif"   style="color:#000;" {{ request('status') === 'aktif'   ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" style="color:#000;" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="tahun" class="form-select form-select-sm border-0"
                                style="background:rgba(255,255,255,0.08); color:#fff; font-size:0.78rem; box-shadow:none;"
                                onchange="this.form.submit()">
                            <option value="" style="color:#000;">Semua Tahun</option>
                            @foreach($tahunList as $tahun)
                            <option value="{{ $tahun }}" style="color:#000;" {{ request('tahun') === $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-1 d-flex gap-1 justify-content-end">
                        <button type="submit" class="btn btn-sm flex-grow-1" style="background:rgba(255,255,255,0.15); color:#fff; font-size:0.78rem; border:1px solid rgba(255,255,255,0.1);">
                            Cari
                        </button>
                        @if(request('search') || request('status') || request('tahun'))
                        <a href="{{ route('pemeriksaan.index') }}" class="btn btn-sm"
                           style="background:rgba(239,68,68,0.2); color:#fca5a5; font-size:0.78rem; border:1px solid rgba(239,68,68,0.3);" title="Reset">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Card List --}}
    @forelse($pemeriksaans as $index => $item)
    @php
        $sc = match($item->status) {
            'aktif'   => ['dot'=>'#22c55e','bg'=>'rgba(34,197,94,0.1)','bd'=>'rgba(34,197,94,0.25)','txt'=>'#4ade80','lbl'=>'Aktif'],
            'selesai' => ['dot'=>'#94a3b8','bg'=>'rgba(148,163,184,0.1)','bd'=>'rgba(148,163,184,0.25)','txt'=>'#94a3b8','lbl'=>'Selesai'],
            default   => ['dot'=>'#f59e0b','bg'=>'rgba(245,158,11,0.1)','bd'=>'rgba(245,158,11,0.25)','txt'=>'#fbbf24','lbl'=>ucfirst($item->status)],
        };
        $suratCount = $item->surat->count();
        $suratAktif = $item->surat->where('status','aktif')->count();
    @endphp
    <div class="card border-0 shadow-sm mb-2" style="border-radius:10px; transition:box-shadow 0.2s;"
         onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.1)'" 
         onmouseout="this.style.boxShadow='0 1px 4px rgba(0,0,0,0.06)'">
        <div class="card-body py-2 px-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                {{-- Left: name + meta --}}
                <div class="d-flex align-items-center gap-3 flex-grow-1" style="min-width:0;">
                    {{-- Index badge --}}
                    <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-3"
                         style="width:30px; height:30px; background:#f1f5f9; color:#64748b; font-size:0.75rem; font-weight:700;">
                        {{ $pemeriksaans->firstItem() + $index }}
                    </div>
                    <div style="min-width:0;">
                        <a href="{{ route('pemeriksaan.show', $item->id) }}" class="text-decoration-none">
                            <div class="fw-bold text-dark" style="font-size:0.78rem; line-height:1.3;">{{ $item->nama }}</div>
                        </a>
                        <div class="d-flex align-items-center flex-wrap gap-2 mt-1" style="font-size:0.68rem; color:#94a3b8;">
                            <span><i class="bi bi-calendar3 me-1"></i>{{ $item->tahun }}</span>
                            @if($item->tanggal_mulai)
                            <span><i class="bi bi-play-circle me-1"></i>{{ $item->tanggal_mulai->format('d/m/Y') }}</span>
                            @endif
                            @if($item->tanggal_selesai)
                            <span><i class="bi bi-stop-circle me-1"></i>{{ $item->tanggal_selesai->format('d/m/Y') }}</span>
                            @endif
                            @if($item->keterangan)
                            <span class="text-truncate" style="max-width:250px;"><i class="bi bi-info-circle me-1"></i>{{ $item->keterangan }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Middle: stats --}}
                <div class="d-flex align-items-center gap-3 flex-shrink-0">
                    <div class="text-center" style="min-width:50px;">
                        <div class="fw-bold text-dark" style="font-size:0.85rem;">{{ $suratCount }}</div>
                        <div style="font-size:0.58rem; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Surat</div>
                    </div>
                    <div class="text-center" style="min-width:50px;">
                        <div class="fw-bold" style="font-size:0.85rem; color:#3b82f6;">{{ $suratAktif }}</div>
                        <div style="font-size:0.58rem; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Aktif</div>
                    </div>
                    @if($item->users->count() > 0)
                    <div class="d-flex align-items-center gap-1 px-2 py-1 rounded-2" style="background:#f8fafc; border:1px solid #e2e8f0; max-width:140px;">
                        <i class="bi bi-people text-muted flex-shrink-0" style="font-size:0.7rem;"></i>
                        <span class="text-truncate" style="font-size:0.62rem; color:#64748b;">{{ $item->users->pluck('name')->join(', ') }}</span>
                    </div>
                    @endif
                </div>

                {{-- Right: status + actions --}}
                <div class="d-flex flex-column align-items-end justify-content-center flex-shrink-0 gap-2" style="min-width:80px;">
                    <span class="badge rounded-pill fw-medium" style="background:{{ $sc['bg'] }}; color:{{ $sc['txt'] }}; border:1px solid {{ $sc['bd'] }}; font-size:0.62rem;">
                        <span class="rounded-circle d-inline-block me-1" style="width:6px; height:6px; background:{{ $sc['dot'] }};"></span>
                        {{ $sc['lbl'] }}
                    </span>
                    <div class="d-flex gap-1">
                        <a href="{{ route('pemeriksaan.show', $item->id) }}" class="btn btn-sm"
                           style="background:#f8f9fa; color:#374151; border:1px solid #e5e7eb; font-size:0.65rem; padding:2px 8px;" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('pemeriksaan.edit', $item->id) }}" class="btn btn-sm"
                           style="background:#f8f9fa; color:#374151; border:1px solid #e5e7eb; font-size:0.65rem; padding:2px 8px;" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('pemeriksaan.destroy', $item->id) }}" method="POST" class="d-inline-block m-0"
                              onsubmit="return confirm('Hapus pemeriksaan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm"
                                    style="background:#fff5f5; color:#dc2626; border:1px solid #fecaca; font-size:0.65rem; padding:2px 8px;" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card border-0 shadow-sm" style="border-radius:10px;">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-folder-x d-block mb-2" style="font-size:2.5rem; opacity:0.3;"></i>
            <div style="font-size:0.85rem;">Belum ada data pemeriksaan.</div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('pemeriksaan.create') }}" class="btn btn-sm mt-3"
               style="background:#0b192c; color:#fff; font-size:0.78rem; border:0;">
                <i class="bi bi-plus-lg me-1"></i>Tambah Pemeriksaan
            </a>
            @endif
        </div>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if($pemeriksaans->hasPages())
    <div class="mt-3">
        {{ $pemeriksaans->links('pagination::bootstrap-5') }}
    </div>
    @endif

</div>
@endsection
