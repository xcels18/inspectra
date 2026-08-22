@extends('layouts.app')
@section('title', 'Dashboard')

@section('styles')
<style>
/* ── Base ── */
:root {
    --navy: #0b192c;
    --navy-2: #1a365d;
    --accent: #3b82f6;
}

/* ── Stat Cards ── */
.kpi-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #f0f4f8;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    padding: 1rem 1.25rem;
    position: relative;
    overflow: hidden;
    transition: box-shadow 0.2s, transform 0.2s;
}
.kpi-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.1); transform: translateY(-2px); }
.kpi-card .accent-bar {
    position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 12px 0 0 12px;
}
.kpi-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.15rem; flex-shrink: 0;
}
.kpi-value { font-size: 1.8rem; font-weight: 800; line-height: 1; letter-spacing: -0.03em; }
.kpi-label { font-size: 0.72rem; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-top: 2px; }
.kpi-sub { font-size: 0.68rem; color: #9ca3af; margin-top: 6px; }

/* ── Alert banners ── */
.alert-banner {
    border-radius: 10px;
    border: 0;
    border-left: 4px solid;
    padding: 0.65rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.78rem;
}

/* ── Section Cards ── */
.sec-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #f0f4f8;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.sec-header {
    padding: 0.65rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    font-weight: 700;
    font-size: 0.78rem;
    color: #1e293b;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fafbfc;
}
.sec-body { overflow-y: auto; flex: 1; }

