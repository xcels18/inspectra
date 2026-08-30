<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judulLaporan }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm 12mm 15mm 12mm;
        }
        body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9pt;
            line-height: 1.4;
            background: #fff;
        }
        
        /* KOP SURAT FORMAL */
        .kop-surat { width: 100%; border: none; margin-bottom: 0; margin-top: -5px; }
        .kop-surat td { border: none; padding: 0; vertical-align: middle; }
        .kop-logo-left { width: 70px; text-align: left; }
        .kop-logo-right { width: 70px; text-align: right; }
        .kop-logo-left img, .kop-logo-right img { width: 58px; height: auto; }
        .kop-text { text-align: center; }
        .kop-title-1 { font-size: 10.5pt; font-weight: normal; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-title-2 { font-size: 13.5pt; font-weight: 900; margin-bottom: 2px; letter-spacing: 1px; color: #0b192c; text-transform: uppercase; }
        .kop-address { font-size: 7.5pt; color: #475569; }
        .kop-line-double { border-top: 3px double #000; margin-top: 6px; margin-bottom: 12px; }

        .report-header {
            margin-bottom: 12px;
            text-align: center;
        }
        .title {
            font-size: 11pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0b192c;
            margin: 0 0 2px 0;
        }
        .meta {
            font-size: 9pt;
            color: #475569;
            margin: 2px 0;
        }
        
        .body-surat {
            margin-bottom: 10px;
            text-align: justify;
            font-size: 8.5pt;
        }
        .body-surat p {
            margin: 4px 0;
            text-indent: 20px;
        }
        .body-surat ul {
            margin: 4px 0 6px 25px;
            padding: 0;
        }
        .body-surat li {
            margin: 1px 0;
        }

        /* TABEL DEPAN SURAT (DARK NAVY HEADER & BADGES) */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 12px;
            font-size: 8pt;
        }
        table.data-table th {
            background-color: #0b192c;
            color: #ffffff;
            font-weight: 700;
            padding: 5px 6px;
            text-align: center;
            border: 1px solid #1e293b;
            vertical-align: middle;
        }
        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .progress-bar-bg {
            background-color: #e2e8f0;
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
            width: 100%;
            margin-top: 2px;
        }
        .progress-bar-fill {
            height: 100%;
            background-color: #16a34a;
        }

        .badge-num {
            display: inline-block;
            font-weight: 700;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7.5pt;
        }
        .badge-num-belum { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-num-proses { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-num-selesai { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        
        /* TTD SECTION */
        .ttd-section {
            width: 100%;
            border: none;
            margin-top: 15px;
            page-break-inside: avoid;
            font-size: 8pt;
        }
        .ttd-section td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .footer {
            position: fixed;
            bottom: -5px;
            right: 0px;
            text-align: right;
            font-size: 7pt;
            color: #94a3b8;
        }

        /* LAMPIRAN (RINCIAN DOKUMEN PER OPD) */
        .detail-section {
            margin-top: 10px;
        }
        .detail-header {
            margin-bottom: 10px;
            border-bottom: 2px solid #0b192c;
            padding-bottom: 4px;
            text-align: center;
        }
        .detail-title {
            font-size: 10.5pt;
            font-weight: 800;
            color: #0b192c;
            text-transform: uppercase;
        }
        .detail-subtitle {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 2px;
        }

        .opd-card {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            margin-bottom: 10px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .opd-card-header {
            background-color: #f1f5f9;
            border-bottom: 1px solid #cbd5e1;
            padding: 5px 8px;
            font-weight: 800;
            font-size: 8pt;
            color: #0b192c;
        }

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

        .status-badge-head {
            font-weight: 800;
            font-size: 7pt;
            padding: 2px 5px;
            border-radius: 3px;
            margin-bottom: 4px;
            display: inline-block;
        }
        .head-selesai { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .head-proses { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .head-belum { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

        .surat-group-head {
            font-weight: 700;
            font-size: 7pt;
            color: #334155;
            margin-top: 3px;
            margin-bottom: 1px;
        }
        .item-list {
            margin: 0;
            padding-left: 12px;
            font-size: 6.8pt;
            line-height: 1.3;
        }
        .item-list li {
            margin-bottom: 1px;
        }
    </style>
</head>
<body>
    {{-- KOP FORMAL SURAT --}}
    <table class="kop-surat">
        <tr>
            <td class="kop-logo-left">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo-puncak-jaya.png'))) }}" alt="Logo Puncak Jaya">
            </td>
            <td class="kop-text">
                <div class="kop-title-1">PEMERINTAH KABUPATEN PUNCAK JAYA</div>
                <div class="kop-title-2">INSPEKTORAT DAERAH</div>
                <div class="kop-address">Jl. Yos Sudarso No. 1 Mulia - Papua Tengah &bull; Email: inspektorat@puncakjayakab.go.id</div>
            </td>
            <td class="kop-logo-right">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo-inspektorat.png'))) }}" alt="Logo Inspektorat">
            </td>
        </tr>
    </table>
    <div class="kop-line-double"></div>

    <div class="report-header">
        <div class="title">{{ strtoupper($judulLaporan) }}</div>
        @if(isset($pemeriksaan) && $pemeriksaan)
            <div class="meta" style="font-weight:bold; font-size:10pt; margin-top:2px;">{{ $pemeriksaan->nama }} (Tahun {{ $pemeriksaan->tahun }})</div>
        @endif
    </div>

    <div class="body-surat">
        <p>Bersama ini kami sampaikan Laporan Monitoring atas Pemenuhan Permintaan Data/Dokumen pada Organisasi Perangkat Daerah (OPD) di Lingkungan Pemerintah Kabupaten Puncak Jaya. Adapun laporan progres ini didasarkan pada permintaan surat berikut:</p>
        
        <ul>
            @foreach($selectedSurat as $surat)
                <li>
                    <strong>{{ $surat->nomor_surat }}</strong>
                    @if(!empty($surat->perihal))
                        — {{ $surat->perihal }}
                    @endif
                </li>
            @endforeach
        </ul>
        
        <p>Berdasarkan data yang telah dihimpun per tanggal {{ $generatedAt->setTimezone('Asia/Jayapura')->translatedFormat('d F Y - H:i') }} WIT, berikut adalah rincian capaian progres pemenuhan dokumen dari masing-masing OPD:</p>
    </div>

    {{-- TABEL DEPAN SURAT (REDESIGNED DARK NAVY HEADER & STATUS BADGES) --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 22px;">NO</th>
                <th class="text-left">NAMA ENTITAS / OPD</th>
                <th style="width: 45px;">TOTAL</th>
                <th style="width: 45px;">BELUM</th>
                <th style="width: 45px;">PROSES</th>
                <th style="width: 45px;">SELESAI</th>
                <th style="width: 75px;">CAPAIAN (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats as $i => $row)
                @php
                    $pct = (float) $row['progress'];
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-left" style="font-weight: 600;">{{ $row['opd'] }}</td>
                    <td class="text-center" style="font-weight: 700;">{{ $row['total'] }}</td>
                    <td class="text-center">
                        <span class="badge-num badge-num-belum">{{ $row['belum'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge-num badge-num-proses">{{ $row['proses'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge-num badge-num-selesai">{{ $row['selesai'] }}</span>
                    </td>
                    <td class="text-center">
                        <div style="font-weight: 800; font-size: 7.5pt; color: {{ $pct >= 100 ? '#15803d' : ($pct > 0 ? '#b45309' : '#dc2626') }}">
                            {{ number_format($pct, 2, ',', '.') }}%
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: {{ $pct }}%;"></div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 12px; color: #64748b;">Tidak ada data monitoring untuk kriteria yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="body-surat">
        <p>Demikian laporan ini dibuat untuk dapat dipergunakan sebagaimana mestinya. Atas perhatian dan kerja sama yang baik, kami ucapkan terima kasih.</p>
    </div>

    <table class="ttd-section">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: left;">
                Mulia, {{ $generatedAt->setTimezone('Asia/Jayapura')->translatedFormat('d F Y') }}<br>
                <strong>Inspektur Kabupaten Puncak Jaya</strong>
                <br>
                @php
                    $qrCodeUrl = route('validasi.index', ['kode' => 'DOC-'.$generatedAt->format('YmdHis')]);
                    $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(80)->margin(0)->generate($qrCodeUrl);
                    $qrCodeBase64 = base64_encode($qrCodeSvg);
                @endphp
                <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" alt="QR Code Validasi" style="margin-top: 8px; margin-bottom: 4px;">
                <br>
                <i style="font-size: 7.5pt; color: #64748b;">(Ditandatangani secara elektronik)</i>
            </td>
        </tr>
    </table>

    {{-- BAGIAN LAMPIRAN: RINCIAN DOKUMEN PER OPD (REDESIGNED 3-COLUMN STATUS CARDS) --}}
    @if(!empty($showDetail))
        <div style="page-break-before: always;"></div>
        <div class="detail-section">
            <div class="detail-header">
                <div class="detail-title">LAMPIRAN: RINCIAN DOKUMEN PER ENTITAS / OPD</div>
                <div class="detail-subtitle">Pengelompokan rincian item dokumen berdasarkan status pemenuhan</div>
            </div>

            @forelse($detailByStatus as $opdIndex => $opdGroup)
                <div class="opd-card">
                    <div class="opd-card-header">
                        {{ $opdIndex + 1 }}. {{ $opdGroup['opd'] }}
                    </div>
                    
                    <table style="width: 100%; border-collapse: collapse; font-size: 7.5pt;">
                        <tr>
                            @foreach(($opdGroup['statuses'] ?? []) as $st)
                                @php
                                    $isLast = $loop->last;
                                    $headClass = 'head-belum';
                                    $dotClass = 'dot-red';
                                    if ($st['status_label'] === 'Proses') {
                                        $headClass = 'head-proses'; $dotClass = 'dot-yellow';
                                    } elseif ($st['status_label'] === 'Selesai') {
                                        $headClass = 'head-selesai'; $dotClass = 'dot-green';
                                    }
                                @endphp
                                <td style="width: 33.3%; vertical-align: top; padding: 6px; {{ !$isLast ? 'border-right: 1px solid #cbd5e1;' : '' }}">
                                    <div class="status-badge-head {{ $headClass }}">
                                        <span class="dot {{ $dotClass }}"></span> {{ strtoupper($st['status_label']) }}
                                    </div>

                                    @if(empty($st['surat_groups']))
                                        <p style="margin: 0; color: #94a3b8; font-style: italic; font-size: 6.8pt;">- Nihil -</p>
                                    @else
                                        @foreach($st['surat_groups'] as $sg)
                                            <div class="surat-group-head">
                                                Surat: {{ $sg['nomor_surat'] }}
                                            </div>
                                            @if(empty($sg['items']))
                                                <p style="margin: 0; color: #94a3b8; font-style: italic; font-size: 6.8pt;">- Tidak ada rincian -</p>
                                            @else
                                                <ol class="item-list">
                                                    @foreach($sg['items'] as $item)
                                                        <li>{{ \Illuminate\Support\Str::limit($item, 200) }}</li>
                                                    @endforeach
                                                </ol>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </table>
                </div>
            @empty
                <div style="text-align: center; color: #64748b; font-style: italic; padding: 15px;">Tidak ada detail data untuk filter yang dipilih.</div>
            @endforelse
        </div>
    @endif

    <div class="footer">
        Dicetak otomatis oleh Sistem Informasi Inspectra ({{ $generatedAt->setTimezone('Asia/Jayapura')->translatedFormat('d F Y - H:i') }} WIT)
    </div>
</body>
</html>
