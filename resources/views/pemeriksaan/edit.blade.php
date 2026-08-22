@extends('layouts.app')
@section('title', 'Edit Pemeriksaan')

@section('styles')
<style>
.section-card { border:0; box-shadow:0 1px 4px rgba(0,0,0,0.07); border-radius:10px; margin-bottom:1rem; }
.section-card .card-header { border-radius:10px 10px 0 0 !important; background:#f8fafc; border-bottom:1px solid #e9ecef; padding:0.65rem 1.1rem; font-size:0.85rem; font-weight:600; }
.form-label { font-size:0.8rem; font-weight:600; margin-bottom:0.3rem; color:#374151; }
.form-control, .form-select { font-size:0.82rem; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3" style="max-width:960px;">
    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); border-radius:10px; position:relative;">
        <div style="position:absolute; top:0; right:0; bottom:0; left:0; background:radial-gradient(circle at top right, rgba(255,255,255,0.06), transparent 60%); pointer-events:none;"></div>
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 position-relative z-1">
            <div>
                <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square"></i> Edit Pemeriksaan
                </h5>
                <div style="font-size:0.75rem; color:#94a3b8; margin-top:2px;">{{ $pemeriksaan->nama }}</div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('pemeriksaan.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.3); font-size:0.78rem;">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('pemeriksaan.update', $pemeriksaan->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card section-card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2 text-primary"></i>Informasi Pemeriksaan
            </div>
            <div class="card-body pt-3 pb-3">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nama Pemeriksaan <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $pemeriksaan->nama) }}" required>
                        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tahun Anggaran <span class="text-danger">*</span></label>
                        <input type="text" name="tahun" class="form-control @error('tahun') is-invalid @enderror" value="{{ old('tahun', $pemeriksaan->tahun) }}" required maxlength="4">
                        @error('tahun') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai', $pemeriksaan->tanggal_mulai ? $pemeriksaan->tanggal_mulai->format('Y-m-d') : '') }}">
                        @error('tanggal_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai', $pemeriksaan->tanggal_selesai ? $pemeriksaan->tanggal_selesai->format('Y-m-d') : '') }}">
                        @error('tanggal_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="aktif" {{ old('status', $pemeriksaan->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" {{ old('status', $pemeriksaan->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Keterangan (Opsional)</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $pemeriksaan->keterangan) }}</textarea>
                        @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card section-card">
            <div class="card-header">
                <i class="bi bi-people me-2 text-primary"></i>Akses Pengguna (Tim Pemeriksa)
            </div>
            <div class="card-body pt-3 pb-3">
                <div class="alert alert-info py-2" style="font-size:0.8rem;">
                    <i class="bi bi-info-circle me-1"></i>Pilih pengguna yang berhak melihat dan mengelola data pada pemeriksaan ini. Admin secara otomatis memiliki akses penuh.
                </div>
                <div class="row g-2">
                    @foreach($users as $user)
                    <div class="col-md-4 col-sm-6">
                        <div class="form-check border rounded p-2" style="background:#f8fafc;">
                            <input class="form-check-input ms-1" type="checkbox" name="user_ids[]" value="{{ $user->id }}" id="user_{{ $user->id }}" {{ in_array($user->id, old('user_ids', $assignedUsers)) ? 'checked' : '' }}>
                            <label class="form-check-label w-100 ms-2" for="user_{{ $user->id }}" style="cursor:pointer; font-size:0.82rem;">
                                <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                <div class="text-muted" style="font-size:0.7rem;">{{ $user->role_label }}</div>
                            </label>
                        </div>
                    </div>
                    @endforeach
                    @if($users->isEmpty())
                    <div class="col-12 text-muted" style="font-size:0.8rem;">Tidak ada pengguna selain admin.</div>
                    @endif
                </div>
                @error('user_ids') <div class="text-danger mt-2" style="font-size:0.8rem;">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mt-3">
            <a href="{{ route('pemeriksaan.index') }}" class="btn btn-sm btn-outline-secondary px-4">
                Batal
            </a>
            <button type="submit" class="btn btn-sm px-4 fw-semibold"
                    style="background:#0b192c; color:#fff; border:0; font-size:0.82rem;">
                <i class="bi bi-save me-1"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
