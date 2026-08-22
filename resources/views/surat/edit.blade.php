@extends('layouts.app')
@section('title', 'Edit Surat')

@section('styles')
<style>
.section-card { border:0; box-shadow:0 1px 4px rgba(0,0,0,0.07); border-radius:10px; margin-bottom:1rem; }
.section-card .card-header { border-radius:10px 10px 0 0 !important; background:#f8fafc; border-bottom:1px solid #e9ecef; padding:0.65rem 1.1rem; font-size:0.85rem; font-weight:600; }
.form-label { font-size:0.8rem; font-weight:600; margin-bottom:0.3rem; color:#374151; }
.form-control, .form-select { font-size:0.82rem; }
.form-text { font-size:0.72rem; }
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
                    <i class="bi bi-pencil-square"></i> Edit Surat Permintaan
                </h5>
                <div style="font-size:0.75rem; color:#94a3b8; margin-top:2px;">{{ $surat->nomor_surat }}</div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('surat.show', $surat) }}" class="btn btn-sm"
                   style="background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.3); font-size:0.78rem;">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('surat.update', $surat) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="card section-card">
            <div class="card-header">
                <i class="bi bi-envelope me-2 text-primary"></i>Informasi Surat
            </div>
            <div class="card-body pt-3 pb-3">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Pemeriksaan Induk <span class="text-danger">*</span></label>
                        <select name="pemeriksaan_id" class="form-select @error('pemeriksaan_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Pemeriksaan --</option>
                            @foreach($pemeriksaans as $p)
                                <option value="{{ $p->id }}" {{ old('pemeriksaan_id', $surat->pemeriksaan_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }} (Tahun: {{ $p->tahun }})
                                </option>
                            @endforeach
                        </select>
                        @error('pemeriksaan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_surat"
                               class="form-control @error('nomor_surat') is-invalid @enderror"
                               value="{{ old('nomor_surat', $surat->nomor_surat) }}" required>
                        @error('nomor_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_surat"
                               class="form-control @error('tanggal_surat') is-invalid @enderror"
                               value="{{ old('tanggal_surat', $surat->tanggal_surat->format('Y-m-d')) }}" required>
                        @error('tanggal_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Terima <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_terima"
                               class="form-control @error('tanggal_terima') is-invalid @enderror"
                               value="{{ old('tanggal_terima', $surat->tanggal_terima->format('Y-m-d')) }}" required>
                        @error('tanggal_terima')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-7">
                        <label class="form-label">Perihal <span class="text-danger">*</span></label>
                        <input type="text" name="perihal"
                               class="form-control @error('perihal') is-invalid @enderror"
                               value="{{ old('perihal', $surat->perihal) }}" required>
                        @error('perihal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="aktif"   {{ old('status', $surat->status) === 'aktif'   ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" {{ old('status', $surat->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="arsip"   {{ old('status', $surat->status) === 'arsip'   ? 'selected' : '' }}>Arsip</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tahun Anggaran <span class="text-danger">*</span></label>
                        <input type="text" name="tahun_anggaran"
                               class="form-control"
                               value="{{ old('tahun_anggaran', $surat->tahun_anggaran) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Deadline</label>
                        <input type="date" name="deadline"
                               class="form-control @error('deadline') is-invalid @enderror"
                               value="{{ old('deadline', $surat->deadline?->format('Y-m-d')) }}">
                        @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-10">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"
                                  placeholder="Keterangan tambahan (opsional)...">{{ old('keterangan', $surat->keterangan) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Ganti File Surat <span class="text-muted fw-normal">(PDF/Word, opsional)</span></label>
                        @if($surat->file_surat)
                        <div class="mb-2 d-flex align-items-center gap-2"
                             style="font-size:0.78rem; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:6px 10px; color:#15803d;">
                            <i class="bi bi-paperclip"></i>
                            File saat ini:
                            <a href="{{ route('surat.download-file', $surat) }}" target="_blank"
                               class="text-decoration-none fw-semibold" style="color:#15803d;">
                                Lihat file <i class="bi bi-box-arrow-up-right ms-1" style="font-size:0.65rem;"></i>
                            </a>
                        </div>
                        @endif
                        <input type="file" name="file_surat"
                               class="form-control @error('file_surat') is-invalid @enderror"
                               accept=".pdf,.doc,.docx">
                        <div class="form-text">Kosongkan jika tidak ingin mengganti file. Maks. 10MB.</div>
                        @error('file_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Submit --}}
        <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('surat.show', $surat) }}" class="btn btn-sm btn-outline-secondary px-4">
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
