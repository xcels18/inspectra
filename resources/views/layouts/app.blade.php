<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'INSPECTRA') - Sistem Pengelolaan Dokumen Pemeriksaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bs-font-sans-serif: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Geist', 'Segoe UI', Roboto, sans-serif;
            --bs-body-font-family: var(--bs-font-sans-serif);
            --bg-canvas: #f6f7f9;
            --sidebar-bg: #ffffff;
            --sidebar-border: #e5e7eb;
            --active-nav: #f1f5f9;
            --active-text: #0f172a;
        }

        body, input, button, select, textarea, .btn, .form-control, .form-select, h1, h2, h3, h4, h5, h6 { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Geist', 'Segoe UI', Roboto, sans-serif !important; 
        }

        body { 
            background-color: var(--bg-canvas); 
            font-size: 0.875rem; 
            color: #0f172a;
        }

        /* ── Sidebar Layout Bergaya Magnific AI ── */
        .sidebar { 
            min-height: 100vh; 
            background: var(--sidebar-bg); 
            width: 250px; 
            position: fixed; 
            top: 0; 
            left: 0; 
            z-index: 1000; 
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            padding: 1.25rem 0.85rem;
        }

        .sidebar .sidebar-brand { 
            padding: 0.5rem 0.75rem 1.25rem; 
            border-bottom: 1px solid #f1f5f9; 
            display: flex; 
            align-items: center; 
            gap: 12px;
        }

        .sidebar .sidebar-brand .brand-icon { 
            width: 40px; 
            height: 40px; 
            background: linear-gradient(135deg, #0b192c 0%, #1e40af 100%); 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            box-shadow: 0 4px 10px rgba(30,64,175,0.2); 
            color: #ffffff;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .sidebar .sidebar-brand h5 { 
            color: #0f172a; 
            font-weight: 800; 
            margin: 0; 
            font-size: 1.05rem; 
            letter-spacing: -0.02em; 
            line-height: 1.2;
        }

        .sidebar .sidebar-brand .sub-brand { 
            color: #64748b; 
            font-size: 0.7rem; 
            font-weight: 600; 
            margin-top: 1px; 
        }

        .sidebar .nav-label { 
            color: #94a3b8; 
            font-size: 0.68rem; 
            text-transform: uppercase; 
            letter-spacing: 0.08em; 
            padding: 1.25rem 0.75rem 0.4rem; 
            font-weight: 800; 
        }

        .sidebar .nav-link { 
            color: #64748b; 
            padding: 0.65rem 0.85rem; 
            border-radius: 10px; 
            margin: 0.15rem 0; 
            display: flex; 
            align-items: center; 
            gap: 0.75rem; 
            font-size: 0.84rem; 
            font-weight: 600;
            transition: all 0.15s ease-in-out; 
        }

        .sidebar .nav-link:hover { 
            background: #f8fafc; 
            color: #0f172a; 
        }

        .sidebar .nav-link.active { 
            background: var(--active-nav); 
            color: var(--active-text); 
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .sidebar .nav-link.active i {
            color: #2563eb;
        }

        /* ── Main Content Area ── */
        .main-content { 
            margin-left: 250px; 
            padding: 0; 
            min-height: 100vh; 
        }

        .topbar { 
            background: #ffffff; 
            border-bottom: 1px solid #e5e7eb; 
            padding: 0.85rem 1.75rem; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            position: sticky; 
            top: 0; 
            z-index: 999; 
        }

        .notif-bell { position: relative; cursor: pointer; }
        .notif-badge { 
            position: absolute; 
            top: -5px; 
            right: -6px; 
            background: #dc2626; 
            color: #fff; 
            font-size: 0.6rem; 
            font-weight: 700; 
            border-radius: 99px; 
            min-width: 16px; 
            height: 16px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 0 3px; 
        }

        .notif-dropdown { 
            position: absolute; 
            right: 0; 
            top: calc(100% + 8px); 
            width: 360px; 
            background: #fff; 
            border: 1px solid #e5e7eb; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            z-index: 1050; 
            display: none; 
        }
        .notif-dropdown.show { display: block; }
        .notif-item { padding: 0.65rem 1rem; border-bottom: 1px solid #f3f4f6; font-size: 0.78rem; transition: background 0.15s; }
        .notif-item:last-child { border-bottom: none; }
        .notif-item:hover { background: #f8fafc; }

        .page-content { padding: 1.5rem 1.75rem; }
        
        .card { border: 1px solid #e5e7eb; box-shadow: 0 2px 12px rgba(15, 23, 42, 0.03); border-radius: 16px; }
        .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; border-radius: 16px 16px 0 0 !important; font-weight: 700; padding: 1rem 1.25rem; }

        @media (max-width: 768px) { 
            .sidebar { transform: translateX(-100%); } 
            .main-content { margin-left: 0; } 
        }
    </style>
    @yield('styles')
</head>
<body>
    {{-- Sidebar Kiri --}}
    <div class="sidebar">
        <div class="sidebar-brand text-center d-flex flex-column align-items-center justify-content-center py-3 border-bottom">
            <div class="brand-icon mb-2" style="width: 52px; height: 52px; background: transparent; border-radius: 14px; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 2px;">
                <img src="/images/logo-inspektorat.png" alt="Logo Inspektorat" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div>
                <h5 class="fw-extrabold text-dark" style="font-size: 1.15rem; letter-spacing: -0.02em; margin: 0; line-height: 1.2;">INSPECTRA</h5>
                <div class="sub-brand mt-1" style="font-size: 0.72rem; color: #64748b; font-weight: 600;">Inspektorat Kab. Puncak Jaya</div>
            </div>
        </div>

        <nav class="mt-3 flex-grow-1 overflow-auto">
            <div class="nav-label">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="{{ route('pemeriksaan.index') }}" class="nav-link {{ request()->routeIs('pemeriksaan.*') ? 'active' : '' }}">
                <i class="bi bi-card-checklist"></i> Daftar Pemeriksaan
            </a>
            <a href="{{ route('surat.index') }}" class="nav-link {{ request()->routeIs('surat.*') ? 'active' : '' }}">
                <i class="bi bi-envelope-paper-fill"></i> Surat Permintaan
            </a>
            <a href="{{ route('opd.index') }}" class="nav-link {{ request()->routeIs('opd.*') ? 'active' : '' }}">
                <i class="bi bi-activity"></i> Monitoring OPD
            </a>
            <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <i class="bi bi-printer-fill"></i> Cetak Laporan
            </a>

            @if(auth()->user()->isAdmin())
            <div class="nav-label mt-3">Pengaturan System</div>
            <a href="{{ route('master-opd.index') }}" class="nav-link {{ request()->routeIs('master-opd.*') ? 'active' : '' }}">
                <i class="bi bi-buildings-fill"></i> Master OPD
            </a>
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i> Kelola Pengguna
            </a>
            <a href="{{ route('google-drive.index') }}" class="nav-link {{ request()->routeIs('google-drive.*') ? 'active' : '' }}">
                <i class="bi bi-google"></i> Google Drive Sync
            </a>
            <a href="{{ route('backup-dokumen.index') }}" class="nav-link {{ request()->routeIs('backup-dokumen.*') ? 'active' : '' }}">
                <i class="bi bi-archive-fill"></i> Backup Dokumen
            </a>
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i> Pengaturan
            </a>
            @endif
        </nav>

        {{-- Footer Sidebar Profile --}}
        <div class="pt-3 border-top mt-auto">
            <div class="p-2 rounded-3 d-flex align-items-center justify-content-between" style="background:#f8fafc; border:1px solid #e2e8f0;">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold"
                         style="width: 34px; height: 34px; min-width: 34px; background:linear-gradient(135deg, #0b192c 0%, #1e40af 100%); font-size:0.8rem;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.78rem;">{{ auth()->user()->name }}</div>
                        <div class="text-muted text-truncate" style="font-size: 0.68rem;">
                            @if(session('preview_role') === 'tim_bpk' && auth()->user()->role === 'admin')
                                <span class="badge bg-warning text-dark" style="font-size: 0.6rem;">Tim Pemeriksa</span>
                            @else
                                {{ auth()->user()->role_label }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="main-content">
        <div class="topbar">
            <div class="fw-bold text-dark" style="font-size:0.95rem;">@yield('page-title', 'Dashboard')</div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted" style="font-size:0.78rem;"><i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('l, d F Y') }}</span>

                {{-- Bell Notifikasi --}}
                <div class="notif-bell" id="notifBell">
                    <button class="btn btn-sm btn-light border position-relative" id="notifBtn" style="width:36px; height:34px; padding:0; border-radius:10px;">
                        <i class="bi bi-bell text-secondary" style="font-size:1rem;"></i>
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
                    <button type="submit" class="btn btn-sm {{ $isPreview ? 'btn-warning fw-bold' : 'btn-outline-secondary' }}"
                            style="border-radius:10px; font-size:0.78rem;"
                            title="{{ $isPreview ? 'Kembali ke Admin' : 'Lihat sebagai Tim Pemeriksa' }}">
                        <i class="bi bi-arrow-left-right me-1"></i>
                        {{ $isPreview ? 'Mode: Tim Pemeriksa' : 'Mode: Admin' }}
                    </button>
                </form>
                @endif

                <form action="{{ route('logout') }}" method="POST" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius:10px; font-size:0.78rem;">
                        <i class="bi bi-box-arrow-right me-1"></i>Keluar
                    </button>
                </form>
            </div>
        </div>

        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius:12px;">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius:12px;">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" style="border-radius:12px;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
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

    {{-- Global Document Live Preview Modal --}}
    <div class="modal fade" id="modalGlobalPreview" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header bg-dark text-white px-4 py-3 border-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2 overflow-hidden me-3">
                        <span id="previewFileIcon" class="fs-4 text-info"><i class="bi bi-file-earmark-text"></i></span>
                        <div class="text-truncate">
                            <h6 class="modal-title mb-0 fw-bold text-white text-truncate" id="previewFileName" style="font-size: 0.95rem;">Pratinjau Dokumen</h6>
                            <small class="text-muted" style="font-size: 0.72rem;">Live Preview PDF / Gambar / Dokumen</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a id="btnGlobalDownload" href="#" download class="btn btn-sm btn-success fw-bold px-3" style="border-radius: 8px; font-size: 0.78rem;">
                            <i class="bi bi-download me-1"></i>Unduh File
                        </a>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0 text-center bg-secondary-subtle d-flex align-items-center justify-content-center" style="min-height: 520px; position: relative;">
                    <div id="previewSpinner" class="spinner-border text-primary" role="status" style="display: none; position: absolute;">
                        <span class="visually-hidden">Loading...</span>
                    </div>

                    <iframe id="previewIframe" src="" style="width: 100%; height: 680px; border: 0; display: none;"></iframe>
                    <img id="previewImg" src="" alt="Pratinjau Gambar" style="max-width: 100%; max-height: 680px; object-fit: contain; display: none;" />

                    <div id="previewFallback" class="p-5 text-center" style="display: none;">
                        <div class="mb-3 text-warning"><i class="bi bi-file-earmark-x" style="font-size: 3.5rem;"></i></div>
                        <h6 class="fw-bold text-dark mb-1">Pratinjau Langsung Tidak Tersedia</h6>
                        <p class="text-muted mb-3" style="font-size: 0.82rem;">Format file ini tidak mendukung live preview browser. Silakan unduh file secara langsung.</p>
                        <a id="btnFallbackDownload" href="#" download class="btn btn-primary btn-sm px-4 fw-semibold" style="border-radius: 8px;">
                            <i class="bi bi-download me-1"></i>Unduh Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const notifBtn = document.getElementById('notifBtn');
        const notifBell = document.getElementById('notifBell');
        const notifDropdown = document.getElementById('notifDropdown');
        const notifCount = document.getElementById('notifCount');
        const notifList = document.getElementById('notifList');
        const btnMarkRead = document.getElementById('btnMarkRead');

        function fetchNotif() {
            fetch("{{ route('notifikasi.index') }}")
                .then(r => r.json())
                .then(data => {
                    if (data.unread_count > 0) {
                        notifCount.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                        notifCount.style.display = 'flex';
                    } else {
                        notifCount.style.display = 'none';
                    }

                    if (data.notifikasi.length === 0) {
                        notifList.innerHTML = `<div class="text-center text-muted py-4" style="font-size:0.78rem;">
                            <i class="bi bi-bell-slash" style="font-size:1.5rem; display:block; margin-bottom:4px;"></i>
                            Tidak ada notifikasi baru
                        </div>`;
                    } else {
                        let html = '';
                        data.notifikasi.forEach(item => {
                            html += `<div class="notif-item ${item.is_read ? '' : 'bg-light'}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span class="fw-semibold text-primary" style="font-size:0.78rem;">${item.judul_permintaan}</span>
                                    <span class="text-muted" style="font-size:0.65rem;">${item.created_at}</span>
                                </div>
                                <div class="text-dark mt-1" style="font-size:0.75rem;">
                                    <strong>${item.opd}</strong> mengunggah file <em>${item.nama_file}</em>
                                </div>
                                <div class="text-muted mt-1" style="font-size:0.68rem;">Surat: ${item.nomor_surat}</div>
                            </div>`;
                        });
                        notifList.innerHTML = html;
                    }
                })
                .catch(e => console.error(e));
        }

        if (notifBtn) {
            notifBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                notifDropdown.classList.toggle('show');
            });
            document.addEventListener('click', function(e) {
                if (!notifBell.contains(e.target)) {
                    notifDropdown.classList.remove('show');
                }
            });
            fetchNotif();
            setInterval(fetchNotif, 30000);
        }

        if (btnMarkRead) {
            btnMarkRead.addEventListener('click', function() {
                fetch("{{ route('notifikasi.mark-read') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => fetchNotif());
            });
        }
    });

    function openGlobalPreview(previewUrl, downloadUrl, fileName) {
        const modal = new bootstrap.Modal(document.getElementById('modalGlobalPreview'));
        const titleEl = document.getElementById('previewFileName');
        const iconEl = document.getElementById('previewFileIcon');
        const btnDownload = document.getElementById('btnGlobalDownload');
        const btnFallback = document.getElementById('btnFallbackDownload');
        const iframe = document.getElementById('previewIframe');
        const img = document.getElementById('previewImg');
        const fallback = document.getElementById('previewFallback');
        const spinner = document.getElementById('previewSpinner');

        titleEl.textContent = fileName || 'Pratinjau Dokumen';
        btnDownload.href = downloadUrl;
        btnFallback.href = downloadUrl;

        const ext = (fileName || '').split('.').pop().toLowerCase();

        iframe.style.display = 'none';
        img.style.display = 'none';
        fallback.style.display = 'none';
        spinner.style.display = 'block';

        if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(ext)) {
            iconEl.innerHTML = '<i class="bi bi-file-earmark-image text-success"></i>';
            img.src = previewUrl;
            img.onload = function() {
                spinner.style.display = 'none';
                img.style.display = 'block';
            };
            img.onerror = function() {
                spinner.style.display = 'none';
                fallback.style.display = 'block';
            };
        } else if (['pdf', 'txt'].includes(ext)) {
            iconEl.innerHTML = '<i class="bi bi-file-earmark-pdf text-danger"></i>';
            iframe.src = previewUrl;
            iframe.onload = function() {
                spinner.style.display = 'none';
                iframe.style.display = 'block';
            };
            iframe.onerror = function() {
                spinner.style.display = 'none';
                fallback.style.display = 'block';
            };
        } else {
            iconEl.innerHTML = '<i class="bi bi-file-earmark-word text-primary"></i>';
            spinner.style.display = 'none';
            fallback.style.display = 'block';
        }

        modal.show();
    }
    </script>
    @yield('scripts')
</body>
</html>
