<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BPK Dokumen') - Sistem Pengelolaan Dokumen BPK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif; font-size: 0.875rem; }
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #0b192c 0%, #1a365d 100%); width: 250px; position: fixed; top: 0; left: 0; z-index: 1000; box-shadow: 2px 0 15px rgba(0,0,0,0.08); }
        .sidebar::before { content: ""; position: absolute; top: 0; right: 0; bottom: 0; left: 0; background: radial-gradient(circle at top right, rgba(255,255,255,0.06), transparent 60%); pointer-events: none; z-index: 0; }
        .sidebar .sidebar-brand, .sidebar nav, .sidebar .mt-auto { position: relative; z-index: 1; }
        .sidebar .sidebar-brand { padding: 1.4rem 1rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.08); display: flex; flex-direction: column; align-items: center; text-align: center; }
        .sidebar .sidebar-brand .brand-icon { width: 60px; height: 60px; background: rgba(255,255,255,0.08); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.7rem; box-shadow: 0 4px 10px rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,0.12); overflow: hidden; padding: 6px; }
        .sidebar .sidebar-brand h5 { color: #fff; font-weight: 800; margin: 0; font-size: 1.15rem; letter-spacing: 1.5px; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .sidebar .sidebar-brand .sub-brand { color: #bae6fd; font-size: 0.75rem; font-weight: 600; margin-top: 2px; }
        .sidebar .sidebar-brand p { color: rgba(255,255,255,0.5); margin: 0; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 0.5rem; }
        .sidebar .nav-link { color: rgba(255,255,255,0.75); padding: 0.65rem 1.25rem; border-radius: 0.5rem; margin: 0.1rem 0.75rem; display: flex; align-items: center; gap: 0.6rem; font-size: 0.875rem; transition: all 0.2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.12); color: #fff; box-shadow: inset 3px 0 0 #38bdf8; }
        .sidebar .nav-label { color: rgba(255,255,255,0.35); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; padding: 1.2rem 1.25rem 0.4rem; font-weight: 700; }
        .main-content { margin-left: 250px; padding: 0; min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e0e5ec; padding: 0.75rem 1.5rem; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 999; box-shadow: 0 1px 4px rgba(0,0,0,0.05); }
        .notif-bell { position: relative; cursor: pointer; }
        .notif-badge { position: absolute; top: -5px; right: -6px; background: #dc2626; color: #fff; font-size: 0.6rem; font-weight: 700; border-radius: 99px; min-width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; padding: 0 3px; }
        .notif-dropdown { position: absolute; right: 0; top: calc(100% + 8px); width: 360px; background: #fff; border: 1px solid #e5e7eb; border-radius: 0.75rem; box-shadow: 0 8px 24px rgba(0,0,0,0.12); z-index: 1050; display: none; }
        .notif-dropdown.show { display: block; }
        .notif-item { padding: 0.65rem 1rem; border-bottom: 1px solid #f3f4f6; font-size: 0.78rem; transition: background 0.15s; }
        .notif-item:last-child { border-bottom: none; }
        .notif-item:hover { background: #f8fafc; }
        .page-content { padding: 1.5rem; }
        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,0.06); border-radius: 0.75rem; }
        .card-header { background: #fff; border-bottom: 1px solid #e9ecef; border-radius: 0.75rem 0.75rem 0 0 !important; font-weight: 600; padding: 1rem 1.25rem; }
        .stat-card { border-radius: 0.75rem; padding: 1.25rem; color: #fff; }
        .badge-belum { background: #dc3545; }
        .badge-proses { background: #fd7e14; }
        .badge-selesai { background: #198754; }
        .table th { font-size: 0.8rem; text-transform: uppercase; color: #6c757d; font-weight: 600; border-top: none; }
        .progress { height: 8px; border-radius: 4px; }
        .btn-sm { font-size: 0.8rem; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } }
    </style>
    @yield('styles')
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <img src="/images/logo-inspektorat.png" alt="Logo Inspektorat" style="width:100%; height:100%; object-fit:contain;">
            </div>
            <h5>INSPECTRA</h5>
            <div class="sub-brand">Kabupaten Puncak Jaya</div>
            <p>Manajemen Bahan Pemeriksaan</p>
        </div>
        <nav class="mt-2">
            <div class="nav-label">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('pemeriksaan.index') }}" class="nav-link {{ request()->routeIs('pemeriksaan.*') ? 'active' : '' }}">
                <i class="bi bi-folder2-open"></i> Daftar Pemeriksaan
            </a>
            <a href="{{ route('surat.index') }}" class="nav-link {{ request()->routeIs('surat.*') ? 'active' : '' }}">
                <i class="bi bi-envelope-paper"></i> Surat Permintaan
            </a>
            <a href="{{ route('opd.index') }}" class="nav-link {{ request()->routeIs('opd.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Monitoring OPD
            </a>
            @if(auth()->user()->isAdmin())
            <div class="nav-label mt-3">Pengaturan</div>
            <a href="{{ route('master-opd.index') }}" class="nav-link {{ request()->routeIs('master-opd.*') ? 'active' : '' }}">
                <i class="bi bi-building-add"></i> Master OPD
            </a>
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Pengguna
            </a>
            <a href="{{ route('google-drive.index') }}" class="nav-link {{ request()->routeIs('google-drive.*') ? 'active' : '' }}">
                <i class="bi bi-cloud-arrow-up"></i> Google Drive Sync
            </a>
            <a href="{{ route('backup-dokumen.index') }}" class="nav-link {{ request()->routeIs('backup-dokumen.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-zip"></i> Backup Dokumen
            </a>
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Pengaturan
            </a>
            @endif
        </nav>
        <div class="mt-auto p-3" style="position: absolute; bottom: 0; width: 100%;">
            <div class="p-2 rounded" style="background: rgba(255,255,255,0.1);">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; min-width: 32px;">
                        <i class="bi bi-person-fill" style="font-size: 1rem;"></i>
                    </div>
                    <div>
                        <div class="text-white fw-semibold" style="font-size: 0.8rem;">{{ auth()->user()->name }}</div>
                        <div class="text-white-50" style="font-size: 0.7rem;">
                            @if(session('preview_role') === 'tim_bpk' && auth()->user()->role === 'admin')
                                <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Preview Tim BPK</span>
                            @else
                                {{ auth()->user()->role_label }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="fw-semibold text-dark">@yield('page-title', 'Dashboard')</div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">{{ now()->translatedFormat('l, d F Y') }}</span>

                {{-- Bell Notifikasi --}}
                <div class="notif-bell" id="notifBell">
                    <button class="btn btn-sm btn-outline-secondary position-relative" id="notifBtn" style="width:36px; height:32px; padding:0;">
                        <i class="bi bi-bell" style="font-size:1rem;"></i>
                        <span class="notif-badge" id="notifCount" style="display:none;">0</span>
                    </button>
                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                            <span class="fw-semibold" style="font-size:0.82rem;"><i class="bi bi-bell me-1"></i>Dokumen Baru Masuk</span>
                            <button class="btn btn-xs btn-link text-muted p-0" style="font-size:0.72rem;" id="btnMarkRead">Tandai semua dibaca</button>
                        </div>
                        <div id="notifList" style="max-height: 340px; overflow-y: auto;">
                            <div class="text-center text-muted py-4" style="font-size:0.78rem;">
                                <i class="bi bi-bell-slash" style="font-size:1.5rem; display:block; margin-bottom:4px;"></i>
                                Tidak ada notifikasi baru
                            </div>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->role === 'admin')
                <form action="{{ route('switch-role') }}" method="POST" class="mb-0">
                    @csrf
                    @php $isPreview = session('preview_role') === 'tim_bpk'; @endphp
                    <button type="submit" class="btn btn-sm {{ $isPreview ? 'btn-warning' : 'btn-outline-secondary' }}" title="{{ $isPreview ? 'Kembali ke Admin' : 'Lihat sebagai Tim BPK' }}">
                        <i class="bi bi-arrow-left-right me-1"></i>
                        {{ $isPreview ? 'Mode: Tim BPK' : 'Mode: Admin' }}
                    </button>
                </form>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </div>
        </div>

        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function() {
        const btn = document.getElementById('notifBtn');
        const dropdown = document.getElementById('notifDropdown');
        const countEl = document.getElementById('notifCount');
        const listEl = document.getElementById('notifList');
        const markReadBtn = document.getElementById('btnMarkRead');

        function fetchNotif() {
            fetch('{{ route('notifikasi.index') }}')
                .then(r => r.json())
                .then(data => {
                    if (data.count > 0) {
                        countEl.textContent = data.count > 99 ? '99+' : data.count;
                        countEl.style.display = 'flex';
                    } else {
                        countEl.style.display = 'none';
                    }

                    if (data.items.length === 0) {
                        listEl.innerHTML = '<div class="text-center text-muted py-4" style="font-size:0.78rem;"><i class="bi bi-bell-slash" style="font-size:1.5rem; display:block; margin-bottom:4px;"></i>Tidak ada notifikasi baru</div>';
                        return;
                    }

                    listEl.innerHTML = data.items.map(function(item) {
                        const url = item.opd_url || '#';
                        return '<a href="' + url + '" class="notif-item d-block text-decoration-none text-dark">' +
                            '<div class="d-flex gap-2 align-items-start">' +
                            '<div class="flex-shrink-0 mt-1"><i class="bi bi-file-earmark-arrow-up text-primary" style="font-size:1rem;"></i></div>' +
                            '<div>' +
                            '<div class="fw-semibold" style="font-size:0.78rem;">' + item.opd + '</div>' +
                            '<div class="text-muted" style="font-size:0.72rem;">Upload: ' + item.nama_file + '</div>' +
                            '<div class="text-muted" style="font-size:0.7rem;">' + item.surat + ' &bull; ' + item.waktu + '</div>' +
                            '</div></div></a>';
                    }).join('');
                });
        }

        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
            if (dropdown.classList.contains('show')) fetchNotif();
        });

        document.addEventListener('click', function(e) {
            if (!document.getElementById('notifBell').contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        markReadBtn.addEventListener('click', function() {
            fetch('{{ route('notifikasi.mark-read') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
            }).then(() => {
                countEl.style.display = 'none';
                listEl.innerHTML = '<div class="text-center text-muted py-4" style="font-size:0.78rem;"><i class="bi bi-bell-slash" style="font-size:1.5rem; display:block; margin-bottom:4px;"></i>Tidak ada notifikasi baru</div>';
            });
        });

        fetchNotif();
        setInterval(fetchNotif, 30000);
    })();
    </script>
    @yield('scripts')
</body>
</html>
