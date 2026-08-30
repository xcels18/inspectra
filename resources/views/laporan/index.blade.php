@extends('layouts.app')
@section('title', 'Cetak Laporan')

@section('content')
<div class="container-fluid py-3" style="max-width:960px;">

    {{-- Header --}}
    <div class="card border-0 mb-3 overflow-hidden" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); border-radius:14px; position:relative;">
        <div style="position:absolute;top:0;right:0;bottom:0;left:0;background:radial-gradient(circle at top right,rgba(255,255,255,0.07),transparent 60%);pointer-events:none;"></div>
        <div class="card-body py-3 px-4 position-relative" style="z-index:1;">
            <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2" style="font-size:0.95rem;">
                <i class="bi bi-printer-fill text-info"></i> Cetak & Export Laporan Monitoring BPK
            </h5>
            <div style="font-size:0.72rem;color:#94a3b8;margin-top:2px;">Generate laporan PDF resmi formal & export data matriks kepatuhan dokumen OPD ke format Excel</div>
        </div>
    </div>

    {{-- Single Unified Form --}}
    <form id="formLaporanUtama" method="POST" action="{{ route('laporan.eksekutif.pdf') }}" target="_blank">
        @csrf
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h6 class="mb-0 fw-bold text-dark" style="font-size:0.88rem;">
                    <i class="bi bi-sliders me-2 text-primary"></i>Parameter Laporan & Pemfilteran Data
                </h6>
            </div>
            <div class="card-body p-4">

                {{-- Judul Laporan --}}
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:0.82rem;">Judul Laporan <span class="text-danger">*</span></label>
                    <input type="text" name="judul_laporan" class="form-control form-control-sm"
                           value="Laporan Monitoring Pemenuhan Dokumen Pemeriksaan BPK RI" required
                           style="font-size:0.82rem;">
                </div>

                <div class="row g-3 mb-3">
                    {{-- Pemeriksaan --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:0.82rem;">Pemeriksaan <span class="text-danger">*</span></label>
                        <select id="selectPemeriksaan" name="pemeriksaan_id" class="form-select form-select-sm" style="font-size:0.82rem;" required>
                            <option value="">— Pilih Pemeriksaan —</option>
                            @foreach($pemeriksaanList as $p)
                                <option value="{{ $p->id }}" {{ (request('pemeriksaan_id') == $p->id) ? 'selected' : '' }}>
                                    {{ $p->nama }} ({{ $p->tahun }}) — {{ ucfirst($p->status) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text" style="font-size:0.72rem;">Pilih pemeriksaan untuk memfilter surat yang tersedia.</div>
                    </div>

                    {{-- Filter Kepatuhan OPD --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:0.82rem;">Filter Kepatuhan OPD</label>
                        <select name="filter_kepatuhan" class="form-select form-select-sm" style="font-size:0.82rem;">
                            <option value="semua">Semua Entitas / OPD</option>
                            <option value="belum_lengkap">Hanya OPD Belum Lengkap (&lt; 100%)</option>
                            <option value="selesai_100">Hanya OPD Selesai Lengkap (100%)</option>
                        </select>
                        <div class="form-text" style="font-size:0.72rem;">Tampilkan seluruh OPD atau filter berdasarkan tingkat penyelesaian.</div>
                    </div>
                </div>

                {{-- Surat Dasar Perhitungan --}}
                <div class="mb-3">
                    <label class="form-label fw-bold d-flex align-items-center justify-content-between" style="font-size:0.82rem;">
                        <span>Surat Dasar Perhitungan <span class="text-danger">*</span></span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm py-0 px-2" style="font-size:0.7rem;background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;" onclick="toggleAllSurat(true)">Pilih Semua</button>
                            <button type="button" class="btn btn-sm py-0 px-2" style="font-size:0.7rem;background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;" onclick="toggleAllSurat(false)">Hapus Semua</button>
                        </div>
                    </label>
                    <div id="suratContainer" class="border rounded p-2" style="max-height:220px;overflow:auto;background:#fafbfc;">
                        @foreach($suratList as $s)
                        <div class="form-check mb-1 surat-option" data-pemeriksaan-id="{{ $s->pemeriksaan_id ?? '' }}">
                            <input class="form-check-input surat-check" type="checkbox" name="surat_ids[]"
                                   value="{{ $s->id }}" id="ps_{{ $s->id }}">
                            <label class="form-check-label" for="ps_{{ $s->id }}" style="font-size:0.8rem;">
                                <strong>{{ $s->nomor_surat }}</strong> — {{ Str::limit($s->perihal, 90) }}
                                <span class="text-muted" style="font-size:0.68rem;">
                                    @if($s->pemeriksaan)
                                        ({{ $s->pemeriksaan->nama }})
                                    @else
                                        (Tanpa Pemeriksaan)
                                    @endif
                                </span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <div id="suratEmpty" class="text-center py-3 text-muted" style="font-size:0.78rem;display:none;">
                        <i class="bi bi-inbox d-block mb-1" style="font-size:1.5rem;opacity:0.4;"></i>
                        Pilih pemeriksaan terlebih dahulu untuk melihat daftar surat.
                    </div>
                    <div class="form-text" style="font-size:0.72rem;">Centang minimal 1 nomor surat yang akan digunakan dalam laporan.</div>
                </div>

                {{-- Filter OPD --}}
                <div class="mb-3">
                    <label class="form-label fw-bold d-flex align-items-center justify-content-between" style="font-size:0.82rem;">
                        <span>Pilih Entitas / OPD <span class="text-muted fw-normal">(Opsional)</span></span>
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <button type="button" class="btn btn-sm py-0 px-2" style="font-size:0.7rem;background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;" onclick="toggleKategori('semua')">Semua Entitas</button>
                            <button type="button" class="btn btn-sm py-0 px-2" style="font-size:0.7rem;background:#f8fafc;color:#475569;border:1px solid #e2e8f0;" onclick="toggleKategori('OPD')">Semua OPD</button>
                            <button type="button" class="btn btn-sm py-0 px-2" style="font-size:0.7rem;background:#f8fafc;color:#475569;border:1px solid #e2e8f0;" onclick="toggleKategori('Instansi')">Semua Instansi</button>
                            <button type="button" class="btn btn-sm py-0 px-2" style="font-size:0.7rem;background:#f8fafc;color:#475569;border:1px solid #e2e8f0;" onclick="toggleKategori('Sekolah')">Semua Sekolah</button>
                            <button type="button" class="btn btn-sm py-0 px-2" style="font-size:0.7rem;background:#f8fafc;color:#475569;border:1px solid #e2e8f0;" onclick="toggleKategori('Vertical')">Semua Vertical</button>
                            <button type="button" class="btn btn-sm py-0 px-2" style="font-size:0.7rem;background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;" onclick="toggleKategori('none')">Hapus Pilihan</button>
                        </div>
                    </label>
                    <div class="border rounded p-2" style="max-height:220px;overflow:auto;background:#fafbfc;">
                        @foreach($masterOpds as $kategori => $opds)
                            <div class="fw-bold mb-1 mt-2 text-primary" style="font-size:0.8rem;border-bottom:1px solid #e2e8f0;padding-bottom:2px;">Kategori: {{ $kategori }}</div>
                            <div class="row g-1 mb-2">
                                @foreach($opds as $opd)
                                <div class="col-md-6">
                                    <div class="form-check m-0">
                                        <input class="form-check-input opd-checkbox" type="checkbox" name="opds[]"
                                               value="{{ $opd->nama }}" id="opd_{{ Str::slug($opd->nama) }}"
                                               data-kategori="{{ $kategori }}">
                                        <label class="form-check-label text-truncate w-100" for="opd_{{ Str::slug($opd->nama) }}" style="font-size:0.78rem;" title="{{ $opd->nama }}">
                                            {{ $opd->nama }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <div class="form-text" style="font-size:0.72rem;">Kosongkan untuk menampilkan semua entitas tanpa pemfilteran spesifik.</div>
                </div>

                {{-- Penandatangan Laporan (Opsional) --}}
                <div class="p-3 rounded mb-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-bold text-dark" style="font-size:0.82rem;">
                            <i class="bi bi-pen me-1 text-primary"></i>Penandatangan Laporan <span class="text-muted fw-normal">(Tersimpan Otomatis)</span>
                        </div>
                        <small class="text-muted" style="font-size:0.7rem;"><i class="bi bi-info-circle me-1"></i>Dapat diubah kapan saja</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label mb-1 text-muted" style="font-size:0.72rem;">Nama Penandatangan</label>
                            <input type="text" id="penandatangan_nama" name="penandatangan_nama" class="form-control form-control-sm"
                                   value="{{ old('penandatangan_nama', $penandatanganNama) }}"
                                   placeholder="Contoh: Drs. H. Ahmad, M.Si" style="font-size:0.8rem;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1 text-muted" style="font-size:0.72rem;">Jabatan Penandatangan</label>
                            <input type="text" id="penandatangan_jabatan" name="penandatangan_jabatan" class="form-control form-control-sm"
                                   value="{{ old('penandatangan_jabatan', $penandatanganJabatan) }}"
                                   placeholder="Contoh: Inspektur Kabupaten Puncak Jaya" style="font-size:0.8rem;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1 text-muted" style="font-size:0.72rem;">NIP Penandatangan</label>
                            <input type="text" id="penandatangan_nip" name="penandatangan_nip" class="form-control form-control-sm"
                                   value="{{ old('penandatangan_nip', $penandatanganNip) }}"
                                   placeholder="Contoh: 19780112 200501 1 002" style="font-size:0.8rem;">
                        </div>
                    </div>
                </div>

                {{-- Opsi Tambahan --}}
                <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #f1f5f9;">
                    <div class="fw-bold mb-2" style="font-size:0.82rem;"><i class="bi bi-sliders me-1"></i>Opsi Tambahan</div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="print_detail" name="detail" value="1" checked>
                        <label class="form-check-label fw-semibold" for="print_detail" style="font-size:0.82rem;">Tampilkan Lampiran Detail Pemenuhan Dokumen per OPD</label>
                        <div class="form-text" style="font-size:0.72rem;">Menampilkan rincian item permintaan dengan pengelompokan Selesai, Proses, dan Belum pada PDF.</div>
                    </div>
                </div>

            </div>

            {{-- Action Buttons Footer --}}
            <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <a href="{{ route('opd.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:0.82rem;">Kembali ke Monitoring</a>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" onclick="submitLaporanForm('pdf_eksekutif')" class="btn btn-sm fw-bold shadow-sm" style="background:#dc2626;color:#fff;border:0;font-size:0.82rem;">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i>Export PDF Matriks (Landscape)
                    </button>
                    <button type="button" onclick="submitLaporanForm('pdf_standar')" class="btn btn-sm fw-semibold shadow-sm btn-outline-danger" style="font-size:0.82rem;">
                        <i class="bi bi-file-earmark-text me-1"></i>Cetak PDF Formal (Potret)
                    </button>
                    <button type="button" onclick="submitLaporanForm('excel')" class="btn btn-sm fw-bold shadow-sm" style="background:#16a34a;color:#fff;border:0;font-size:0.82rem;">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i>Export Excel (.xlsx)
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
const selectPem = document.getElementById('selectPemeriksaan');
const suratOptions = document.querySelectorAll('.surat-option');
const suratContainer = document.getElementById('suratContainer');
const suratEmpty = document.getElementById('suratEmpty');

function filterSuratByPemeriksaan() {
    const pemId = selectPem.value;
    let visible = 0;
    suratOptions.forEach(el => {
        const match = !pemId || el.dataset.pemeriksaanId === pemId;
        el.style.display = match ? '' : 'none';
        if (!match) {
            el.querySelector('.surat-check').checked = false;
        }
        if (match) visible++;
    });
    suratContainer.style.display = visible > 0 ? '' : 'none';
    suratEmpty.style.display = visible === 0 ? '' : 'none';
}

function toggleAllSurat(state) {
    suratOptions.forEach(el => {
        if (el.style.display !== 'none') {
            el.querySelector('.surat-check').checked = state;
        }
    });
}

selectPem.addEventListener('change', filterSuratByPemeriksaan);

function toggleKategori(kategori) {
    const checkboxes = document.querySelectorAll('.opd-checkbox');
    if (kategori === 'none') {
        checkboxes.forEach(cb => cb.checked = false);
    } else if (kategori === 'semua') {
        checkboxes.forEach(cb => cb.checked = true);
    } else {
        checkboxes.forEach(cb => {
            if (cb.dataset.kategori === kategori) {
                cb.checked = true;
            }
        });
    }
}

function submitLaporanForm(type) {
    const form = document.getElementById('formLaporanUtama');
    if (type === 'pdf_standar') {
        form.method = 'GET';
        form.action = "{{ route('opd.print') }}";
    } else if (type === 'pdf_eksekutif') {
        form.method = 'POST';
        form.action = "{{ route('laporan.eksekutif.pdf') }}";
    } else if (type === 'excel') {
        form.method = 'POST';
        form.action = "{{ route('laporan.eksekutif.excel') }}";
    }
    form.submit();
}

// LocalStorage Auto-Save & Restore for Penandatangan
['penandatangan_nama', 'penandatangan_jabatan', 'penandatangan_nip'].forEach(fieldId => {
    const inputEl = document.getElementById(fieldId);
    if (!inputEl) return;
    
    const savedVal = localStorage.getItem('inspectra_' + fieldId);
    if (!inputEl.value && savedVal) {
        inputEl.value = savedVal;
    }
    
    inputEl.addEventListener('input', () => {
        localStorage.setItem('inspectra_' + fieldId, inputEl.value);
    });
});

if (selectPem.value) {
    filterSuratByPemeriksaan();
}
</script>
@endsection
