@extends('layouts.app')
@section('title', 'Dashboard')

@section('styles')
<style>
/* ── Typography & Base Styles ── */
body, button, input, select, textarea {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
}

:root {
    --navy-main: #0b192c;
    --navy-light: #1a365d;
    --accent-blue: #3b82f6;
    --card-border: rgba(226, 232, 240, 0.8);
}

/* ── KPI Stat Cards ── */
.kpi-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
    padding: 1.2rem 1.25rem;
    position: relative;
    overflow: hidden;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.kpi-card:hover {
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
    transform: translateY(-2px);
    border-color: #cbd5e1;
}
.kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}
.kpi-label {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.kpi-icon-wrap {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}
.kpi-value {
    font-size: 2.1rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.03em;
    color: #0f172a;
    margin-bottom: 0.6rem;
}
.kpi-sub {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

/* ── Alert Banners ── */
.alert-banner {
    border-radius: 14px;
    border: 1px solid transparent;
    padding: 0.85rem 1.15rem;
    display: flex;
    align-items: center;
    gap: 0.85rem;
    font-size: 0.8rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}

/* ── Section Cards ── */
.sec-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid var(--card-border);
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.sec-header {
    padding: 0.85rem 1.2rem;
    border-bottom: 1px solid #f1f5f9;
    font-weight: 700;
    font-size: 0.82rem;
    color: #0f172a;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
}
.sec-body {
    overflow-y: auto;
    flex: 1;
}

/* ── List Row Items ── */
.row-item {
    padding: 0.75rem 1.15rem;
    border-bottom: 1px solid #f8fafc;
    font-size: 0.8rem;
    transition: background 0.15s ease;
}
.row-item:last-child {
    border-bottom: none;
}
.row-item:hover {
    background: #f8fafc;
}

/* ── Ranking Badges ── */
.rank-num {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 800;
    flex-shrink: 0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.08);
}
.rank-gold {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #ffffff;
}
.rank-silver {
    background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
    color: #ffffff;
}
.rank-bronze {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    color: #ffffff;
}
.rank-default {
    background: #f1f5f9;
    color: #64748b;
    box-shadow: none;
}

/* ── Progress Bars ── */
.prog-bar {
    height: 6px;
    background: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
    margin-top: 6px;
}
.prog-fill {
    height: 100%;
    border-radius: 999px;
    transition: width 0.4s ease;
}

/* ── Status Pills ── */
.dl-pill {
    font-size: 0.7rem;
    padding: 3px 10px;
    border-radius: 999px;
    font-weight: 700;
    white-space: nowrap;
    flex-shrink: 0;
    letter-spacing: 0.01em;
}
</style>
@endsection

@section('content')
<div class="container-fluid py-3" style="max-width:1300px;">

    {{-- ══ HEADER BANNER ══ --}}
    <div class="card border-0 mb-3 overflow-hidden" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); border-radius:16px; position:relative; box-shadow:0 6px 20px rgba(11,25,44,0.15);">
        <div style="position:absolute;top:0;right:0;bottom:0;left:0;background:radial-gradient(circle at top right,rgba(255,255,255,0.08),transparent 60%);pointer-events:none;"></div>
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3 position-relative" style="z-index:1;">
            <div>
                <h5 class="mb-0 fw-extrabold text-white d-flex align-items-center gap-2" style="font-size:1.05rem; letter-spacing:-0.02em;">
                    <i class="bi bi-speedometer2 text-info"></i> Dashboard Monitoring BPK
                </h5>
                <div style="font-size:0.75rem;color:#94a3b8;margin-top:3px;font-weight:500;">
                    Pemantauan Pemenuhan Dokumen Pemeriksaan BPK RI &mdash; {{ now()->isoFormat('dddd, D MMMM Y') }}
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('pemeriksaan.index') }}" class="btn btn-sm fw-semibold shadow-sm" style="background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.22);font-size:0.78rem;border-radius:10px;">
                    <i class="bi bi-folder2-open me-1 text-info"></i>Pemeriksaan
                </a>
                <a href="{{ route('surat.index') }}" class="btn btn-sm fw-semibold shadow-sm" style="background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.22);font-size:0.78rem;border-radius:10px;">
                    <i class="bi bi-envelope me-1 text-warning"></i>Surat BPK
                </a>
                <a href="{{ route('opd.index') }}" class="btn btn-sm fw-semibold shadow-sm" style="background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.22);font-size:0.78rem;border-radius:10px;">
                    <i class="bi bi-building me-1 text-success"></i>Monitoring OPD
                </a>
                <a href="{{ route('laporan.index') }}" class="btn btn-sm fw-bold shadow-sm" style="background:#2563eb;color:#fff;border:0;font-size:0.78rem;border-radius:10px;">
                    <i class="bi bi-printer me-1"></i>Cetak Laporan
                </a>
            </div>
        </div>
    </div>

    {{-- ══ KPI CARDS ══ --}}
    <div class="row g-3 mb-3">

        {{-- KPI 1: Pemeriksaan --}}
        <div class="col-6 col-md-3">
            <div class="kpi-card h-100">
                <div class="kpi-header">
                    <span class="kpi-label">Pemeriksaan</span>
                    <div class="kpi-icon-wrap" style="background:#eff6ff; color:#2563eb;">
                        <i class="bi bi-folder-check"></i>
                    </div>
                </div>
                <div class="kpi-value">{{ $totalPemeriksaan }}</div>
                <div class="kpi-sub">
                    <span class="badge bg-primary-subtle text-primary fw-bold py-1 px-2" style="font-size:0.68rem;"><i class="bi bi-lightning-fill me-1"></i>{{ $pemeriksaanAktif }} Aktif</span>
                    <span class="badge bg-success-subtle text-success fw-bold py-1 px-2" style="font-size:0.68rem;"><i class="bi bi-check-circle-fill me-1"></i>{{ $pemeriksaanSelesai }} Selesai</span>
                </div>
            </div>
        </div>

        {{-- KPI 2: Surat BPK --}}
        <div class="col-6 col-md-3">
            <div class="kpi-card h-100">
                <div class="kpi-header">
                    <span class="kpi-label">Total Surat</span>
                    <div class="kpi-icon-wrap" style="background:#fffbeb; color:#d97706;">
                        <i class="bi bi-envelope-paper"></i>
                    </div>
                </div>
                <div class="kpi-value">{{ $totalSurat }}</div>
                <div class="kpi-sub">
                    <span class="badge bg-warning-subtle text-warning-emphasis fw-bold py-1 px-2" style="font-size:0.68rem;">
                        <i class="bi bi-clock-history me-1"></i>{{ $suratAktif }} Surat Aktif
                    </span>
                </div>
            </div>
        </div>

        {{-- KPI 3: Penugasan OPD --}}
        <div class="col-6 col-md-3">
            <div class="kpi-card h-100">
                <div class="kpi-header">
                    <span class="kpi-label">Penugasan OPD</span>
                    <div class="kpi-icon-wrap" style="background:#ecfdf5; color:#059669;">
                        <i class="bi bi-building-check"></i>
                    </div>
                </div>
                <div class="kpi-value">{{ $totalOpd }}</div>
                <div class="kpi-sub">
                    <span class="badge bg-success-subtle text-success fw-bold py-1 px-2" style="font-size:0.68rem;">
                        <i class="bi bi-check2-all me-1"></i>{{ $opdSelesai }} Selesai ({{ $progressPersen }}%)
                    </span>
                </div>
            </div>
        </div>

        {{-- KPI 4: Dokumen Upload --}}
        <div class="col-6 col-md-3">
            <div class="kpi-card h-100">
                <div class="kpi-header">
                    <span class="kpi-label">Dokumen Upload</span>
                    <div class="kpi-icon-wrap" style="background:#f5f3ff; color:#7c3aed;">
                        <i class="bi bi-cloud-arrow-up"></i>
                    </div>
                </div>
                <div class="kpi-value">{{ $totalDokumen }}</div>
                <div class="kpi-sub">
                    <span class="badge bg-purple-subtle text-purple fw-bold py-1 px-2" style="font-size:0.68rem; background:#f3e8ff; color:#7e22ce;">
                        <i class="bi bi-file-earmark-check me-1"></i>Terkumpul di Sistem
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- ══ ALERT BANNERS ══ --}}
    @if($suratDeadlineDekat > 0 || $suratOverdue > 0 || $opdBelum > 0)
    <div class="row g-2 mb-3">
        @if($suratDeadlineDekat > 0 || $suratOverdue > 0)
        <div class="col-12 col-md-6">
            <div class="alert-banner" style="background:#fef2f2; border-color:#fecaca;">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:#fee2e2;color:#dc2626;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size:1.1rem;"></i>
                </div>
                <div>
                    <div class="fw-bold text-danger" style="font-size:0.82rem;">Peringatan Deadline Surat</div>
                    <div style="color:#991b1b;font-size:0.75rem;">
                        <strong>{{ $suratOverdue }}</strong> surat lewat deadline &bull; <strong>{{ $suratDeadlineDekat }}</strong> mendekati batas waktu (H‑14)
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if($opdBelum > 0)
        <div class="col-12 col-md-6">
            <div class="alert-banner" style="background:#fffbeb; border-color:#fde68a;">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:#fef3c7;color:#d97706;">
                    <i class="bi bi-info-circle-fill" style="font-size:1.1rem;"></i>
                </div>
                <div>
                    <div class="fw-bold" style="color:#92400e;font-size:0.82rem;">Status Kepatuhan Penugasan</div>
                    <div style="color:#b45309;font-size:0.75rem;">
                        <strong>{{ $opdBelum }}</strong> penugasan OPD belum ditindaklanjuti sama sekali
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ══ ROW: RANKING OPD + PROGRESS ══ --}}
    <div class="row g-3 mb-3">

        {{-- Ranking OPD --}}
        <div class="col-12 col-lg-7">
            <div class="sec-card">
                <div class="sec-header">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-trophy-fill text-warning" style="font-size:0.95rem;"></i>
                        <span>Ranking Kepatuhan OPD <span class="text-muted fw-normal">(Pemeriksaan Aktif)</span></span>
                    </span>
                    <a href="{{ route('opd.index') }}" class="text-decoration-none fw-semibold" style="font-size:0.75rem;color:#2563eb;">Lihat Semua OPD &rarr;</a>
                </div>
                <div class="sec-body p-0">
                    <div class="row g-0 h-100">
                        {{-- Top 5 --}}
                        <div class="col-12 col-md-6" style="border-right:1px solid #f1f5f9;">
                            <div class="px-3 py-2 d-flex align-items-center gap-2" style="background:#f0fdf4;border-bottom:1px solid #dcfce7;">
                                <i class="bi bi-hand-thumbs-up-fill text-success" style="font-size:0.82rem;"></i>
                                <span style="font-size:0.75rem;font-weight:800;color:#166534;">Capaian Tertinggi</span>
                            </div>
                            @forelse($topOpd as $idx => $opd)
                            <div class="row-item d-flex align-items-center gap-2">
                                @if($idx == 0)
                                    <div class="rank-num rank-gold">1</div>
                                @elseif($idx == 1)
                                    <div class="rank-num rank-silver">2</div>
                                @elseif($idx == 2)
                                    <div class="rank-num rank-bronze">3</div>
                                @else
                                    <div class="rank-num rank-default">{{ $idx+1 }}</div>
                                @endif
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold text-dark text-truncate" style="font-size:0.8rem;" title="{{ $opd->opd }}">{{ $opd->opd }}</div>
                                    <div class="text-muted" style="font-size:0.68rem;">{{ $opd->selesai }}/{{ $opd->total }} selesai</div>
                                </div>
                                <span class="dl-pill" style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;">{{ $opd->persentase }}%</span>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted" style="font-size:0.78rem;">Belum ada data</div>
                            @endforelse
                        </div>

                        {{-- Bottom 5 (Perlu Perhatian) --}}
                        <div class="col-12 col-md-6">
                            <div class="px-3 py-2 d-flex align-items-center gap-2" style="background:#fef2f2;border-bottom:1px solid #fecaca;">
                                <i class="bi bi-exclamation-octagon-fill text-danger" style="font-size:0.82rem;"></i>
                                <span style="font-size:0.75rem;font-weight:800;color:#991b1b;">Perlu Perhatian</span>
                            </div>
                            @forelse($bottomOpd as $idx => $opd)
                            <div class="row-item d-flex align-items-center gap-2">
                                <div class="rank-num rank-default" style="background:#fee2e2;color:#991b1b;">{{ count($bottomOpd)-$idx }}</div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold text-dark text-truncate" style="font-size:0.8rem;" title="{{ $opd->opd }}">{{ $opd->opd }}</div>
                                    <div class="text-muted" style="font-size:0.68rem;">{{ $opd->selesai }}/{{ $opd->total }} selesai</div>
                                </div>
                                <span class="dl-pill" style="background:#fecaca;color:#991b1b;border:1px solid #fca5a5;">{{ $opd->persentase }}%</span>
                            </div>
                            @empty
                            <div class="text-center py-4 text-success" style="font-size:0.78rem;">
                                <i class="bi bi-check-circle-fill d-block mb-1 fs-5 text-success"></i>
                                Tidak ada OPD perlu perhatian<br><small class="text-muted fw-normal">(Semua OPD 100% selesai)</small>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progress Pemeriksaan Aktif --}}
        <div class="col-12 col-lg-5">
            <div class="sec-card">
                <div class="sec-header">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-bar-chart-fill text-primary" style="font-size:0.95rem;"></i>
                        <span>Progres Pemeriksaan Aktif</span>
                    </span>
                    <a href="{{ route('pemeriksaan.index') }}" class="text-decoration-none fw-semibold" style="font-size:0.75rem;color:#2563eb;">Lihat Semua &rarr;</a>
                </div>
                <div class="sec-body p-0">
                    @forelse($pemeriksaanProgress as $p)
                    <div class="row-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-semibold text-dark text-truncate pe-2" style="font-size:0.8rem;max-width:240px;" title="{{ $p->nama }}">{{ $p->nama }}</div>
                            <span class="fw-extrabold" style="font-size:0.75rem;color:{{ $p->persentase==100?'#16a34a':'#2563eb' }};flex-shrink:0;">{{ $p->persentase }}%</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size:0.68rem;">{{ $p->selesai }} dari {{ $p->total }} penugasan OPD selesai</div>
                        <div class="prog-bar">
                            <div class="prog-fill" style="width:{{ $p->persentase }}%;background:{{ $p->persentase==100?'linear-gradient(90deg, #22c55e, #16a34a)':'linear-gradient(90deg, #3b82f6, #1d4ed8)' }};"></div>
                        </div>
                    </div>
                    @empty
                    <div class="py-5 text-center text-muted" style="font-size:0.78rem;">
                        <i class="bi bi-folder-x d-block mb-2" style="font-size:2rem;opacity:0.3;"></i>
                        Tidak ada pemeriksaan aktif saat ini
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ══ ROW: AKTIVITAS + DEADLINE ══ --}}
    <div class="row g-3">

        {{-- Aktivitas Upload Terbaru --}}
        <div class="col-12 col-lg-7">
            <div class="sec-card" style="min-height:280px;">
                <div class="sec-header">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-indigo" style="color:#6366f1;font-size:0.95rem;"></i>
                        <span>Aktivitas Upload Dokumen Terbaru</span>
                    </span>
                    <a href="{{ route('dokumen.index') }}" class="text-decoration-none fw-semibold" style="font-size:0.75rem;color:#2563eb;">Lihat Dokumen &rarr;</a>
                </div>
                <div class="sec-body p-0">
                    @forelse($aktivitasTerbaru as $dok)
                    @php
                        $surat = $dok->permintaanOpd
                            ? $dok->permintaanOpd->permintaan->surat
                            : ($dok->permintaan ? $dok->permintaan->surat : null);
                        $pemeriksaan = $surat?->pemeriksaan;
                    @endphp
                    <div class="row-item d-flex gap-3 align-items-start">
                        <div class="flex-shrink-0 rounded-3 d-flex align-items-center justify-content-center shadow-sm"
                             style="width:38px;height:38px;background:#eff6ff;color:#2563eb;border:1px solid #dbeafe;">
                            <i class="bi bi-file-earmark-arrow-up-fill" style="font-size:1.05rem;"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="fw-bold text-dark text-truncate" style="font-size:0.8rem;max-width:320px;">{{ $dok->nama_file }}</div>
                                <span class="badge bg-light text-secondary border fw-normal flex-shrink-0" style="font-size:0.65rem;">{{ $dok->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-muted mt-1 text-truncate" style="font-size:0.72rem;">
                                Pengunggah: <strong class="text-dark">{{ $dok->uploader?->name ?? 'Pengguna System' }}</strong>
                                @if($pemeriksaan) &bull; <span class="text-primary">{{ Str::limit($pemeriksaan->nama, 35) }}</span> @endif
                            </div>
                            <div class="text-muted mt-1" style="font-size:0.68rem;">
                                No. Surat: <span class="font-monospace text-secondary">{{ $surat?->nomor_surat ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="py-5 text-center text-muted" style="font-size:0.78rem;">
                        <i class="bi bi-inbox d-block mb-2" style="font-size:2rem;opacity:0.3;"></i>
                        Belum ada aktivitas dokumen
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Deadline Mendekat --}}
        <div class="col-12 col-lg-5">
            <div class="sec-card" style="min-height:280px;">
                <div class="sec-header">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-event text-danger" style="font-size:0.95rem;"></i>
                        <span>Surat Menunggu Deadline</span>
                    </span>
                    <a href="{{ route('surat.index') }}" class="text-decoration-none fw-semibold" style="font-size:0.75rem;color:#2563eb;">Semua Surat &rarr;</a>
                </div>
                <div class="sec-body p-0">
                    @forelse($suratDeadline as $surat)
                    @php
                        $isOverdue = $surat->deadline < now();
                        $daysLeft  = (int) now()->diffInDays($surat->deadline, false);
                    @endphp
                    <a href="{{ route('surat.show', $surat->id) }}" class="text-decoration-none text-dark row-item d-block">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div class="fw-bold text-dark text-truncate" style="font-size:0.8rem;max-width:220px;">{{ $surat->nomor_surat }}</div>
                            @if($isOverdue)
                                <span class="dl-pill" style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;">Terlewat</span>
                            @elseif($daysLeft <= 3)
                                <span class="dl-pill" style="background:#fef9c3;color:#854d0e;border:1px solid #fef08a;">{{ $daysLeft }} hari lagi</span>
                            @else
                                <span class="dl-pill" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;">{{ $daysLeft }} hari lagi</span>
                            @endif
                        </div>
                        <div class="text-muted text-truncate mt-1" style="font-size:0.72rem;">{{ $surat->perihal }}</div>
                        <div class="d-flex align-items-center justify-content-between mt-1" style="font-size:0.68rem;">
                            <span class="text-primary"><i class="bi bi-folder me-1"></i>{{ $surat->pemeriksaan ? Str::limit($surat->pemeriksaan->nama, 24) : 'Tanpa Pemeriksaan' }}</span>
                            <span class="text-muted fw-semibold"><i class="bi bi-calendar3 me-1"></i>{{ $surat->deadline->format('d/m/Y') }}</span>
                        </div>
                    </a>
                    @empty
                    <div class="py-5 text-center text-muted" style="font-size:0.78rem;">
                        <i class="bi bi-calendar-check d-block mb-2" style="font-size:2rem;opacity:0.3;"></i>
                        Tidak ada surat dengan deadline mendesak
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
