@extends('layouts.app')
@section('title', 'Master OPD')
@section('page-title', 'Master OPD')

@section('content')
<div class="card border-0 shadow-sm mb-4" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); border-radius:10px;">
    <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                <i class="bi bi-building"></i> Master OPD
            </h5>
            <div style="font-size:0.75rem; color:#94a3b8; margin-top:2px;">Kelola daftar Organisasi Perangkat Daerah</div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('master-opd.export', ['search' => request('search')]) }}" class="btn btn-sm" style="background:rgba(16, 185, 129, 0.15); color:#10b981; border:1px solid rgba(16, 185, 129, 0.3); font-size:0.78rem;">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
            <a href="{{ route('master-opd.create') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.3); font-size:0.78rem;">
                <i class="bi bi-plus-lg me-1"></i>Tambah OPD
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" style="border-radius:10px; font-size:0.85rem;" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card border-0 shadow-sm" style="border-radius:10px;">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-radius:10px 10px 0 0; border-bottom:1px solid #f1f5f9;">
        <h6 class="mb-0 fw-bold" style="color:#1e293b; font-size:0.9rem;">Daftar OPD</h6>
        <form action="{{ route('master-opd.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group input-group-sm" style="width:250px;">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama OPD..." value="{{ $search }}">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
            </div>
            @if($search)
                <a href="{{ route('master-opd.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-x-lg text-danger"></i></a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.82rem;">
            <thead class="bg-light">
                <tr>
                    <th width="5%" class="text-center py-3 text-muted">NO</th>
                    <th class="py-3 text-muted">NAMA OPD</th>
                    <th width="20%" class="py-3 text-muted">KATEGORI</th>
                    <th width="15%" class="text-center py-3 text-muted">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($opds as $idx => $opd)
                <tr>
                    <td class="text-center text-muted">{{ $opds->firstItem() + $idx }}</td>
                    <td class="fw-semibold text-dark">{{ $opd->nama }}</td>
                    <td>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill px-3">{{ $opd->kategori ?? 'OPD' }}</span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="{{ route('master-opd.edit', $opd) }}" class="btn btn-sm btn-light border" title="Edit">
                                <i class="bi bi-pencil text-primary"></i>
                            </a>
                            <form action="{{ route('master-opd.destroy', $opd) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus OPD ini?');" style="display:inline-block;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border" title="Hapus">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox d-block mb-2" style="font-size:2rem; color:#cbd5e1;"></i>
                        Belum ada data OPD yang ditambahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($opds->hasPages())
    <div class="card-footer bg-white py-3 border-top-0">
        {{ $opds->links() }}
    </div>
    @endif
</div>
@endsection
