<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $validated['judul_laporan'] }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 10mm 12mm 15mm 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8pt;
            color: #1e293b;
            line-height: 1.2;
            background: #fff;
        }
        
        /* FORMAL KOP DINAS */
        .kop-table {
            width: 100%;
            border-bottom: 3px double #000000;
            padding-bottom: 6px;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .kop-logo-left {
            width: 65px;
            text-align: left;
            vertical-align: middle;
        }
        .kop-logo-right {
            width: 65px;
            text-align: right;
            vertical-align: middle;
        }
        .kop-logo img {
            max-width: 58px;
            max-height: 58px;
            height: auto;
        }
        .kop-text {
            text-align: center;
            vertical-align: middle;
        }
        .kop-text h4 {
            margin: 0;
            font-size: 11pt;
            font-weight: 800;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text h3 {
            margin: 2px 0 0 0;
            font-size: 13pt;
            font-weight: 900;
            color: #0b192c;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-text p {
            margin: 3px 0 0 0;
            font-size: 7.5pt;
            color: #334155;
        }

        .report-title-box {
            text-align: center;
            margin-bottom: 12px;
        }
        .report-title-box h5 {
            margin: 0;
            font-size: 10.5pt;
            font-weight: 800;
            color: #0b192c;
            text-transform: uppercase;
        }
        .report-title-box p {
            margin: 3px 0 0 0;
            font-size: 8pt;
            color: #475569;
        }
        
        /* KPI SUMMARY CARDS */
        .kpi-container {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 5px 8px;
            text-align: center;
        }
        .kpi-title {
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
        }
        .kpi-value {
            font-size: 10.5pt;
            font-weight: 800;
            margin-top: 2px;
        }
        .text-selesai { color: #16a34a; }
        .text-proses { color: #d97706; }
        .text-belum { color: #dc2626; }
        .text-primary-dark { color: #0b192c; }

        /* MATRIX TABLE */
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            margin-bottom: 15px;
        }
        .matrix-table th {
            background-color: #0b192c;
            color: #ffffff;
            font-weight: 700;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #1e293b;
            vertical-align: middle;
        }
        .matrix-table td {
            padding: 4px 5px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }

        .badge-status {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: 700;
            font-size: 6.5pt;
            text-align: center;
        }
        .badge-selesai { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-proses { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-belum { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

        .dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            margin-right: 3px;
            vertical-align: middle;
        }
        .dot-green { background-color: #16a34a; }
        .dot-yellow { background-color: #d97706; }
        .dot-red { background-color: #dc2626; }

        .progress-bar-bg {
            background-color: #e2e8f0;
            height: 5px;
            border-radius: 3px;
            overflow: hidden;
            width: 100%;
            margin-top: 2px;
        }
        .progress-bar-fill {
            height: 100%;
            background-color: #16a34a;
        }

        .signature-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 35%;
            text-align: center;
            vertical-align: top;
            font-size: 8pt;
        }
    </style>
</head>
<body>

    {{-- KOP FORMAL DINAS INSPEKTORAT --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo-left kop-logo">
                @if($logoLeftBase64)
                    <img src="{{ $logoLeftBase64 }}" alt="Logo Kab">
                @endif
            </td>
            <td class="kop-text">
                <h4>PEMERINTAH KABUPATEN PUNCAK JAYA</h4>
                <h3>INSPEKTORAT DAERAH</h3>
                <p>Jl. Yos Sudarso No. 1 Mulia - Papua Tengah &bull; Email: inspektorat@puncakjayakab.go.id</p>
            </td>
            <td class="kop-logo-right kop-logo">
                @if($logoRightBase64)
                    <img src="{{ $logoRightBase64 }}" alt="Logo Inspektorat">
                @endif
            </td>
        </tr>
    </table>

    {{-- REPORT TITLE --}}
    <div class="report-title-box">
        <h5>{{ $validated['judul_laporan'] }}</h5>
        <p>
            @if($pemeriksaan)
                Pemeriksaan: <strong>{{ $pemeriksaan->nama }} (Tahun {{ $pemeriksaan->tahun }})</strong> &bull;
            @endif
            Tanggal Cetak: {{ $generatedAt->translatedFormat('d F Y - H:i') }} WIT
        </p>
    </div>

    {{-- EXECUTIVE KPI SUMMARY --}}
    <table class="kpi-container">
        <tr>
            <td style="width: 20%; padding-right: 4px;">
                <div class="kpi-card">
                    <div class="kpi-title">Total Entitas / OPD</div>
                    <div class="kpi-value text-primary-dark">{{ $summary['total_opd'] }}</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0 4px;">
                <div class="kpi-card">
                    <div class="kpi-title">Lengkap (100%)</div>
                    <div class="kpi-value text-selesai">{{ $summary['opd_lengkap'] }} OPD</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0 4px;">
                <div class="kpi-card">
                    <div class="kpi-title">Sedang Diproses</div>
                    <div class="kpi-value text-proses">{{ $summary['opd_proses'] }} OPD</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0 4px;">
                <div class="kpi-card">
                    <div class="kpi-title">Belum Ada Data</div>
                    <div class="kpi-value text-belum">{{ $summary['opd_belum'] }} OPD</div>
                </div>
            </td>
            <td style="width: 20%; padding-left: 4px;">
                <div class="kpi-card" style="background: #0b192c; color: #fff; border-color: #0b192c;">
                    <div class="kpi-title" style="color: #94a3b8;">Total Kepatuhan</div>
                    <div class="kpi-value text-selesai" style="color: #4ade80;">{{ $summary['overall_pct'] }}%</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- BAGIAN 1: MATRIKS TABEL PEMENUHAN (GROUPED BY JUDUL PERMINTAAN) --}}
    <table class="matrix-table">
        <thead>
            <tr>
                <th style="width: 22px;">NO</th>
                <th style="width: 140px;" class="text-left">NAMA ENTITAS / OPD</th>
                <th style="width: 65px;">KEPATUHAN</th>
                <th style="width: 30px;">SELESAI</th>
                <th style="width: 30px;">PROSES</th>
                <th style="width: 30px;">BELUM</th>
                @foreach($judulGroups as $jTitle => $gInfo)
                    <th style="font-size: 6.5pt;">{{ Str::limit($jTitle, 28) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($opdRows as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-left" style="font-weight: 600;">{{ $row['opd_nama'] }}</td>
                <td class="text-center">
                    <div style="font-weight: 800; font-size: 7.5pt; color: {{ $row['progress_pct'] >= 100 ? '#15803d' : ($row['progress_pct'] > 0 ? '#b45309' : '#dc2626') }}">
                        {{ $row['progress_pct'] }}%
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: {{ $row['progress_pct'] }}%;"></div>
                    </div>
                </td>
                <td class="text-center fw-bold text-selesai">{{ $row['selesai'] }}</td>
                <td class="text-center fw-bold text-proses">{{ $row['proses'] }}</td>
                <td class="text-center fw-bold text-belum">{{ $row['belum'] }}</td>
                @foreach($judulGroups as $jTitle => $gInfo)
                    @php
                        $gs = $row['groupStats'][$jTitle] ?? null;
                    @endphp
                    <td class="text-center">
                        @if($gs && $gs['total'] > 0)
                            @if($gs['pct'] >= 100)
                                <span class="badge-status badge-selesai"><span class="dot dot-green"></span>100% ({{ $gs['selesai'] }}/{{ $gs['total'] }})</span>
                            @elseif($gs['selesai'] > 0 || $gs['proses'] > 0)
                                <span class="badge-status badge-proses"><span class="dot dot-yellow"></span>{{ $gs['pct'] }}% ({{ $gs['selesai'] }}/{{ $gs['total'] }})</span>
                            @else
                                <span class="badge-status badge-belum"><span class="dot dot-red"></span>0% (0/{{ $gs['total'] }})</span>
                            @endif
                        @else
                            <span style="color:#94a3b8;">-</span>
                        @endif
                    </td>
                @endforeach
            </tr>
            @empty
            <tr>
                <td colspan="{{ 6 + count($judulGroups) }}" class="text-center" style="padding: 15px; color: #64748b;">
                    Tidak ada data pemenuhan dokumen yang sesuai dengan filter.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN REGION --}}
    <table class="signature-table">
        <tr>
            <td style="width: 65%;"></td>
            <td class="signature-box">
                <p>Mulia, {{ $generatedAt->translatedFormat('d F Y') }}</p>
                <p style="font-weight: 700; margin-top: -5px;">
                    {{ $validated['penandatangan_jabatan'] ?? 'Inspektur Kabupaten Puncak Jaya' }}
                </p>
                <div style="height: 45px;"></div>
                <p style="font-weight: 800; text-decoration: underline; margin-bottom: 2px;">
                    {{ $validated['penandatangan_nama'] ?? '......................................................' }}
                </p>
                @if(!empty($validated['penandatangan_nip']))
                    <p style="font-size: 7.5pt; color: #475569; margin: 0;">NIP. {{ $validated['penandatangan_nip'] }}</p>
                @endif
            </td>
        </tr>
    </table>

    {{-- BAGIAN 2: RINCIAN ITEM PER ENTITAS / OPD (SELESAI, PROSES, BELUM) --}}
    <div style="page-break-before: always;"></div>

    <div style="text-align: center; margin-bottom: 12px; border-bottom: 2px solid #0b192c; padding-bottom: 6px;">
        <h4 style="margin: 0; font-size: 11pt; font-weight: 800; color: #0b192c; text-transform: uppercase;">
            BAGIAN II: RINCIAN PEMENUHAN DOKUMEN PER ENTITAS / OPD
        </h4>
        <p style="margin: 2px 0 0 0; font-size: 8pt; color: #64748b;">
            Rincian detail item permintaan data BPK untuk setiap OPD dengan pengelompokan status: <strong>Selesai</strong>, <strong>Proses</strong>, dan <strong>Belum Ada</strong>
        </p>
    </div>

    @foreach($opdRows as $opdIndex => $opd)
    <div style="margin-bottom: 10px; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden; page-break-inside: avoid;">
        <div style="background-color: #f1f5f9; padding: 5px 8px; border-bottom: 1px solid #cbd5e1;">
            <table style="width: 100%; border-collapse: collapse; font-size: 8pt;">
                <tr>
                    <td style="font-weight: 800; color: #0b192c;">
                        {{ $opdIndex + 1 }}. {{ $opd['opd_nama'] }}
                    </td>
                    <td style="text-align: right; font-weight: 700; color: {{ $opd['progress_pct'] >= 100 ? '#15803d' : ($opd['progress_pct'] > 0 ? '#b45309' : '#dc2626') }}">
                        Progress Kepatuhan: {{ $opd['progress_pct'] }}% (Selesai: {{ $opd['selesai'] }} | Proses: {{ $opd['proses'] }} | Belum: {{ $opd['belum'] }})
                    </td>
                </tr>
            </table>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; font-size: 7pt;">
            <tr>
                {{-- SELESAI --}}
                <td style="width: 33.3%; vertical-align: top; padding: 6px; border-right: 1px solid #cbd5e1;">
                    <div style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 3px 5px; font-weight: 800; border-radius: 3px; margin-bottom: 4px;">
                        <span class="dot dot-green"></span> SELESAI ({{ count($opd['detailItems']['selesai']) }})
                    </div>
                    <ol style="margin: 0; padding-left: 14px; color: #166534; line-height: 1.3;">
                        @forelse($opd['detailItems']['selesai'] as $itemText)
                            <li style="margin-bottom: 2px;">{{ $itemText }}</li>
                        @empty
                            <li style="list-style: none; color: #94a3b8; font-style: italic; margin-left: -14px;">(Tidak ada)</li>
                        @endforelse
                    </ol>
                </td>

                {{-- PROSES --}}
                <td style="width: 33.3%; vertical-align: top; padding: 6px; border-right: 1px solid #cbd5e1;">
                    <div style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; padding: 3px 5px; font-weight: 800; border-radius: 3px; margin-bottom: 4px;">
                        <span class="dot dot-yellow"></span> PROSES ({{ count($opd['detailItems']['proses']) }})
                    </div>
                    <ol style="margin: 0; padding-left: 14px; color: #92400e; line-height: 1.3;">
                        @forelse($opd['detailItems']['proses'] as $itemText)
                            <li style="margin-bottom: 2px;">{{ $itemText }}</li>
                        @empty
                            <li style="list-style: none; color: #94a3b8; font-style: italic; margin-left: -14px;">(Tidak ada)</li>
                        @endforelse
                    </ol>
                </td>

                {{-- BELUM --}}
                <td style="width: 33.3%; vertical-align: top; padding: 6px;">
                    <div style="background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; padding: 3px 5px; font-weight: 800; border-radius: 3px; margin-bottom: 4px;">
                        <span class="dot dot-red"></span> BELUM ADA ({{ count($opd['detailItems']['belum']) }})
                    </div>
                    <ol style="margin: 0; padding-left: 14px; color: #991b1b; line-height: 1.3;">
                        @forelse($opd['detailItems']['belum'] as $itemText)
                            <li style="margin-bottom: 2px;">{{ $itemText }}</li>
                        @empty
                            <li style="list-style: none; color: #94a3b8; font-style: italic; margin-left: -14px;">(Tidak ada)</li>
                        @endforelse
                    </ol>
                </td>
            </tr>
        </table>
    </div>
    @endforeach

</body>
</html>
