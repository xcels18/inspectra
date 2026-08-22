@extends('layouts.app')
@section('title', 'Tambah Surat')

@php $daftarOpd = App\Models\PermintaanData::opsiOpd(); @endphp

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
.ts-wrapper.multi .ts-control { min-height:30px; font-size:0.8rem; }
.ts-dropdown { font-size:0.8rem; }
.section-card { border:0; box-shadow:0 1px 4px rgba(0,0,0,0.07); border-radius:10px; margin-bottom:1rem; }
.section-card .card-header { border-radius:10px 10px 0 0 !important; background:#f8fafc; border-bottom:1px solid #e9ecef; padding:0.65rem 1.1rem; font-size:0.85rem; font-weight:600; }
.judul-item { background:#fafbfc; border:1px solid #e9ecef !important; border-radius:8px; }
.list-data-row { background:#fff; border:1px solid #f0f0f0 !important; border-radius:6px; }
.form-label { font-size:0.8rem; font-weight:600; margin-bottom:0.3rem; color:#374151; }
.form-control, .form-select { font-size:0.82rem; }
.form-text { font-size:0.72rem; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3" style="max-width:960px;">

    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4" style="background:linear-gradient(135deg,#1e40af 0%,#1e3a8a 100%); border-radius:10px;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-envelope-plus me-2"></i>Tambah Surat Permintaan</h5>
                <div style="font-size:0.75rem; color:rgba(255,255,255,0.65); margin-top:2px;">Isi informasi surat dan daftar permintaan data</div>
            </div>
            <a href="{{ route('surat.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.3); font-size:0.78rem;">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('surat.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="import_payload" id="import_payload" value="">

        {{-- Seksi 1: Informasi Surat --}}
        <div class="card section-card">
            <div class="card-header">
                <i class="bi bi-envelope me-2 text-primary"></i>Informasi Surat
            </div>
            <div class="card-body pt-3 pb-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_surat"
                               class="form-control @error('nomor_surat') is-invalid @enderror"
                               value="{{ old('nomor_surat') }}"
                               placeholder="Contoh: 06/Terinci/LKPD/04/2026" required>
                        @error('nomor_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_surat"
                               class="form-control @error('tanggal_surat') is-invalid @enderror"
                               value="{{ old('tanggal_surat') }}" required>
                        @error('tanggal_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Terima <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_terima"
                               class="form-control @error('tanggal_terima') is-invalid @enderror"
                               value="{{ old('tanggal_terima', date('Y-m-d')) }}" required>
                        @error('tanggal_terima')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Perihal <span class="text-danger">*</span></label>
                        <input type="text" name="perihal"
                               class="form-control @error('perihal') is-invalid @enderror"
                               value="{{ old('perihal') }}"
                               placeholder="Perihal surat..." required>
                        @error('perihal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tahun Anggaran <span class="text-danger">*</span></label>
                        <input type="text" name="tahun_anggaran"
                               class="form-control @error('tahun_anggaran') is-invalid @enderror"
                               value="{{ old('tahun_anggaran', date('Y')) }}" required>
                        @error('tahun_anggaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Deadline</label>
                        <input type="date" name="deadline"
                               class="form-control @error('deadline') is-invalid @enderror"
                               value="{{ old('deadline') }}">
                        @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"
                                  placeholder="Keterangan tambahan (opsional)...">{{ old('keterangan') }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">File Surat <span class="text-muted fw-normal">(PDF/Word)</span></label>
                        <input type="file" name="file_surat"
                               class="form-control @error('file_surat') is-invalid @enderror"
                               accept=".pdf,.doc,.docx">
                        <div class="form-text">Maks. 10MB</div>
                        @error('file_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Seksi 2: Daftar Permintaan Data --}}
        <div class="card section-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-list-check me-2 text-success"></i>Daftar Permintaan Data <span class="text-danger">*</span></span>
                <div class="d-flex gap-2">
                    <a href="{{ route('surat.template-excel') }}" class="btn btn-sm"
                       style="background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd; font-size:0.76rem;">
                        <i class="bi bi-download me-1"></i>Unduh Template
                    </a>
                    <button type="button" class="btn btn-sm"
                            style="background:#fefce8; color:#854d0e; border:1px solid #fde68a; font-size:0.76rem;"
                            data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                        <i class="bi bi-file-earmark-excel me-1"></i>Upload Excel
                    </button>
                    <button type="button" class="btn btn-sm"
                            style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; font-size:0.76rem;"
                            onclick="tambahJudul()">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Judul
                    </button>
                </div>
            </div>
            <div class="card-body pt-3 pb-3">
                <div id="import-summary" class="d-none alert alert-info py-2 px-3 mb-3" style="font-size:0.8rem;"></div>
                <div id="judul-container">

                    <div class="judul-item p-3 mb-3" data-judul-index="0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold" style="font-size:0.82rem; color:#1d4ed8;">
                                <i class="bi bi-folder me-1"></i>Judul Permintaan #1
                            </span>
                            <button type="button" class="btn btn-sm hapus-judul-btn"
                                    style="background:#fff5f5; color:#dc2626; border:1px solid #fecaca; font-size:0.72rem;"
                                    onclick="hapusJudul(this)" style="display:none;">
                                <i class="bi bi-trash me-1"></i>Hapus Judul
                            </button>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="judul_permintaan[0][judul]"
                                   class="form-control"
                                   placeholder="Judul permintaan data..." required>
                        </div>
                        <div class="list-data-container ms-2" data-judul="0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0" style="color:#6b7280;">
                                    <i class="bi bi-card-list me-1"></i>List Data yang Diminta
                                </label>
                                <button type="button" class="btn btn-sm"
                                        style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.72rem;"
                                        onclick="tambahListData(this, 0)">
                                    <i class="bi bi-plus me-1"></i>Tambah Data
                                </button>
                            </div>
                            <div class="list-data-items">
                                <div class="list-data-row mb-2 p-2">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="text-muted fw-semibold" style="min-width:18px; font-size:0.78rem;">1.</span>
                                        <input type="text" name="judul_permintaan[0][list_data][]"
                                               class="form-control form-control-sm"
                                               placeholder="Data yang diminta...">
                                        <button type="button" class="btn btn-sm hapus-list-btn"
                                                style="background:#fff5f5; color:#dc2626; border:1px solid #fecaca; font-size:0.72rem; padding:2px 7px; display:none;"
                                                onclick="hapusListData(this)">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                    <div class="ps-4">
                                        <select name="judul_permintaan[0][list_opd][0][]"
                                                class="form-select form-select-sm opd-select" multiple>
                                            @foreach($daftarOpd as $opd)
                                            <option value="{{ $opd }}">{{ $opd }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Tombol Submit --}}
        <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('surat.index') }}" class="btn btn-sm btn-outline-secondary px-4">
                Batal
            </a>
            <button type="submit" class="btn btn-sm px-4 fw-semibold"
                    style="background:#1e40af; color:#fff; border:0; font-size:0.82rem;">
                <i class="bi bi-save me-1"></i>Simpan Surat
            </button>
        </div>

    </form>
</div>

{{-- Modal Import Excel --}}
<div class="modal fade" id="modalImportExcel" tabindex="-1" aria-labelledby="modalImportExcelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:10px;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e40af 0%,#1e3a8a 100%); border-radius:10px 10px 0 0;">
                <h6 class="modal-title text-white fw-bold mb-0">
                    <i class="bi bi-file-earmark-excel me-2"></i>Import dari Excel
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:0.8rem; color:#6b7280;">Upload file Excel sesuai format template. Data akan otomatis mengisi daftar permintaan di bawah.</p>
                <div class="mb-3">
                    <label class="form-label" style="font-size:0.8rem;">File Excel <span class="text-danger">*</span></label>
                    <input type="file" id="inputFileExcel" class="form-control form-control-sm" accept=".xlsx,.xls">
                    <div class="form-text">Format: .xlsx atau .xls, maks. 5MB</div>
                </div>
                <div id="importAlert" class="d-none alert alert-sm py-2" style="font-size:0.8rem;"></div>
            </div>
            <div class="modal-footer" style="gap:0.5rem;">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm fw-semibold" id="btnProseImport"
                        style="background:#1e40af; color:#fff; border:0; font-size:0.82rem;"
                        onclick="prosesImportExcel()">
                    <i class="bi bi-upload me-1"></i>Proses Import
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
const tsOpts = { plugins: ['remove_button'], maxOptions: null, placeholder: 'Pilih OPD...' };

function initTomSelect(el) {
    if (!el.tomselect) new TomSelect(el, {...tsOpts});
}

document.querySelectorAll('.opd-select').forEach(initTomSelect);

const opdOptions = `@foreach($daftarOpd as $opd)<option value="{{ $opd }}">{{ $opd }}</option>@endforeach`;

const btnStyle = {
    hapusJudul: 'background:#fff5f5; color:#dc2626; border:1px solid #fecaca; font-size:0.72rem;',
    tambahData: 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.72rem;',
    hapusList:  'background:#fff5f5; color:#dc2626; border:1px solid #fecaca; font-size:0.72rem; padding:2px 7px;',
};

let judulIdx = 1;
let importedPayload = null;

function tambahJudul() {
    const container = document.getElementById('judul-container');
    const div = document.createElement('div');
    div.className = 'judul-item p-3 mb-3';
    div.setAttribute('data-judul-index', judulIdx);
    div.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-semibold" style="font-size:0.82rem; color:#1d4ed8;">
                <i class="bi bi-folder me-1"></i>Judul Permintaan #${judulIdx + 1}
            </span>
            <button type="button" class="btn btn-sm hapus-judul-btn" style="${btnStyle.hapusJudul}" onclick="hapusJudul(this)">
                <i class="bi bi-trash me-1"></i>Hapus Judul
            </button>
        </div>
        <div class="mb-3">
            <input type="text" name="judul_permintaan[${judulIdx}][judul]" class="form-control" placeholder="Judul permintaan data..." required>
        </div>
        <div class="list-data-container ms-2" data-judul="${judulIdx}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label mb-0" style="color:#6b7280;"><i class="bi bi-card-list me-1"></i>List Data yang Diminta</label>
                <button type="button" class="btn btn-sm" style="${btnStyle.tambahData}" onclick="tambahListData(this, ${judulIdx})">
                    <i class="bi bi-plus me-1"></i>Tambah Data
                </button>
            </div>
            <div class="list-data-items">
                <div class="list-data-row mb-2 p-2">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="text-muted fw-semibold" style="min-width:18px; font-size:0.78rem;">1.</span>
                        <input type="text" name="judul_permintaan[${judulIdx}][list_data][]" class="form-control form-control-sm" placeholder="Data yang diminta...">
                        <button type="button" class="btn btn-sm hapus-list-btn" style="${btnStyle.hapusList} display:none;" onclick="hapusListData(this)"><i class="bi bi-x"></i></button>
                    </div>
                    <div class="ps-4">
                        <select name="judul_permintaan[${judulIdx}][list_opd][0][]" class="form-select form-select-sm opd-select" multiple>${opdOptions}</select>
                    </div>
                </div>
            </div>
        </div>`;
    container.appendChild(div);
    div.querySelectorAll('.opd-select').forEach(initTomSelect);
    updateJudulNumbers();
    judulIdx++;
}

function hapusJudul(btn) {
    if (document.querySelectorAll('.judul-item').length <= 1) return;
    btn.closest('.judul-item').remove();
    updateJudulNumbers();
}

function updateJudulNumbers() {
    const items = document.querySelectorAll('.judul-item');
    items.forEach(function(item, i) {
        item.querySelector('.fw-semibold').innerHTML = `<i class="bi bi-folder me-1"></i>Judul Permintaan #${i + 1}`;
        item.querySelector('.hapus-judul-btn').style.display = items.length > 1 ? 'inline-flex' : 'none';
    });
}

function tambahListData(btn, judulIndex) {
    const container = btn.closest('.list-data-container').querySelector('.list-data-items');
    const items = container.querySelectorAll('.list-data-row');
    const newNum = items.length + 1;
    const div = document.createElement('div');
    div.className = 'list-data-row mb-2 p-2';
    div.innerHTML = `
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="text-muted fw-semibold" style="min-width:18px; font-size:0.78rem;">${newNum}.</span>
            <input type="text" name="judul_permintaan[${judulIndex}][list_data][]" class="form-control form-control-sm" placeholder="Data yang diminta...">
            <button type="button" class="btn btn-sm hapus-list-btn" style="${btnStyle.hapusList}" onclick="hapusListData(this)"><i class="bi bi-x"></i></button>
        </div>
        <div class="ps-4">
            <select name="judul_permintaan[${judulIndex}][list_opd][${newNum - 1}][]" class="form-select form-select-sm opd-select" multiple>${opdOptions}</select>
        </div>`;
    container.appendChild(div);
    div.querySelectorAll('.opd-select').forEach(initTomSelect);
    updateListNumbers(container);
}

function hapusListData(btn) {
    const container = btn.closest('.list-data-items');
    if (container.querySelectorAll('.list-data-row').length <= 1) return;
    btn.closest('.list-data-row').remove();
    updateListNumbers(container);
}

function updateListNumbers(container) {
    const items = container.querySelectorAll('.list-data-row');
    items.forEach(function(item, i) {
        item.querySelector('.text-muted.fw-semibold').textContent = `${i + 1}.`;
        item.querySelector('.hapus-list-btn').style.display = items.length > 1 ? 'inline-flex' : 'none';
    });
}

function clearImportPayload() {
    importedPayload = null;
    const payloadEl = document.getElementById('import_payload');
    if (payloadEl) payloadEl.value = '';

    const summaryEl = document.getElementById('import-summary');
    if (summaryEl) {
        summaryEl.className = 'd-none alert alert-info py-2 px-3 mb-3';
        summaryEl.textContent = '';
    }
}

function setImportPayload(data) {
    importedPayload = data;
    const payloadEl = document.getElementById('import_payload');
    if (payloadEl) {
        payloadEl.value = JSON.stringify(data);
    }

    const judulCount = Array.isArray(data) ? data.length : 0;
    const itemCount = Array.isArray(data)
        ? data.reduce((sum, g) => sum + ((g.items && Array.isArray(g.items)) ? g.items.length : 0), 0)
        : 0;

    const summaryEl = document.getElementById('import-summary');
    if (summaryEl) {
        summaryEl.className = 'alert alert-info py-2 px-3 mb-3';
        summaryEl.innerHTML = `<i class="bi bi-info-circle me-1"></i>Data import aktif: <strong>${judulCount}</strong> judul, <strong>${itemCount}</strong> item. Data akan diproses langsung saat simpan.`;
    }
}

function prosesImportExcel() {
    const fileInput = document.getElementById('inputFileExcel');
    const alertEl = document.getElementById('importAlert');
    alertEl.className = 'd-none alert alert-sm py-2';
    alertEl.textContent = '';

    if (!fileInput.files || !fileInput.files[0]) {
        alertEl.className = 'alert alert-danger py-2';
        alertEl.style.fontSize = '0.8rem';
        alertEl.textContent = 'Pilih file Excel terlebih dahulu.';
        return;
    }

    const formData = new FormData();
    formData.append('file_excel', fileInput.files[0]);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    const btn = document.getElementById('btnProseImport');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';

    fetch('{{ route("surat.import-excel") }}', {
        method: 'POST',
        body: formData,
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-upload me-1"></i>Proses Import';

        if (!res.success) {
            alertEl.className = 'alert alert-danger py-2';
            alertEl.style.fontSize = '0.8rem';
            alertEl.textContent = res.message;
            return;
        }

        const container = document.getElementById('judul-container');
        container.innerHTML = '';
        judulIdx = 0;

        setImportPayload(res.data);

        const info = document.createElement('div');
        info.className = 'text-muted';
        info.style.fontSize = '0.8rem';
        info.innerHTML = '<i class="bi bi-check-circle text-success me-1"></i>Data detail disimpan sebagai payload import untuk mencegah limit input PHP.';
        container.appendChild(info);

        bootstrap.Modal.getInstance(document.getElementById('modalImportExcel')).hide();
        fileInput.value = '';
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-upload me-1"></i>Proses Import';
        alertEl.className = 'alert alert-danger py-2';
        alertEl.style.fontSize = '0.8rem';
        alertEl.textContent = 'Terjadi kesalahan jaringan. Coba lagi.';
    });
}

document.querySelector('form[action="{{ route('surat.store') }}"]').addEventListener('submit', function() {
    if (importedPayload) {
        document.querySelectorAll('#judul-container input, #judul-container select, #judul-container textarea').forEach(function(el) {
            el.disabled = true;
        });
    }
});

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endsection
