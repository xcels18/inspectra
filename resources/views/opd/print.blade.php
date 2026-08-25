<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judulLaporan }}</title>
    <style>
        @page {
            size: A4;
            margin: 14mm 10mm 14mm 10mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 12px;
        }
        .kop-surat { width: 100%; border: none; margin-bottom: 0; margin-top: -10px; }
        .kop-surat td { border: none; padding: 0; vertical-align: middle; }
        .kop-logo-left { width: 90px; text-align: left; }
        .kop-logo-right { width: 90px; text-align: right; }
        .kop-logo-left img, .kop-logo-right img { width: 75px; }
        .kop-text { text-align: center; }
        .kop-title-1 { font-size: 16px; font-weight: normal; margin-bottom: 2px; }
        .kop-title-2 { font-size: 24px; font-weight: bold; margin-bottom: 2px; letter-spacing: 1px; }
        .kop-address { font-size: 11px; }
        .kop-line-1 { border-top: 3px solid #000; margin-top: 8px; }
        .kop-line-2 { border-top: 1px solid #000; margin-top: 2px; margin-bottom: 15px; }

        .report-header {
            margin-bottom: 14px;
            text-align: center;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0 0 4px 0;
        }
        .meta {
            font-size: 11px;
            color: #4b5563;
            margin: 2px 0;
        }
        .summary-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 10px;
            margin: 10px 0 14px 0;
            font-size: 11px;
        }
        .summary-box ul {
            margin: 6px 0 0 16px;
            padding: 0;
        }
        .summary-box li {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 7px;
            vertical-align: middle;
        }
        th {
            background: #1e3a8a;
            color: #fff;
            font-size: 11px;
            text-align: center;
        }
        td {
            font-size: 11px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .opd-name { font-weight: 600; }
        .footer {
            position: fixed;
            bottom: 0px;
            right: 10px;
            text-align: left;
            font-size: 7px;
            color: #6b7280;
            font-family: "Courier New", Courier, monospace;
        }
        .bottom-space {
            height: 5px;
        }
        .detail-section {
            margin-top: 16px;
        }
        .detail-header {
            border-left: 4px solid #1e3a8a;
            background: #eef2ff;
            padding: 7px 10px;
            margin-bottom: 10px;
        }
        .detail-title {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: 0.2px;
        }
        .detail-subtitle {
            margin-top: 2px;
            font-size: 10.5px;
            color: #475569;
        }
        .opd-card {
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #ffffff;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .opd-card.new-page {
            page-break-before: always;
            break-before: page;
        }
        .opd-card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
        }
        .opd-card-body {
            padding: 7px 10px 8px 10px;
        }
        .status-block {
            margin-bottom: 7px;
        }
        .status-title {
            display: inline-block;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 0 0 4px 0;
            padding: 2px 7px;
            border-radius: 999px;
            border: 1px solid transparent;
        }
        .status-belum {
            color: #991b1b;
            background: #fee2e2;
            border-color: #fecaca;
        }
        .status-proses {
            color: #92400e;
            background: #fef3c7;
            border-color: #fde68a;
        }
        .status-selesai {
            color: #166534;
            background: #dcfce7;
            border-color: #bbf7d0;
        }
        .surat-group {
            margin: 6px 0 8px 0;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #ffffff;
        }
        .surat-head {
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .surat-title {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 2px 0;
        }
        .surat-meta {
            font-size: 9.8px;
            color: #475569;
            margin: 0;
            line-height: 1.35;
        }
        .detail-list {
            margin: 0;
            padding-left: 18px;
            font-size: 10.5px;
            color: #1f2937;
            list-style-type: decimal;
        }
        .detail-list li {
            margin-bottom: 2px;
            line-height: 1.35;
        }
        .empty-note {
            font-size: 10px;
            color: #64748b;
            margin: 0 0 5px 0;
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
                <div class="kop-address">Mulia, Puncak Jaya, Papua Tengah</div>
            </td>
            <td class="kop-logo-right">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo-inspektorat.png'))) }}" alt="Logo Inspektorat">
            </td>
        </tr>
    </table>
    <div class="kop-line-1"></div>
    <div class="kop-line-2"></div>

    <div class="report-header">
        <div class="title">{{ $judulLaporan }}</div>
        @if(isset($pemeriksaan) && $pemeriksaan)
            <div class="meta" style="font-weight:bold;color:#1e40af;">Pemeriksaan: {{ $pemeriksaan->nama }} ({{ $pemeriksaan->tahun }})</div>
        @endif
        <div class="meta">Tanggal Cetak: {{ $generatedAt->setTimezone('Asia/Jayapura')->format('d-m-Y H:i') }} WIT</div>
        @if(!empty($search))
            <div class="meta">Filter pencarian OPD: "{{ $search }}"</div>
        @endif
    </div>

    <div class="summary-box">
        <strong>Dengan hormat</strong>
        <div>Dalam rangka mendukung kelancaran pelaksanaan kegiatan pemeriksaan serta memastikan ketersediaan data yang dibutuhkan, dilakukan pemantauan terhadap pemenuhan permintaan dokumen kepada entitas terkait.

Ringkasan ini menyajikan dasar perhitungan progres atas permintaan data yang telah disampaikan. Perhitungan progres dilakukan berdasarkan nomor dan jenis surat permintaan yang dipilih sebagaimana tercantum di bawah ini:</div>
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
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 38px;">No</th>
                <th>Nama OPD</th>
                <th style="width: 70px;">Total</th>
                <th style="width: 70px;">Belum</th>
                <th style="width: 70px;">Proses</th>
                <th style="width: 70px;">Selesai</th>
                <th style="width: 90px;">Progress (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats as $i => $row)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="opd-name">{{ $row['opd'] }}</td>
                    <td class="text-center">{{ $row['total'] }}</td>
                    <td class="text-center">{{ $row['belum'] }}</td>
                    <td class="text-center">{{ $row['proses'] }}</td>
                    <td class="text-center">{{ $row['selesai'] }}</td>
                    <td class="text-right">{{ number_format((float) $row['progress'], 2, ',', '.') }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data monitoring untuk surat yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="detail-section">
            <div class="detail-header">
                <div class="detail-title">Catatan: </div>
                <div class="detail-subtitle">Mohon untuk segera melengkapi data-data yang diminta </div>
            </div>

    @if(!empty($showDetail))
       

            @php
                $statusClassMap = [
                    'selesai' => 'status-selesai',
                    'proses' => 'status-proses',
                    'belum' => 'status-belum',
                ];
            @endphp

            @forelse($detailByStatus as $opdGroup)
            
                <div class="opd-card new-page">
                    <div class="opd-card-header">{{ $opdGroup['opd'] }}</div>
                    <div class="opd-card-body">
                        @foreach(($opdGroup['statuses'] ?? []) as $st)
                            <div class="status-block">
                                <div class="status-title {{ $statusClassMap[$st['status_key']] ?? '' }}">
                                    {{ $st['status_label'] }}
                                </div>

                                @if(empty($st['surat_groups']))
                                    <p class="empty-note">Tidak ada data.</p>
                                @else
                                    @foreach($st['surat_groups'] as $sg)
                                        <div class="surat-group">
                                            <div class="surat-head">
                                                <p class="surat-title">Nomor Surat: {{ $sg['nomor_surat'] }}</p>
                                                <p class="surat-meta">Tanggal Surat: {{ $sg['tanggal_surat'] ?? '-' }}</p>
                                                <p class="surat-meta">Perihal: {{ $sg['perihal'] ?? '-' }}</p>
                                            </div>
                                            @if(empty($sg['items']))
                                                <p class="empty-note">Tidak ada list data.</p>
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

    <div class="bottom-space"></div>

    <div class="footer">
        Printed By <b>Inspectra</b> | Inspektorat Kab.Puncak Jaya.
    </div>
</body>
</html>
