<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspectra &mdash; Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            display: flex;
            overflow: hidden;
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
            gap: 12px;
        }

        .brand-logo .icon {
            width: 42px;
            height: 42px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #fff;
            backdrop-filter: blur(8px);
        }

        .brand-logo .name {
            font-size: 1.1rem;
            font-weight: 700;
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
            width: 460px;
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
            color: #6b7280;
            margin-bottom: 2rem;
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
            border: 1.5px solid #e5e7eb;
            border-radius: 0.6rem;
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
            background: linear-gradient(135deg, #1a3a6b 0%, #2563eb 100%);
            border: none;
            border-radius: 0.65rem;
            padding: 0.65rem;
            font-size: 0.88rem;
            font-weight: 600;
            color: #fff;
            width: 100%;
            transition: opacity 0.15s, transform 0.1s;
            letter-spacing: 0.01em;
        }

        .btn-login:hover {
            opacity: 0.92;
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-login:active { transform: translateY(0); }

        .divider {
            height: 1px;
            background: #f3f4f6;
            margin: 1.5rem 0;
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
            color: #6b7280;
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
            <img src="/images/logo-puncak-jaya.png" alt="Logo Kab. Puncak Jaya" style="height:40px; width:auto; filter:brightness(0) invert(1);">
            <img src="/images/logo-berakhlak.png" alt="Logo BerAKHLAK" style="height:28px; width:auto; filter:brightness(0) invert(1);">
            <div>
                <div class="name">Inspectra</div>
                <div class="tagline">Sistem Informasi Manajemen Bahan Pemeriksaan</div>
            </div>
        </div>

        <div class="brand-copy">
            <h1>Kelola Dokumen <span>Pemeriksaan</span> dengan Tepat & Terstruktur</h1>
            <p>Platform terpadu untuk manajemen permintaan data BPK, monitoring OPD, dan pengumpulan dokumen secara real-time.</p>
        </div>

        <div class="brand-footer">
            &copy; {{ date('Y') }} Pemerintah Kabupaten Puncak Jaya &mdash; Sistem Internal
        </div>
    </div>

    {{-- Form login kanan --}}
    <div class="login-panel">
        <div class="login-box">
            {{-- Logo --}}
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:2rem;">
                <img src="/images/logo-puncak-jaya.png" alt="Logo Kab. Puncak Jaya" style="height:42px; width:auto; object-fit:contain;">
                <div style="width:1px; height:36px; background:#e5e7eb;"></div>
                <img src="/images/logo-berakhlak.png" alt="Logo BerAKHLAK" style="height:32px; width:auto; object-fit:contain;">
                <div style="margin-left:4px;">
                    <div style="font-size:0.85rem;font-weight:700;color:#0f172a;line-height:1.2;">Inspectra</div>
                    <div style="font-size:0.62rem;color:#9ca3af;line-height:1.3;">Manajemen Bahan Pemeriksaan</div>
                </div>
            </div>

            <div class="welcome">Selamat Datang</div>
            <div class="sub">Masuk ke akun Anda untuk melanjutkan</div>

            @if($errors->any())
            <div class="alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email">Email</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" id="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="email@domain.com"
                            required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password">Password</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="••••••••"
                            required>
                    </div>
                </div>

                <div class="remember-row">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>

            <div class="divider"></div>

            <div style="font-size:0.72rem; color:#9ca3af; text-align:center;">
                <i class="bi bi-shield-lock me-1"></i>Akses terbatas untuk pengguna yang berwenang
            </div>

            <div style="font-size:0.68rem; color:#d1d5db; text-align:center; margin-top:2rem;">
                &copy; {{ date('Y') }} Pemerintah Kabupaten Puncak Jaya
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
