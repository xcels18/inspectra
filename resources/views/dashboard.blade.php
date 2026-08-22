@extends('layouts.app')
@section('title', 'Dashboard')

@section('styles')
<style>
.stat-card { background:#fff; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,0.07); border:1px solid #f0f0f0; padding:1rem 1.25rem; }
.stat-icon { width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.stat-value { font-size:1.6rem; font-weight:700; line-height:1; color:#1e3a8a; }
.stat-label { font-size:0.75rem; color:#6b7280; margin-top:2px; }
.stat-sub { font-size:0.7rem; color:#9ca3af; margin-top:5px; }
.sec-card { background:#fff; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,0.07); border:1px solid #f0f0f0; overflow:hidden; display:flex; flex-direction:column; }
.sec-header { padding:0.75rem 1rem; border-bottom:1px solid #f3f4f6; font-weight:600; font-size:0.82rem; color:#374151; display:flex; justify-content:space-between; align-items:center; }
.sec-body { overflow-y:auto; flex:1; }
.row-item { padding:0.6rem 1rem; border-bottom:1px solid #f9fafb; font-size:0.8rem; }
.row-item:last-child { border-bottom:none; }
.row-item:hover { background:#fafafa; }
.deadline-pill { font-size:0.68rem; padding:2px 8px; border-radius:999px; font-weight:500; white-space:nowrap; }
.bar-wrap { height:4px; background:#f3f4f6; border-radius:99px; overflow:hidden; margin-top:4px; }
.bar-fill { height:100%; border-radius:99px; transition:width 0.4s; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3" style="max-width:1300px;">

    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4" style="background:linear-gradient(135deg,#1e40af 0%,#1e3a8a 100%); border-radius:10px;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h5>
                <div style="font-size:0.75rem; color:rgba(255,255,255,0.65); margin-top:2px;">Rekap pengumpulan data BPK — {{ now()->format('d F Y') }}</div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('surat.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.3); font-size:0.78rem;">
                    <i class="bi bi-envelope me-1"></i>Surat
                </a>
                <a href="{{ route('opd.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.3); font-size:0.78rem;">
                    <i class="bi bi-building me-1"></i>OPD
                </a>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon" style="background:#eff6ff;">
                        <i class="bi bi-envelope-paper text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $totalSurat }}</div>
                        <div class="stat-label">Total Surat</div>
                    </div>
                </div>
                <div class="stat-sub">{{ $suratAktif }} aktif &bull; {{ $suratSelesai }} selesai</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon" style="background:#fef9c3;">
                        <i class="bi bi-hourglass-split" style="color:#ca8a04;"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="color:#ca8a04;">{{ $opdBelum }}</div>
                        <div class="stat-label">OPD Belum Kirim</div>
                    </div>
                </div>
                <div class="stat-sub">{{ $opdProses }} sedang diproses</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon" style="background:#f0fdf4;">
                        <i class="bi bi-check2-circle" style="color:#16a34a;"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="color:#16a34a;">{{ $opdSelesai }}</div>
                        <div class="stat-label">OPD Selesai</div>
                    </div>
                </div>
                <div class="stat-sub">dari {{ $totalOpd }} total penugasan</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon" style="background:#fdf4ff;">
                        <i class="bi bi-file-earmark-arrow-up" style="color:#9333ea;"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="color:#9333ea;">{{ $totalDokumen }}</div>
                        <div class="stat-label">Dokumen Upload</div>
                    </div>
                </div>
                <div class="stat-sub">
                    @if($suratOverdue > 0)
                    <span class="text-danger"><i class="bi bi-exclamation-circle"></i> {{ $suratOverdue }} surat overdue</span>
                    @elseif($suratDeadlineDekat > 0)
                    <span class="text-warning"><i class="bi bi-clock"></i> {{ $suratDeadlineDekat }} deadline dekat</span>
                    @else
                    <span class="text-success"><i class="bi bi-shield-check"></i> Semua deadline aman</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Progress Global --}}
    <div class="stat-card mb-3">
        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
            <div>
                <span class="fw-semibold" style="font-size:0.85rem; color:#1e3a8a;">Progress Keseluruhan Pengumpulan Data</span>
                <span class="text-muted ms-2" style="font-size:0.75rem;">{{ $opdSelesai + $opdProses }} dari {{ $totalOpd }} penugasan OPD berjalan</span>
            </div>
            <span class="fw-bold" style="font-size:1.1rem; color:{{ $progressPersen >= 75 ? '#16a34a' : ($progressPersen >= 40 ? '#d97706' : '#dc2626') }};">
                {{ $progressPersen }}%
            </span>
        </div>
        <div style="height:8px; background:#e9ecef; border-radius:99px; overflow:hidden;">
            <div style="height:100%; width:{{ $progressPersen }}%; border-radius:99px; transition:width 0.6s;
                background:{{ $progressPersen >= 75 ? '#22c55e' : ($progressPersen >= 40 ? '#f59e0b' : '#ef4444') }};"></div>
        </div>
        <div class="d-flex gap-3 mt-2" style="font-size:0.72rem;">
            <span style="color:#dc2626;"><span class="fw-semibold">{{ $opdBelum }}</span> belum</span>
            <span style="color:#ca8a04;"><span class="fw-semibold">{{ $opdProses }}</span> proses</span>
            <span style="color:#16a34a;"><span class="fw-semibold">{{ $opdSelesai }}</span> selesai</span>
        </div>
    </div>

    {{-- Content Row --}}
    <div class="row g-3 mb-3" style="align-items:stretch;">

        {{-- Surat Terbaru --}}
        <div class="col-lg-5">
            <div class="sec-card h-100">
                <div class="sec-header">
                    <span><i class="bi bi-envelope-paper me-2 text-primary"></i>Surat Terbaru</span>
                    <a href="{{ route('surat.index') }}" class="btn btn-sm py-0 px-2"
                       style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.72rem;">
                        Lihat Semua
                    </a>
                </div>
                <div class="sec-body">
                    @forelse($suratTerbaru as $surat)
                    <div class="row-item d-flex justify-content-between align-items-start gap-2">
                        <div style="min-width:0; flex:1;">
                            <a href="{{ route('surat.show', $surat) }}" class="fw-semibold text-decoration-none text-dark" style="font-size:0.8rem;">
                                {{ $surat->nomor_surat }}
                            </a>
                            <div class="text-muted text-truncate" style="font-size:0.7rem; max-width:240px;">{{ $surat->perihal }}</div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="badge bg-{{ $surat->status_badge }}" style="font-size:0.62rem;">{{ $surat->status_label }}</span>
                            <div class="text-muted mt-1" style="font-size:0.65rem;">{{ $surat->tanggal_terima->format('d/m/Y') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4" style="font-size:0.8rem;">Belum ada surat</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Deadline Surat --}}
        <div class="col-lg-4">
            <div class="sec-card h-100">
                <div class="sec-header">
                    <span><i class="bi bi-calendar-event me-2 text-danger"></i>Deadline Surat</span>
                </div>
                <div class="sec-body">
                    @forelse($suratDeadline as $surat)
                    @php
                        $daysLeft  = now()->startOfDay()->diffInDays($surat->deadline->startOfDay(), false);
                        $isOverdue = $daysLeft < 0;
                        $isNear    = $daysLeft <= 7;
                        $pillStyle = $isOverdue
                            ? 'background:#fee2e2; color:#dc2626;'
                            : ($isNear ? 'background:#fef9c3; color:#ca8a04;' : 'background:#f3f4f6; color:#6b7280;');
                        $label = $isOverdue
                            ? 'Lewat ' . abs($daysLeft) . 'h'
                            : ($daysLeft == 0 ? 'Hari Ini' : $daysLeft . ' hari lagi');
                    @endphp
                    <div class="row-item d-flex justify-content-between align-items-center gap-2">
                        <div style="min-width:0;">
                            <a href="{{ route('surat.show', $surat) }}" class="text-decoration-none text-dark fw-semibold" style="font-size:0.8rem;">
                                {{ $surat->nomor_surat }}
                            </a>
                            <div class="text-muted" style="font-size:0.7rem;">{{ $surat->deadline->format('d M Y') }}</div>
                        </div>
                        <span class="deadline-pill flex-shrink-0" style="{{ $pillStyle }}">{{ $label }}</span>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4" style="font-size:0.8rem;">Tidak ada deadline aktif</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Progress OPD --}}
        <div class="col-lg-3">
            <div class="sec-card h-100">
                <div class="sec-header">
                    <span><i class="bi bi-building me-2 text-primary"></i>Progress OPD</span>
                    <a href="{{ route('opd.index') }}" class="btn btn-sm py-0 px-2"
                       style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.72rem;">
                        Semua
                    </a>
                </div>
                <div class="sec-body">
                    @forelse($opdProgress as $opd)
                    @php
                        $pct       = $opd->total > 0 ? round(($opd->selesai / $opd->total) * 100) : 0;
                        $barColor  = $pct >= 75 ? '#22c55e' : ($pct >= 40 ? '#f59e0b' : '#ef4444');
                    @endphp
                    <div class="row-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-truncate" style="font-size:0.72rem; color:#374151; max-width:140px;" title="{{ $opd->opd }}">
                                {{ $opd->opd }}
                            </span>
                            <span class="fw-semibold ms-1 flex-shrink-0" style="font-size:0.68rem; color:{{ $barColor }};">{{ $pct }}%</span>
                        </div>
                        <div class="bar-wrap">
                            <div class="bar-fill" style="width:{{ $pct }}%; background:{{ $barColor }};"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4" style="font-size:0.8rem;">Belum ada data OPD</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Aktivitas Terbaru --}}
    <div class="sec-card">
        <div class="sec-header">
            <span><i class="bi bi-clock-history me-2 text-primary"></i>Dokumen Terbaru Diupload</span>
            <span class="text-muted" style="font-size:0.72rem;">10 aktivitas terakhir</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size:0.78rem;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th style="width:30px; color:#9ca3af; font-weight:500;"></th>
                        <th style="color:#6b7280; font-weight:500;">File</th>
                        <th style="width:200px; color:#6b7280; font-weight:500;">OPD</th>
                        <th style="color:#6b7280; font-weight:500;">Data yang Diminta</th>
                        <th style="width:160px; color:#6b7280; font-weight:500;">Surat</th>
                        <th style="width:100px; color:#6b7280; font-weight:500;">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($aktivitasTerbaru as $dok)
                @php
                    $surat  = $dok->permintaan?->surat;
                    $opd    = $dok->permintaanOpd?->opd ?? '-';
                    $opdUrl = $dok->permintaanOpd?->opd ? url('/opd/' . rawurlencode($dok->permintaanOpd->opd)) : null;
                    $itemUrl = $opdUrl ?? ($surat ? route('surat.show', $surat) : '#');
                @endphp
                <tr style="transition:background 0.1s;" onmouseenter="this.style.background='#f8f9fa'" onmouseleave="this.style.background=''">
                    <td class="text-center" style="color:#3b82f6;"><i class="bi bi-file-earmark-arrow-up"></i></td>
                    <td style="max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        <a href="{{ $itemUrl }}" class="text-decoration-none fw-semibold text-dark" title="{{ $dok->nama_file }}">
                            {{ $dok->nama_file }}
                        </a>
                    </td>
                    <td class="text-muted" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px;" title="{{ $opd }}">{{ $opd }}</td>
                    <td class="text-muted" style="max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $dok->permintaan?->judul_permintaan }}">
                        {{ Str::limit($dok->permintaan?->judul_permintaan ?? '-', 50) }}
                    </td>
                    <td>
                        @if($surat)
                        <a href="{{ route('surat.show', $surat) }}" class="text-decoration-none">
                            <span style="font-size:0.68rem; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:1px 7px; border-radius:4px; font-weight:500;">
                                {{ $surat->nomor_surat }}
                            </span>
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-muted" style="white-space:nowrap; font-size:0.72rem;">{{ $dok->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size:1.5rem; display:block; margin-bottom:4px;"></i>
                        Belum ada dokumen diupload
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
