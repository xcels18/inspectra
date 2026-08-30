<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspectra &mdash; Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@900,800,700,600,500,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Satoshi', 'Inter', 'Plus Jakarta Sans', sans-serif !important;
            display: flex;
            overflow: hidden;
        }

        input, button, select, textarea {
            font-family: 'Satoshi', 'Inter', 'Plus Jakarta Sans', sans-serif !important;
        }

        .bg-panel {
            position: fixed;
            inset: 0;
            background-image: url('/images/login-bg.jpg');
            background-size: cover;
            background-position: center;
            background-color: #0d1b3e;
        }

        .bg-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                100deg,
                rgba(10, 20, 55, 0.88) 0%,
                rgba(10, 20, 55, 0.6) 55%,
                rgba(10, 20, 55, 0.15) 100%
            );
        }

        .layout {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            width: 100%;
            display: flex;
        }

        /* Left branding panel */
        .brand-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem 3.5rem;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        
        .brand-logo img.logo-inspektorat {
            height: 52px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,0.35));
        }
        
        .brand-logo .name {
            font-size: 1.1rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.01em;
        }
        
        .brand-logo .tagline {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.55);
            letter-spacing: 0.03em;
        }

        .brand-copy {
            max-width: 480px;
        }

        .brand-copy h1 {
            font-size: 2.6rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin-bottom: 1rem;
        }

        .brand-copy h1 span {
            color: #60a5fa;
        }

        .brand-copy p {
            font-size: 0.92rem;
            color: rgba(255,255,255,0.6);
            line-height: 1.7;
            max-width: 380px;
        }

        .brand-footer {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.35);
        }

        /* Right login panel */
        .login-panel {
            width: 470px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 2.5rem;
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(12px);
        }

        .login-box {
            width: 100%;
        }

        .login-box .welcome {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .login-box .sub {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 1.25rem;
        }

        .login-box label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
            display: block;
        }

        .login-box .form-control {
            font-size: 0.85rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.65rem;
            padding: 0.6rem 0.85rem;
            color: #111827;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .login-box .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
            outline: none;
        }

        .input-icon-wrap {
            position: relative;
        }

        .input-icon-wrap .bi {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.9rem;
            pointer-events: none;
        }

        .input-icon-wrap .form-control {
            padding-left: 2.1rem;
        }

        .btn-login {
            background: linear-gradient(135deg, #0b192c 0%, #1e40af 100%);
            border: none;
            border-radius: 0.65rem;
            padding: 0.65rem;
            font-size: 0.88rem;
            font-weight: 700;
            color: #fff;
            width: 100%;
            transition: opacity 0.15s, transform 0.1s;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 12px rgba(30,64,175,0.25);
        }

        .btn-login:hover {
            opacity: 0.94;
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-login:active { transform: translateY(0); }

        .divider {
            height: 1px;
            background: #f1f5f9;
            margin: 1.25rem 0;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.25rem;
        }

        .remember-row input[type=checkbox] {
            width: 15px;
            height: 15px;
            accent-color: #2563eb;
            cursor: pointer;
        }

        .remember-row label {
            font-size: 0.78rem;
            color: #64748b;
            margin: 0;
            cursor: pointer;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 0.6rem;
            padding: 0.65rem 0.9rem;
            font-size: 0.78rem;
            color: #dc2626;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── Modern 2-Tab Navigation ── */
        .login-nav-tabs .nav-link {
            color: #64748b;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .login-nav-tabs .nav-link.active {
            background: #ffffff !important;
            color: #0f172a !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
        }

        @media (max-width: 768px) {
            .brand-panel { display: none; }
            .login-panel { width: 100%; padding: 2rem; }
        }
    </style>
</head>
<body>

<div class="bg-panel"></div>

<div class="layout">
    {{-- Branding kiri --}}
    <div class="brand-panel">
        <div class="brand-logo">
            <img src="/images/logo-puncak-jaya.png" alt="Logo Kab. Puncak Jaya" class="logo-inspektorat">
            <img src="/images/logo-inspektorat.png" alt="Logo Inspektorat" class="logo-inspektorat">
            <div>
                <div class="name">INSPECTRA</div>
                <div class="tagline">Inspektorat &mdash; Kab. Puncak Jaya</div>
            </div>
        </div>

        <div class="brand-copy">
            <h1>Kelola Dokumen <span>Pemeriksaan</span> dengan Tepat & Terstruktur</h1>
            <p>Platform terpadu untuk manajemen permintaan data pemeriksaan, monitoring OPD, dan pengumpulan dokumen secara real-time.</p>
        </div>

        <div class="brand-footer">
            &copy; {{ date('Y') }} Pemerintah Kabupaten Puncak Jaya &mdash; Sistem Internal
        </div>
    </div>

    {{-- Form login kanan --}}
    <div class="login-panel">
        <div class="login-box">
            {{-- Logo --}}
            <div style="display:flex; align-items:center; gap:14px; margin-bottom:1.5rem;">
                <img src="/images/logo-puncak-jaya.png" alt="Logo Kab. Puncak Jaya" style="height:50px; width:auto; object-fit:contain;">
                <div style="width:1px; height:40px; background:#e2e8f0;"></div>
                <img src="/images/logo-inspektorat.png" alt="Logo Inspektorat" style="height:50px; width:auto; object-fit:contain;">
                <div style="width:1px; height:40px; background:#e2e8f0;"></div>
                <div>
                    <div style="font-size:1rem;font-weight:800;color:#0b192c;line-height:1.2;letter-spacing:-0.01em;">INSPECTRA</div>
                    <div style="font-size:0.68rem;color:#64748b;line-height:1.4;margin-top:2px;">Inspektorat Kab. Puncak Jaya</div>
                </div>
            </div>

            <div class="welcome">Selamat Datang</div>
            <div class="sub">Silahkan pilih metode masuk yang Anda inginkan</div>

            @if($errors->any())
            <div class="alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ $errors->first() }}
            </div>
            @endif

            {{-- Modern 2-Tab Navigation Switcher --}}
            <ul class="nav nav-pills nav-fill mb-3 p-1 rounded-3 login-nav-tabs" id="loginTab" role="tablist" style="background:#f1f5f9; border:1px solid #e2e8f0;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold py-2" id="tab-email-tab" data-bs-toggle="pill" data-bs-target="#tab-email" type="button" role="tab" style="font-size:0.78rem;">
                        <i class="bi bi-key-fill me-1 text-primary"></i>1. Email & Password
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold py-2" id="tab-quick-tab" data-bs-toggle="pill" data-bs-target="#tab-quick" type="button" role="tab" style="font-size:0.78rem;">
                        <i class="bi bi-lightning-charge-fill me-1 text-warning"></i>2. Login Cepat / PIN
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="loginTabContent">

                {{-- TAB 1: FORMAL EMAIL & PASSWORD --}}
                <div class="tab-pane fade show active" id="tab-email" role="tabpanel" aria-labelledby="tab-email-tab">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email">Email Pengguna</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-envelope"></i>
                                <input type="email" id="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    placeholder="email@domain.com"
                                    autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password">Password</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-lock"></i>
                                <input type="password" id="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <div class="remember-row">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">Ingat saya di perangkat ini</label>
                        </div>

                        <button type="submit" class="btn-login">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk Akun
                        </button>
                    </form>
                </div>

                {{-- TAB 2: QUICK LOGIN (PIN) --}}
                <div class="tab-pane fade" id="tab-quick" role="tabpanel" aria-labelledby="tab-quick-tab">
                    <div class="p-3 rounded-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                        <div class="fw-bold text-dark mb-1" style="font-size:0.82rem;">
                            <i class="bi bi-shield-check text-primary me-1"></i>Masuk Menggunakan PIN Cepat
                        </div>
                        <div class="text-muted mb-3" style="font-size:0.72rem;">Masukkan PIN 6-digit di bawah ini untuk langsung masuk.</div>

                        <form method="POST" action="{{ route('quick-login') }}">
                            @csrf
                            <label for="pin_code">PIN Akses Cepat</label>
                            <div class="input-group">
                                <input type="password" id="pin_code" name="pin" class="form-control text-center fw-bold letter-spacing-2" placeholder="••••••" maxlength="6" style="font-size:1.1rem; letter-spacing:4px;" autofocus>
                                <button type="submit" class="btn btn-primary fw-bold px-3" style="font-size:0.82rem;">
                                    <i class="bi bi-arrow-right-circle-fill me-1"></i>Masuk
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <div class="divider"></div>

            <div style="font-size:0.72rem; color:#9ca3af; text-align:center;">
                <i class="bi bi-shield-lock me-1"></i>Akses terbatas untuk pengguna yang berwenang
            </div>

            <div style="font-size:0.68rem; color:#d1d5db; text-align:center; margin-top:1.5rem;">
                &copy; {{ date('Y') }} Pemerintah Kabupaten Puncak Jaya
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
