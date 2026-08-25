<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Dokumen</title>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; color: #1f2937; margin: 0; padding: 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); max-width: 400px; width: 100%; border-top: 5px solid #10b981; }
        .icon { font-size: 48px; color: #10b981; margin-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; margin: 10px 0; color: #111827; }
        .desc { font-size: 14px; color: #4b5563; margin-bottom: 20px; line-height: 1.5; }
        .kode { font-family: monospace; background: #f3f4f6; padding: 8px 12px; border-radius: 6px; font-size: 16px; font-weight: bold; letter-spacing: 1px; color: #374151; }
        .footer { margin-top: 30px; font-size: 12px; color: #9ca3af; }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="card">
        <div class="icon">✓</div>
        <div class="title">Dokumen Valid</div>
        <div class="desc">
            Dokumen ini telah ditandatangani secara elektronik dan diterbitkan secara resmi oleh <strong>Inspektorat Kabupaten Puncak Jaya</strong> melalui Sistem Informasi <strong>Inspectra</strong>.
        </div>
        @if(isset($kode))
            <div class="kode">{{ $kode }}</div>
        @endif
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Inspektorat Kabupaten Puncak Jaya
    </div>
</body>
</html>
