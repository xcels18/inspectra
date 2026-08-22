@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')

@section('content')
    {{-- Header --}}
    <div class="card shadow-sm mb-3 border-0 overflow-hidden" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); position:relative;">
        <!-- decorative overlay -->
        <div style="position:absolute; top:0; right:0; bottom:0; left:0; background:radial-gradient(circle at top right, rgba(255,255,255,0.06), transparent 60%); pointer-events:none;"></div>
        
        <div class="card-body py-3 px-4 position-relative z-1">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                    <i class="bi bi-people"></i> Manajemen Pengguna
                </h5>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    @php
                        $totalUsers = $users->total();
                        $adminCount = \App\Models\User::where('role','admin')->count();
                        $opdCount   = \App\Models\User::where('role','opd')->count();
                    @endphp
                    <div class="d-flex align-items-center gap-1 px-3 py-1 rounded-pill" style="background:rgba(255,255,255,0.1); font-size:0.75rem; color:#e2e8f0; border:1px solid rgba(255,255,255,0.15);">
                        <i class="bi bi-person me-1"></i>{{ $totalUsers }} pengguna
                    </div>
                    <div class="d-flex align-items-center gap-1 px-3 py-1 rounded-pill" style="background:rgba(59,130,246,0.2); font-size:0.75rem; color:#93c5fd; border:1px solid rgba(59,130,246,0.3);">
                        <i class="bi bi-shield-check me-1"></i>{{ $adminCount }} admin
                    </div>
                    <div class="d-flex align-items-center gap-1 px-3 py-1 rounded-pill" style="background:rgba(16,185,129,0.2); font-size:0.75rem; color:#6ee7b7; border:1px solid rgba(16,185,129,0.3);">
                        <i class="bi bi-building me-1"></i>{{ $opdCount }} opd
                    </div>
                    <a href="{{ route('users.create') }}" class="btn btn-sm fw-semibold shadow-sm"
                       style="background:#fff; color:#0b192c; font-size:0.78rem; margin-left:0.5rem; transition:transform 0.15s;" 
                       onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                        <i class="bi bi-person-plus me-1"></i>Tambah
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="p-2 rounded-3" style="background:rgba(0,0,0,0.18); border:1px solid rgba(255,255,255,0.06);">
                <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-center m-0">
                    <div class="col-12 col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text border-0" style="background:rgba(255,255,255,0.08); color:#cbd5e1;">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control form-control-sm border-0"
                                   placeholder="Cari nama / email..."
                                   value="{{ request('search') }}"
                                   style="background:rgba(255,255,255,0.08); color:#fff; font-size:0.78rem; box-shadow:none;">
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="role" class="form-select form-select-sm border-0"
                                style="background:rgba(255,255,255,0.08); color:#fff; font-size:0.78rem; box-shadow:none;"
                                onchange="this.form.submit()">
                            <option value="" style="color:#000;">Semua Role</option>
                            <option value="admin" style="color:#000;" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="opd"   style="color:#000;" {{ request('role') === 'opd'   ? 'selected' : '' }}>OPD</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="status" class="form-select form-select-sm border-0"
                                style="background:rgba(255,255,255,0.08); color:#fff; font-size:0.78rem; box-shadow:none;"
                                onchange="this.form.submit()">
                            <option value="" style="color:#000;">Semua Status</option>
                            <option value="aktif"     style="color:#000;" {{ request('status') === 'aktif'     ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif"  style="color:#000;" {{ request('status') === 'nonaktif'  ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-1 d-flex gap-1 justify-content-end">
                        <button type="submit" class="btn btn-sm flex-grow-1" style="background:rgba(255,255,255,0.15); color:#fff; font-size:0.78rem; border:1px solid rgba(255,255,255,0.1);">
                            Cari
                        </button>
                        @if(request('search') || request('role') || request('status'))
                        <a href="{{ route('users.index') }}" class="btn btn-sm" style="background:rgba(239,68,68,0.2); color:#fca5a5; font-size:0.78rem; border:1px solid rgba(239,68,68,0.3);" title="Reset Filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Tabel Data --}}
    <div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-3 text-muted">{{ $users->firstItem() + $loop->index }}</td>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-{{ $user->role === 'admin' ? 'primary' : 'info' }}">
                                {{ $user->role_label }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>
@endsection
