@extends('layouts.app')
@section('title', 'Edit Master OPD')
@section('page-title', 'Edit Master OPD')

@section('styles')
<style>
.section-card { border:0; box-shadow:0 1px 4px rgba(0,0,0,0.07); border-radius:10px; margin-bottom:1rem; }
.section-card .card-header { border-radius:10px 10px 0 0 !important; background:#f8fafc; border-bottom:1px solid #e9ecef; padding:0.65rem 1.1rem; font-size:0.85rem; font-weight:600; }
.form-label { font-size:0.8rem; font-weight:600; margin-bottom:0.3rem; color:#374151; }
.form-control { font-size:0.82rem; }
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
                    <i class="bi bi-pencil-square"></i> Edit Master OPD
                </h5>
                <div style="font-size:0.75rem; color:#94a3b8; margin-top:2px;">Ubah data OPD</div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('master-opd.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.3); font-size:0.78rem;">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('master-opd.update', $masterOpd) }}" method="POST">
        @csrf @method('PUT')
        <div class="card section-card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2 text-primary"></i>Informasi OPD
            </div>
            <div class="card-body pt-3 pb-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nama OPD <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $masterOpd->nama) }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                            <option value="OPD" {{ old('kategori', $masterOpd->kategori) == 'OPD' ? 'selected' : '' }}>OPD</option>
                            <option value="Sekolah" {{ old('kategori', $masterOpd->kategori) == 'Sekolah' ? 'selected' : '' }}>Sekolah</option>
                            <option value="Partai Politik" {{ old('kategori', $masterOpd->kategori) == 'Partai Politik' ? 'selected' : '' }}>Partai Politik</option>
                            <option value="Instansi Vertical" {{ old('kategori', $masterOpd->kategori) == 'Instansi Vertical' ? 'selected' : '' }}>Instansi Vertical</option>
                        </select>
                        @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mt-3">
            <a href="{{ route('master-opd.index') }}" class="btn btn-sm btn-outline-secondary px-4">Batal</a>
            <button type="submit" class="btn btn-sm px-4 fw-semibold" style="background:#0b192c; color:#fff; border:0; font-size:0.82rem;">
                <i class="bi bi-save me-1"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
