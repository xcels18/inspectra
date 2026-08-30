@extends('layouts.app')
@section('title', 'Edit Pengguna')

@section('styles')
<style>
.section-card { border:0; box-shadow:0 1px 4px rgba(0,0,0,0.07); border-radius:10px; margin-bottom:1rem; }
.section-card .card-header { border-radius:10px 10px 0 0 !important; background:#f8fafc; border-bottom:1px solid #e9ecef; padding:0.65rem 1.1rem; font-size:0.85rem; font-weight:600; }
.form-label { font-size:0.8rem; font-weight:600; margin-bottom:0.3rem; color:#374151; }
.form-control, .form-select { font-size:0.82rem; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3" style="max-width:720px;">
    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); border-radius:10px; position:relative;">
        <div style="position:absolute; top:0; right:0; bottom:0; left:0; background:radial-gradient(circle at top right, rgba(255,255,255,0.06), transparent 60%); pointer-events:none;"></div>
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 position-relative z-1">
            <div>
                <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square"></i> Edit Pengguna
                </h5>
                <div style="font-size:0.75rem; color:#94a3b8; margin-top:2px;">{{ $user->name }}</div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('users.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.3); font-size:0.78rem;">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf @method('PUT')
        <div class="card section-card">
            <div class="card-header">
                <i class="bi bi-person-badge me-2 text-primary"></i>Informasi Akun
            </div>
            <div class="card-body pt-3 pb-3">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin Pengelola</option>
                            <option value="tim_bpk" {{ $user->role === 'tim_bpk' ? 'selected' : '' }}>Tim BPK</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active" style="font-size:0.82rem; color:#374151;">Akun Aktif</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card section-card mb-3">
            <div class="card-header">
                <i class="bi bi-key me-2 text-primary"></i>Ubah Password Akun
            </div>
            <div class="card-body pt-3 pb-3">
                <div class="text-muted small mb-3"><i class="bi bi-info-circle me-1"></i>Kosongkan password jika tidak ingin mengubah</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        {{-- Pengaturan PIN Akses Cepat --}}
        <div class="card section-card mb-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-shield-lock me-2 text-warning"></i>Pengaturan PIN Akses Cepat</span>
                <span class="badge bg-primary text-white" style="font-size:0.65rem;">LOKAL MODE</span>
            </div>
            <div class="card-body pt-3 pb-3">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label">Set Ulang PIN Akses Cepat (6 Digit)</label>
                        <input type="text" name="quick_pin" class="form-control" placeholder="Contoh: 121212" maxlength="10">
                        <div class="form-text" style="font-size:0.72rem;">Kosongkan jika tidak ingin mengubah PIN saat ini.</div>
                    </div>
                    <div class="col-md-5 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="reset_pin" value="1" id="reset_pin">
                            <label class="form-check-label fw-bold text-danger" for="reset_pin" style="font-size:0.8rem;">
                                Reset PIN ke Default (121212)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mt-3">
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary px-4">Batal</a>
            <button type="submit" class="btn btn-sm px-4 fw-semibold" style="background:#0b192c; color:#fff; border:0; font-size:0.82rem;">
                <i class="bi bi-save me-1"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