/* ── Row items ── */
.row-item {
    padding: 0.6rem 1rem;
    border-bottom: 1px solid #f8fafc;
    font-size: 0.78rem;
    transition: background 0.1s;
}
.row-item:last-child { border-bottom: none; }
.row-item:hover { background: #f8fafc; }

/* ── Ranking ── */
.rank-num {
    width: 22px; height: 22px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.65rem; font-weight: 700; flex-shrink: 0;
}

/* ── Progress bar ── */
.prog-bar { height: 4px; background: #f1f5f9; border-radius: 99px; overflow: hidden; margin-top: 5px; }
.prog-fill { height: 100%; border-radius: 99px; }

/* ── Deadline pills ── */
.dl-pill {
    font-size: 0.65rem; padding: 2px 8px; border-radius: 999px;
    font-weight: 600; white-space: nowrap; flex-shrink: 0;
}
</style>
@endsection

@section('content')
<div class="container-fluid py-3" style="max-width:1300px;">

    {{-- ══ HEADER ══ --}}
    <div class="card border-0 mb-3 overflow-hidden" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); border-radius:14px; position:relative;">
        <div style="position:absolute;top:0;right:0;bottom:0;left:0;background:radial-gradient(circle at top right,rgba(255,255,255,0.07),transparent 60%);pointer-events:none;"></div>
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 position-relative" style="z-index:1;">
            <div>
                <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2" style="font-size:0.95rem;">
                    <i class="bi bi-speedometer2"></i> Dashboard — INSPECTRA
                </h5>
                <div style="font-size:0.72rem;color:#94a3b8;margin-top:2px;">
                    Pemantauan dokumen Pemeriksaan BPK &mdash; {{ now()->isoFormat('dddd, D MMMM Y') }}
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('pemeriksaan.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.2);font-size:0.75rem;">
                    <i class="bi bi-folder2-open me-1"></i>Pemeriksaan
                </a>
                <a href="{{ route('surat.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.2);font-size:0.75rem;">
                    <i class="bi bi-envelope me-1"></i>Surat
                </a>
                <a href="{{ route('opd.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.2);font-size:0.75rem;">
                    <i class="bi bi-building me-1"></i>OPD
                </a>
            </div>
        </div>
    </div>

    {{-- ══ KPI CARDS ══ --}}
    <div class="row g-3 mb-3">

        {{-- Pemeriksaan --}}
        <div class="col-6 col-md-3">
            <div class="kpi-card h-100">
                <div class="accent-bar" style="background:#3b82f6;"></div>
                <div class="d-flex align-items-start gap-3 ps-2">
                    <div class="kpi-icon" style="background:#eff6ff;"><i class="bi bi-folder-check text-primary"></i></div>
                    <div>
                        <div class="kpi-value" style="color:#1e40af;">{{ $totalPemeriksaan }}</div>
                        <div class="kpi-label">Pemeriksaan</div>
                    </div>
                </div>
                <div class="kpi-sub ps-2">
                    <span class="me-2"><i class="bi bi-lightning-fill text-primary" style="font-size:0.65rem;"></i> {{ $pemeriksaanAktif }} aktif</span>
                    <span><i class="bi bi-check-circle-fill text-success" style="font-size:0.65rem;"></i> {{ $pemeriksaanSelesai }} selesai</span>
                </div>
            </div>
        </div>

        {{-- Surat --}}
        <div class="col-6 col-md-3">
            <div class="kpi-card h-100">
                <div class="accent-bar" style="background:#f59e0b;"></div>
                <div class="d-flex align-items-start gap-3 ps-2">
                    <div class="kpi-icon" style="background:#fefce8;"><i class="bi bi-envelope-paper" style="color:#ca8a04;"></i></div>
                    <div>
                        <div class="kpi-value" style="color:#b45309;">{{ $totalSurat }}</div>
                        <div class="kpi-label">Total Surat</div>
                    </div>
                </div>
                <div class="kpi-sub ps-2">
                    <i class="bi bi-circle-fill text-warning" style="font-size:0.5rem;"></i> {{ $suratAktif }} surat aktif saat ini
                </div>
            </div>
        </div>

        {{-- OPD --}}
        <div class="col-6 col-md-3">
            <div class="kpi-card h-100">
                <div class="accent-bar" style="background:#22c55e;"></div>
                <div class="d-flex align-items-start gap-3 ps-2">
                    <div class="kpi-icon" style="background:#f0fdf4;"><i class="bi bi-building" style="color:#16a34a;"></i></div>
                    <div>
                        <div class="kpi-value" style="color:#15803d;">{{ $totalOpd }}</div>
                        <div class="kpi-label">Penugasan OPD</div>
                    </div>
                </div>
                <div class="kpi-sub ps-2">
                    <i class="bi bi-check2-circle text-success" style="font-size:0.65rem;"></i> {{ $opdSelesai }} penugasan selesai
                </div>
            </div>
        </div>

        {{-- Dokumen --}}
        <div class="col-6 col-md-3">
            <div class="kpi-card h-100">
                <div class="accent-bar" style="background:#8b5cf6;"></div>
                <div class="d-flex align-items-start gap-3 ps-2">
                    <div class="kpi-icon" style="background:#faf5ff;"><i class="bi bi-file-earmark-arrow-up" style="color:#7c3aed;"></i></div>
                    <div>
                        <div class="kpi-value" style="color:#6d28d9;">{{ $totalDokumen }}</div>
                        <div class="kpi-label">Dokumen Upload</div>
                    </div>
                </div>
                <div class="kpi-sub ps-2">
                    <i class="bi bi-cloud-upload" style="font-size:0.65rem; color:#8b5cf6;"></i> Terkumpul keseluruhan
                </div>
            </div>
        </div>
    </div>

    {{-- ══ ALERT BANNERS ══ --}}
    @if($suratDeadlineDekat > 0 || $suratOverdue > 0 || $opdBelum > 0)
    <div class="row g-2 mb-3">
        @if($suratDeadlineDekat > 0 || $suratOverdue > 0)
        <div class="col-12 col-md-6">
            <div class="alert-banner shadow-sm" style="background:#fef2f2;border-left-color:#ef4444;">
                <i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;font-size:1.3rem;flex-shrink:0;"></i>
                <div>
                    <div class="fw-bold" style="color:#991b1b;font-size:0.8rem;">Peringatan Deadline</div>
                    <div style="color:#b91c1c;">
                        <strong>{{ $suratOverdue }}</strong> surat lewat deadline &bull; <strong>{{ $suratDeadlineDekat }}</strong> mendekati batas waktu (H‑14)
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if($opdBelum > 0)
        <div class="col-12 col-md-6">
            <div class="alert-banner shadow-sm" style="background:#fffbeb;border-left-color:#f59e0b;">
                <i class="bi bi-info-circle-fill" style="color:#f59e0b;font-size:1.3rem;flex-shrink:0;"></i>
                <div>
                    <div class="fw-bold" style="color:#92400e;font-size:0.8rem;">Status Kepatuhan OPD</div>
                    <div style="color:#b45309;">
                        <strong>{{ $opdBelum }}</strong> penugasan OPD belum ditindaklanjuti sama sekali
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ══ ROW: RANKING + PROGRESS ══ --}}
    <div class="row g-3 mb-3">

        {{-- OPD Ranking --}}
        <div class="col-12 col-lg-7">
            <div class="sec-card">
                <div class="sec-header">
                    <span><i class="bi bi-trophy-fill me-2" style="color:#f59e0b;"></i>Ranking Kepatuhan OPD <span style="font-weight:400;color:#94a3b8;">(Pemeriksaan Aktif)</span></span>
                </div>
                <div class="sec-body p-0">
                    <div class="row g-0 h-100">
                        {{-- Top 5 --}}
                        <div class="col-12 col-md-6" style="border-right:1px solid #f1f5f9;">
                            <div class="px-3 py-2 d-flex align-items-center gap-1" style="background:#f0fdf4;border-bottom:1px solid #dcfce7;">
                                <i class="bi bi-hand-thumbs-up-fill" style="color:#16a34a;font-size:0.78rem;"></i>
                                <span style="font-size:0.72rem;font-weight:700;color:#166534;">Capaian Tertinggi</span>
                            </div>
                            @forelse($topOpd as $idx => $opd)
                            <div class="row-item d-flex align-items-center gap-2">
                                @if($idx == 0)
                                    <div class="rank-num" style="background:#fbbf24;color:#fff;">1</div>
                                @elseif($idx == 1)
                                    <div class="rank-num" style="background:#94a3b8;color:#fff;">2</div>
                                @elseif($idx == 2)
                                    <div class="rank-num" style="background:#cd7f32;color:#fff;">3</div>
                                @else
                                    <div class="rank-num" style="background:#f1f5f9;color:#64748b;">{{ $idx+1 }}</div>
                                @endif
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold text-truncate" style="font-size:0.78rem;" title="{{ $opd->opd }}">{{ $opd->opd }}</div>
                                    <div class="text-muted" style="font-size:0.65rem;">{{ $opd->selesai }}/{{ $opd->total }} selesai</div>
                                </div>
                                <span class="dl-pill" style="background:#dcfce7;color:#15803d;">{{ $opd->persentase }}%</span>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted" style="font-size:0.75rem;">Belum ada data</div>
                            @endforelse
                        </div>

                        {{-- Bottom 5 --}}
                        <div class="col-12 col-md-6">
                            <div class="px-3 py-2 d-flex align-items-center gap-1" style="background:#fef2f2;border-bottom:1px solid #fecaca;">
                                <i class="bi bi-hand-thumbs-down-fill" style="color:#dc2626;font-size:0.78rem;"></i>
                                <span style="font-size:0.72rem;font-weight:700;color:#991b1b;">Perlu Perhatian</span>
                            </div>
                            @forelse($bottomOpd as $idx => $opd)
                            <div class="row-item d-flex align-items-center gap-2">
                                <div class="rank-num" style="background:#f1f5f9;color:#64748b;">{{ count($bottomOpd)-$idx }}</div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold text-truncate" style="font-size:0.78rem;" title="{{ $opd->opd }}">{{ $opd->opd }}</div>
                                    <div class="text-muted" style="font-size:0.65rem;">{{ $opd->selesai }}/{{ $opd->total }} selesai</div>
                                </div>
                                <span class="dl-pill" style="background:#fecaca;color:#991b1b;">{{ $opd->persentase }}%</span>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted" style="font-size:0.75rem;">Belum ada data</div>
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
                    <span><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Progres Pemeriksaan Aktif</span>
                    <a href="{{ route('pemeriksaan.index') }}" class="text-decoration-none" style="font-size:0.72rem;color:#3b82f6;">Lihat Semua</a>
                </div>
                <div class="sec-body p-0">
                    @forelse($pemeriksaanProgress as $p)
                    <div class="row-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-semibold text-truncate pe-2" style="font-size:0.78rem;max-width:220px;" title="{{ $p->nama }}">{{ $p->nama }}</div>
                            <span style="font-size:0.68rem;font-weight:700;color:{{ $p->persentase==100?'#16a34a':'#1d4ed8' }};flex-shrink:0;">{{ $p->persentase }}%</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size:0.65rem;">{{ $p->selesai }} dari {{ $p->total }} penugasan OPD selesai</div>
                        <div class="prog-bar">
                            <div class="prog-fill" style="width:{{ $p->persentase }}%;background:{{ $p->persentase==100?'#22c55e':'#3b82f6' }};"></div>
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
                    <span><i class="bi bi-clock-history me-2" style="color:#6366f1;"></i>Aktivitas Upload Terbaru</span>
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
                        <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                             style="width:34px;height:34px;background:#eff6ff;">
                            <i class="bi bi-file-earmark-text text-primary" style="font-size:0.9rem;"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="fw-semibold text-truncate" style="font-size:0.78rem;max-width:320px;">{{ $dok->nama_file }}</div>
                                <span class="text-muted flex-shrink-0" style="font-size:0.62rem;">{{ $dok->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-muted mt-1 text-truncate" style="font-size:0.68rem;">
                                Oleh: <strong>{{ $dok->uploader?->name ?? 'Unknown' }}</strong>
                                @if($pemeriksaan) &bull; {{ Str::limit($pemeriksaan->nama, 30) }} @endif
                            </div>
                            <div class="text-muted mt-1" style="font-size:0.65rem;">
                                Surat: {{ $surat?->nomor_surat ?? '-' }}
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
                    <span><i class="bi bi-calendar-event me-2" style="color:#ef4444;"></i>Surat Menunggu Deadline</span>
                    <a href="{{ route('surat.index') }}" class="text-decoration-none" style="font-size:0.72rem;color:#3b82f6;">Semua Surat</a>
                </div>
                <div class="sec-body p-0">
                    @forelse($suratDeadline as $surat)
                    @php
                        $isOverdue = $surat->deadline < now();
                        $daysLeft  = (int) now()->diffInDays($surat->deadline, false);
                    @endphp
                    <a href="{{ route('surat.show', $surat->id) }}" class="text-decoration-none text-dark row-item d-block">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div class="fw-semibold text-truncate" style="font-size:0.78rem;max-width:200px;">{{ $surat->nomor_surat }}</div>
                            @if($isOverdue)
                                <span class="dl-pill" style="background:#fee2e2;color:#dc2626;">Terlewat</span>
                            @elseif($daysLeft <= 3)
                                <span class="dl-pill" style="background:#fef9c3;color:#854d0e;">{{ $daysLeft }}h lagi</span>
                            @else
                                <span class="dl-pill" style="background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;">{{ $daysLeft }}h lagi</span>
                            @endif
                        </div>
                        <div class="text-muted text-truncate mt-1" style="font-size:0.7rem;">{{ $surat->perihal }}</div>
                        <div class="d-flex align-items-center justify-content-between mt-1" style="font-size:0.65rem;">
                            <span style="color:#3b82f6;"><i class="bi bi-folder me-1"></i>{{ $surat->pemeriksaan ? Str::limit($surat->pemeriksaan->nama, 22) : 'Tanpa Pemeriksaan' }}</span>
                            <span class="text-muted"><i class="bi bi-calendar me-1"></i>{{ $surat->deadline->format('d/m/Y') }}</span>
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
