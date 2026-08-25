<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judulLaporan }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            color: #000;
            font-size: 12px;
            line-height: 1.4;
        }
        .kop-surat { width: 100%; border: none; margin-bottom: 0; margin-top: -5px; }
        .kop-surat td { border: none; padding: 0; vertical-align: middle; }
        .kop-logo-left { width: 90px; text-align: left; }
        .kop-logo-right { width: 90px; text-align: right; }
        .kop-logo-left img, .kop-logo-right img { width: 65px; height: auto; }
        .kop-text { text-align: center; }
        .kop-title-1 { font-size: 16px; font-weight: normal; margin-bottom: 2px; font-family: Arial, sans-serif; }
        .kop-title-2 { font-size: 22px; font-weight: bold; margin-bottom: 2px; letter-spacing: 1px; font-family: Arial, sans-serif; }
        .kop-address { font-size: 11px; font-family: Arial, sans-serif; }
        .kop-line-1 { border-top: 3px solid #000; margin-top: 8px; }
        .kop-line-2 { border-top: 1px solid #000; margin-top: 2px; margin-bottom: 20px; }

        .report-header {
            margin-bottom: 15px;
            text-align: center;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0 0 4px 0;
        }
        .meta {
            font-size: 12px;
            margin: 2px 0;
        }
        .body-surat {
            margin-bottom: 15px;
            text-align: justify;
        }
        .body-surat p {
            margin: 8px 0;
            text-indent: 30px;
        }
        .body-surat ul {
            margin: 5px 0 10px 40px;
            padding: 0;
        }
        .body-surat li {
            margin: 2px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }
        table.data-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f2f2f2;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .ttd-section {
            width: 100%;
            border: none;
            margin-top: 30px;
            page-break-inside: avoid;
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
            font-size: 9px;
            color: #666;
            font-family: "Courier New", Courier, monospace;
        }
        
        /* Details */
        .detail-section { margin-top: 20px; }
        .detail-header {
            margin-bottom: 10px;
        }
        .detail-title {
            font-size: 13px;
            font-weight: bold;
            text-decoration: underline;
        }
        .opd-card {
            border: 1px solid #000;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .opd-card.new-page {
            page-break-before: always;
            break-before: page;
        }
        .opd-card-header {
            border-bottom: 1px solid #000;
            background: #f2f2f2;
            padding: 5px 8px;
            font-weight: bold;
        }
        .opd-card-body {
            padding: 8px;
        }
        .status-block {
            margin-bottom: 10px;
        }
        .status-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .surat-group {
            margin-bottom: 8px;
        }
        .surat-head {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .detail-list {
            margin: 0 0 0 20px;
            padding: 0;
        }
        .detail-list li {
            margin-bottom: 3px;
        }
        .empty-note {
            font-style: italic;
            color: #555;
        }
    </style>
</head>
<body>
    <table class="kop-surat">
        <tr>
            <td class="kop-logo-left">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo-puncak-jaya.png'))) }}" alt="Logo Puncak Jaya">
            </td>
            <td class="kop-text">
                <div class="kop-title-1">PEMERINTAH KABUPATEN PUNCAK JAYA</div>
                <div class="kop-title-2">INSPEKTORAT</div>
                <div class="kop-address">Jl. Drs. P.A.Coem, No.01, Mulia, Puncak Jaya</div>
                <div class="kop-address">email : inspektorat@puncakjayakab.go.id</div>
            </td>
            <td class="kop-logo-right">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo-inspektorat.png'))) }}" alt="Logo Inspektorat">
            </td>
        </tr>
    </table>
    <div class="kop-line-1"></div>
    <div class="kop-line-2"></div>

    <div class="report-header">
        <div class="title">{{ strtoupper($judulLaporan) }}</div>
        @if(isset($pemeriksaan) && $pemeriksaan)
            <div class="meta" style="font-weight:bold;">Pemeriksaan: {{ $pemeriksaan->nama }} ({{ $pemeriksaan->tahun }})</div>
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
        
        <p>Berdasarkan data yang telah dihimpun per tanggal {{ $generatedAt->setTimezone('Asia/Jayapura')->format('d-m-Y H:i') }} WIT, berikut adalah rincian capaian progres pemenuhan dokumen dari masing-masing OPD:</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No.</th>
                <th>Nama OPD</th>
                <th style="width: 60px;">Total</th>
                <th style="width: 60px;">Belum</th>
                <th style="width: 60px;">Proses</th>
                <th style="width: 60px;">Selesai</th>
                <th style="width: 80px;">Capaian (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats as $i => $row)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $row['opd'] }}</td>
                    <td class="text-center">{{ $row['total'] }}</td>
                    <td class="text-center">{{ $row['belum'] }}</td>
                    <td class="text-center">{{ $row['proses'] }}</td>
                    <td class="text-center">{{ $row['selesai'] }}</td>
                    <td class="text-right">{{ number_format((float) $row['progress'], 2, ',', '.') }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data monitoring untuk kriteria yang dipilih.</td>
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
                Mulia, {{ $generatedAt->setTimezone('Asia/Jayapura')->format('d M Y') }}<br>
                <strong>Inspektur Kabupaten Puncak Jaya</strong>
                <br>
                @php
                    $qrCodeUrl = route('validasi', ['kode' => 'DOC-'.$generatedAt->format('YmdHis')]);
                    $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(90)->margin(0)->generate($qrCodeUrl);
                    $qrCodeBase64 = base64_encode($qrCodeSvg);
                @endphp
                <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" alt="QR Code Validasi" style="margin-top: 10px; margin-bottom: 5px;">
                <br>
                <i>(Ditandatangani secara elektronik)</i>
            </td>
        </tr>
    </table>

    @if(!empty($showDetail))
        <div class="detail-section">
            <div class="detail-header">
                <div class="detail-title">LAMPIRAN: RINCIAN DOKUMEN PER OPD</div>
            </div>

            @php
                $statusClassMap = [
                    'selesai' => 'Selesai',
                    'proses' => 'Proses',
                    'belum' => 'Belum',
                ];
            @endphp

            @forelse($detailByStatus as $opdGroup)
                <div class="opd-card new-page">
                    <div class="opd-card-header">{{ $opdGroup['opd'] }}</div>
                    <div class="opd-card-body">
                        @foreach(($opdGroup['statuses'] ?? []) as $st)
                            <div class="status-block">
                                <div class="status-title">Status: {{ $st['status_label'] }}</div>

                                @if(empty($st['surat_groups']))
                                    <p class="empty-note">- Nihil -</p>
                                @else
                                    @foreach($st['surat_groups'] as $sg)
                                        <div class="surat-group">
                                            <div class="surat-head">
                                                Surat: {{ $sg['nomor_surat'] }} ({{ $sg['perihal'] ?? '-' }})
                                            </div>
                                            @if(empty($sg['items']))
                                                <p class="empty-note">- Tidak ada rincian -</p>
                                            @else
                                                <ul class="detail-list">
                                                    @foreach($sg['items'] as $item)
                                                        <li>{{ \Illuminate\Support\Str::limit($item, 240) }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="empty-note">Tidak ada detail data untuk filter yang dipilih.</div>
            @endforelse
        </div>
    @endif

    <div class="footer">
        Dicetak otomatis oleh Sistem Informasi Inspectra ({{ $generatedAt->setTimezone('Asia/Jayapura')->format('d-m-Y H:i') }})
    </div>
</body>
</html>
