@extends('layouts.app')
@section('title', 'Dashboard')

@section('styles')
<style>
/* ── Magnific AI Inspired Aesthetic ── */
body, button, input, select, textarea, h1, h2, h3, h4, h5, h6 {
    font-family: 'Satoshi', 'Inter', 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
}

/* ── Hero Greeting Card ── */
.magnific-hero {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 40%, #f1f5f9 100%);
    border-radius: 22px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
    padding: 2.6rem 2rem 2.2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

/* Subtle Geometric Grid & Soft Glow Orbs */
.magnific-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(148, 163, 184, 0.22) 1.2px, transparent 1.2px);
    background-size: 24px 24px;
    opacity: 0.65;
    pointer-events: none;
    z-index: 0;
}

.magnific-hero-shape-1 {
    position: absolute;
    top: -60px;
    left: -60px;
    width: 260px;
    height: 260px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.18) 0%, rgba(59, 130, 246, 0) 70%);
    pointer-events: none;
    z-index: 0;
    filter: blur(24px);
}

.magnific-hero-shape-2 {
    position: absolute;
    bottom: -80px;
    right: -40px;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(168, 85, 247, 0.15) 0%, rgba(168, 85, 247, 0) 70%);
    pointer-events: none;
    z-index: 0;
    filter: blur(28px);
}

.magnific-hero-shape-3 {
    position: absolute;
    top: 15%;
    right: 20%;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.12) 0%, rgba(16, 185, 129, 0) 70%);
    pointer-events: none;
    z-index: 0;
    filter: blur(20px);
}

.neuron-network-svg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 0;
}

.magnific-hero-content {
    position: relative;
    z-index: 1;
}

.magnific-hero-title {
    font-size: 1.85rem;
    font-weight: 900;
    color: #0f172a;
    letter-spacing: -0.035em;
    margin-bottom: 0.35rem;
}

.magnific-hero-sub {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 500;
    margin-bottom: 1.75rem;
}

.magnific-search-wrap {
    max-width: 560px;
    margin: 0 auto 2.2rem;
    position: relative;
}

.magnific-search-input {
    width: 100%;
    height: 50px;
    border-radius: 99px;
    border: 1px solid #cbd5e1;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(8px);
    padding-left: 3.2rem;
    padding-right: 4.5rem;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
}

.magnific-search-input:focus {
    background: #ffffff;
    border-color: #2563eb;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    outline: none;
}

.magnific-search-icon {
    position: absolute;
    left: 1.2rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 1.1rem;
}

.magnific-search-badge {
    position: absolute;
    right: 1.2rem;
    top: 50%;
    transform: translateY(-50%);
    background: #e2e8f0;
    color: #64748b;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
}

/* ── Quick Action Category Tiles Grid ── */
.magnific-tools-grid {
    display: flex;
    justify-content: center;
    gap: 1.2rem;
    flex-wrap: wrap;
}

.tool-tile {
    width: 98px;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    transition: transform 0.2s ease;
}

.tool-tile:hover {
    transform: translateY(-4px);
}

.tool-tile-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}

.tool-tile-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #334155;
    text-align: center;
    line-height: 1.2;
}

