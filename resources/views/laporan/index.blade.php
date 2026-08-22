@extends('layouts.app')
@section('title', 'Cetak Laporan')

@section('content')
<div class="container-fluid py-3" style="max-width:900px;">

    {{-- Header --}}
    <div class="card border-0 mb-3 overflow-hidden" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); border-radius:14px; position:relative;">
        <div style="position:absolute;top:0;right:0;bottom:0;left:0;background:radial-gradient(circle at top right,rgba(255,255,255,0.07),transparent 60%);pointer-events:none;"></div>
        <div class="card-body py-3 px-4 position-relative" style="z-index:1;">
            <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2" style="font-size:0.95rem;">
                <i class="bi bi-file-earmark-pdf"></i> Cetak Laporan Monitoring OPD
            </h5>
            <div style="font-size:0.72rem;color:#94a3b8;margin-top:2px;">Generate laporan PDF kepatuhan dokumen OPD berdasarkan Pemeriksaan dan Surat</div>
        </div>
    </div>

    {{-- Form --}}
    <form method="GET" action="{{ route('opd.print') }}" target="_blank">
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body p-4">

                {{-- Judul Laporan --}}
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:0.82rem;">Judul Laporan <span class="text-danger">*</span></label>
                    <input type="text" name="judul_laporan" class="form-control form-control-sm"
                           placeholder="Contoh: Laporan Monitoring OPD Triwulan I Tahun 2026" required
                           style="font-size:0.82rem;">
                </div>

                {{-- Pemeriksaan --}}
                <div class="mb-3">
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

                {{-- Surat Dasar Perhitungan --}}
                <div class="mb-3">
                    <label class="form-label fw-bold d-flex align-items-center justify-content-between" style="font-size:0.82rem;">
                        <span>Surat Dasar Perhitungan <span class="text-danger">*</span></span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm py-0 px-2" style="font-size:0.7rem;background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;" onclick="toggleAllSurat(true)">Pilih Semua</button>
                            <button type="button" class="btn btn-sm py-0 px-2" style="font-size:0.7rem;background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;" onclick="toggleAllSurat(false)">Hapus Semua</button>
                        </div>
                    </label>
                    <div id="suratContainer" class="border rounded p-2" style="max-height:250px;overflow:auto;background:#fafbfc;">
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
                    <div class="border rounded p-2" style="max-height:250px;overflow:auto;background:#fafbfc;">
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

                {{-- Opsi Tambahan --}}
                <div class="p-3 rounded mb-3" style="background:#f8fafc;border:1px solid #f1f5f9;">
                    <div class="fw-bold mb-2" style="font-size:0.82rem;"><i class="bi bi-sliders me-1"></i>Opsi Tambahan</div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="print_detail" name="detail" value="1">
                        <label class="form-check-label fw-semibold" for="print_detail" style="font-size:0.82rem;">Tampilkan Detail Permintaan per Status</label>
                        <div class="form-text" style="font-size:0.72rem;">Jika aktif, PDF menampilkan rincian item permintaan dengan pengelompokan Selesai, Proses, dan Belum per OPD.</div>
                    </div>
                </div>

            </div>
            <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-end gap-2">
                <a href="{{ route('opd.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:0.82rem;">Kembali ke Monitoring</a>
                <button type="submit" class="btn btn-sm fw-semibold" style="background:#dc2626;color:#fff;border:0;font-size:0.82rem;">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Generate PDF
                </button>
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

// Auto-filter on page load if pemeriksaan pre-selected
if (selectPem.value) {
    filterSuratByPemeriksaan();
}
</script>
@endsection