/* ── KPI Stat Cards ── */
.kpi-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
    padding: 1.35rem 1.4rem;
    position: relative;
    overflow: hidden;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.kpi-card:hover {
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.07);
    transform: translateY(-2px);
    border-color: #cbd5e1;
}
.kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.6rem;
}
.kpi-label {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.kpi-icon-wrap {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.kpi-value {
    font-size: 2.15rem;
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

/* ── Section Cards ── */
.sec-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.sec-header {
    padding: 1rem 1.35rem;
    border-bottom: 1px solid #f1f5f9;
    font-weight: 700;
    font-size: 0.84rem;
    color: #0f172a;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
}
.sec-body {
    overflow-y: auto;
    flex: 1;
}

/* ── List Row Items ── */
.row-item {
    padding: 0.85rem 1.35rem;
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
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    font-weight: 800;
    flex-shrink: 0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.06);
}
.rank-gold { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; }
.rank-silver { background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%); color: #ffffff; }
.rank-bronze { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color: #ffffff; }
.rank-default { background: #f1f5f9; color: #64748b; box-shadow: none; }

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

.dl-pill {
    font-size: 0.7rem;
    padding: 3px 10px;
    border-radius: 999px;
    font-weight: 700;
    white-space: nowrap;
    flex-shrink: 0;
}
</style>
@endsection

@section('content')
<div class="container-fluid py-2" style="max-width:1320px;">

    {{-- ══ HERO GREETING BANNER (Magnific AI Style) ══ --}}
    <div class="magnific-hero mb-4">
        <div class="magnific-hero-shape-1"></div>
        <div class="magnific-hero-shape-2"></div>
        <div class="magnific-hero-shape-3"></div>

        {{-- Texture Lines & Neuron Connections SVG (Samar-samar / Subtle) --}}
        <svg class="neuron-network-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 300" preserveAspectRatio="none">
            <defs>
                <linearGradient id="neuronGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.2" />
                    <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0.08" />
                </linearGradient>
                <linearGradient id="neuronGrad2" x1="0%" y1="100%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.18" />
                    <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.06" />
                </linearGradient>
            </defs>

            <!-- Neuron Main Connection Synapses -->
            <path d="M 50,30 L 180,80 L 340,40 L 520,100 L 700,50 L 860,110 L 960,30" stroke="url(#neuronGrad1)" stroke-width="1.2" fill="none" opacity="0.35" />
            <path d="M 90,210 L 250,170 L 420,230 L 600,180 L 770,240 L 930,190" stroke="url(#neuronGrad2)" stroke-width="1.2" fill="none" opacity="0.35" />

            <!-- Interconnecting Neural Network Fibers -->
            <line x1="50" y1="30" x2="90" y2="210" stroke="#3b82f6" stroke-width="1" opacity="0.2" stroke-dasharray="4,4" />
            <line x1="180" y1="80" x2="250" y2="170" stroke="#8b5cf6" stroke-width="1" opacity="0.22" />
            <line x1="340" y1="40" x2="250" y2="170" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" />
            <line x1="340" y1="40" x2="420" y2="230" stroke="#6366f1" stroke-width="1" opacity="0.2" />
            <line x1="520" y1="100" x2="420" y2="230" stroke="#10b981" stroke-width="1" opacity="0.22" stroke-dasharray="5,4" />
            <line x1="520" y1="100" x2="600" y2="180" stroke="#3b82f6" stroke-width="1" opacity="0.22" />
            <line x1="700" y1="50" x2="600" y2="180" stroke="#8b5cf6" stroke-width="0.8" opacity="0.18" />
            <line x1="700" y1="50" x2="770" y2="240" stroke="#0284c7" stroke-width="1" opacity="0.2" stroke-dasharray="4,4" />
            <line x1="860" y1="110" x2="770" y2="240" stroke="#10b981" stroke-width="1" opacity="0.18" />
            <line x1="860" y1="110" x2="930" y2="190" stroke="#8b5cf6" stroke-width="1" opacity="0.22" />

            <!-- Secondary Neural Branches -->
            <path d="M 180,80 L 130,25 L 240,15 L 340,40" stroke="#60a5fa" stroke-width="0.8" fill="none" opacity="0.15" />
            <path d="M 600,180 L 660,265 L 720,285 L 770,240" stroke="#c084fc" stroke-width="0.8" fill="none" opacity="0.15" />

            <!-- Synaptic Node Bulbs (Glowing Neurons) -->
            <circle cx="50" cy="30" r="3.5" fill="#3b82f6" opacity="0.45" />
            <circle cx="50" cy="30" r="7" fill="#3b82f6" opacity="0.12" />

            <circle cx="180" cy="80" r="4" fill="#8b5cf6" opacity="0.5" />
            <circle cx="180" cy="80" r="8" fill="#8b5cf6" opacity="0.12" />

            <circle cx="340" cy="40" r="3" fill="#3b82f6" opacity="0.4" />

            <circle cx="520" cy="100" r="4.5" fill="#10b981" opacity="0.5" />
            <circle cx="520" cy="100" r="9" fill="#10b981" opacity="0.12" />

            <circle cx="700" cy="50" r="4" fill="#0284c7" opacity="0.45" />

            <circle cx="860" cy="110" r="4.5" fill="#8b5cf6" opacity="0.5" />
            <circle cx="860" cy="110" r="9" fill="#8b5cf6" opacity="0.12" />

            <circle cx="90" cy="210" r="4" fill="#0284c7" opacity="0.4" />
            <circle cx="250" cy="170" r="3.5" fill="#6366f1" opacity="0.45" />
            <circle cx="420" cy="230" r="4.5" fill="#3b82f6" opacity="0.5" />
            <circle cx="420" cy="230" r="9" fill="#3b82f6" opacity="0.12" />

            <circle cx="600" cy="180" r="3.5" fill="#10b981" opacity="0.4" />
            <circle cx="770" cy="240" r="4" fill="#8b5cf6" opacity="0.45" />
            <circle cx="930" cy="190" r="3.5" fill="#3b82f6" opacity="0.4" />

            <!-- Sparkle Nodes -->
            <circle cx="130" cy="25" r="2" fill="#93c5fd" opacity="0.35" />
            <circle cx="240" cy="15" r="2" fill="#c4b5fd" opacity="0.35" />
            <circle cx="660" cy="265" r="2" fill="#a7f3d0" opacity="0.35" />
            <circle cx="720" cy="285" r="2" fill="#93c5fd" opacity="0.35" />
        </svg>

        <div class="magnific-hero-content">
            <h1 class="magnific-hero-title">Selamat datang, kelola dokumen pemeriksaan!</h1>
            <p class="magnific-hero-sub">Sistem Pusat Pemantauan & Pemenuhan Dokumen Pemeriksaan Pemerintah Daerah</p>

            {{-- Interactive Search Bar --}}
            <div class="magnific-search-wrap">
                <i class="bi bi-search magnific-search-icon"></i>
                <input type="text" class="magnific-search-input" id="dashboardSearch" placeholder="Cari data dokumen, surat permintaan, atau OPD..." onkeyup="filterDashboardItems(this.value)">
                <span class="magnific-search-badge">⌘ K</span>
            </div>

            {{-- Quick Action Tiles Grid (Spaces Icons) --}}
            <div class="magnific-tools-grid">
                <a href="{{ route('pemeriksaan.index') }}" class="tool-tile">
                    <div class="tool-tile-icon" style="background:#eff6ff; color:#2563eb;">
                        <i class="bi bi-card-checklist"></i>
                    </div>
                    <span class="tool-tile-label">Pemeriksaan</span>
                </a>

                <a href="{{ route('surat.index') }}" class="tool-tile">
                    <div class="tool-tile-icon" style="background:#fffbeb; color:#d97706;">
                        <i class="bi bi-envelope-paper-fill"></i>
                    </div>
                    <span class="tool-tile-label">Surat</span>
                </a>

                <a href="{{ route('opd.index') }}" class="tool-tile">
                    <div class="tool-tile-icon" style="background:#ecfdf5; color:#059669;">
                        <i class="bi bi-building-fill-check"></i>
                    </div>
                    <span class="tool-tile-label">OPD</span>
                </a>

                <a href="{{ route('laporan.index') }}" class="tool-tile">
                    <div class="tool-tile-icon" style="background:#fefce8; color:#d97706;">
                        <i class="bi bi-printer-fill"></i>
                    </div>
                    <span class="tool-tile-label">Cetak Laporan</span>
                </a>

                @if(auth()->user()->isAdmin())
                <a href="{{ route('google-drive.index') }}" class="tool-tile">
                    <div class="tool-tile-icon" style="background:#e0f2fe; color:#0284c7;">
                        <i class="bi bi-google"></i>
                    </div>
                    <span class="tool-tile-label">Drive Sync</span>
                </a>

                <a href="{{ route('backup-dokumen.index') }}" class="tool-tile">
                    <div class="tool-tile-icon" style="background:#ffe4e6; color:#e11d48;">
                        <i class="bi bi-archive-fill"></i>
                    </div>
                    <span class="tool-tile-label">Backup</span>
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ══ KPI CARDS ══ --}}
    <div class="row g-3 mb-4">
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

        {{-- KPI 2: Surat Permintaan --}}
        <div class="col-6 col-md-3">
            <div class="kpi-card h-100">
                <div class="kpi-header">
                    <span class="kpi-label">Surat Permintaan</span>
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
    <div class="row g-3 mb-4">
        @if($suratDeadlineDekat > 0 || $suratOverdue > 0)
        <div class="col-12 col-md-6">
            <div class="p-3 rounded-4 border d-flex align-items-center gap-3" style="background:#fef2f2; border-color:#fecaca;">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;background:#fee2e2;color:#dc2626;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size:1.15rem;"></i>
                </div>
                <div>
                    <div class="fw-bold text-danger" style="font-size:0.84rem;">Peringatan Batas Waktu Surat</div>
                    <div style="color:#991b1b;font-size:0.76rem;">
                        <strong>{{ $suratOverdue }}</strong> surat lewat deadline &bull; <strong>{{ $suratDeadlineDekat }}</strong> mendekati deadline (H‑14)
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($opdBelum > 0)
        <div class="col-12 col-md-6">
            <div class="p-3 rounded-4 border d-flex align-items-center gap-3" style="background:#fffbeb; border-color:#fde68a;">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;background:#fef3c7;color:#d97706;">
                    <i class="bi bi-info-circle-fill" style="font-size:1.15rem;"></i>
                </div>
                <div>
                    <div class="fw-bold" style="color:#92400e;font-size:0.84rem;">Status Pemenuhan Penugasan</div>
                    <div style="color:#b45309;font-size:0.76rem;">
                        <strong>{{ $opdBelum }}</strong> penugasan OPD belum ditindaklanjuti sama sekali
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ══ ROW: RANKING OPD + PROGRESS ══ --}}
    <div class="row g-3 mb-4">
        {{-- Ranking OPD --}}
        <div class="col-12 col-lg-7">
            <div class="sec-card">
                <div class="sec-header">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-trophy-fill text-warning" style="font-size:1rem;"></i>
                        <span>Ranking Kepatuhan OPD <span class="text-muted fw-normal">(Pemeriksaan Aktif)</span></span>
                    </span>
                    <a href="{{ route('opd.index') }}" class="text-decoration-none fw-semibold" style="font-size:0.78rem;color:#2563eb;">Lihat Semua OPD &rarr;</a>
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
                    <a href="{{ route('pemeriksaan.index') }}" class="text-decoration-none fw-semibold" style="font-size:0.78rem;color:#2563eb;">Lihat Semua &rarr;</a>
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
                    <a href="{{ route('dokumen.index') }}" class="text-decoration-none fw-semibold" style="font-size:0.78rem;color:#2563eb;">Lihat Dokumen &rarr;</a>
                </div>
                <div class="sec-body p-0">
                    @forelse($aktivitasTerbaru as $dok)
                    @php
                        $surat = $dok->permintaanOpd
                            ? $dok->permintaanOpd->permintaan->surat
                            : ($dok->permintaan ? $dok->permintaan->surat : null);
                        $pemeriksaan = $surat?->pemeriksaan;
                    @endphp
                    <div class="row-item d-flex gap-3 align-items-start search-target">
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
                    <a href="{{ route('surat.index') }}" class="text-decoration-none fw-semibold" style="font-size:0.78rem;color:#2563eb;">Semua Surat &rarr;</a>
                </div>
                <div class="sec-body p-0">
                    @forelse($suratDeadline as $surat)
                    @php
                        $isOverdue = $surat->deadline < now();
                        $daysLeft  = (int) now()->diffInDays($surat->deadline, false);
                    @endphp
                    <a href="{{ route('surat.show', $surat->id) }}" class="text-decoration-none text-dark row-item d-block search-target">
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

@section('scripts')
<script>
function filterDashboardItems(query) {
    const q = query.toLowerCase().trim();
    const items = document.querySelectorAll('.search-target');
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(q)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
@endsection
