<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspectra &mdash; Login Sistem Pemeriksaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Geist', 'Segoe UI', Roboto, sans-serif !important;
            background: linear-gradient(135deg, #090d16 0%, #0f172a 40%, #1e1b4b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 1.5rem;
        }

        input, button, select, textarea {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Geist', 'Segoe UI', Roboto, sans-serif !important;
        }

        /* Ambient Glowing Shapes */
        .glow-shape-1 {
            position: fixed;
            top: -100px;
            left: -100px;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.25) 0%, rgba(37, 99, 235, 0) 70%);
            filter: blur(50px);
            pointer-events: none;
            z-index: 0;
        }

        .glow-shape-2 {
            position: fixed;
            bottom: -120px;
            right: -100px;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.2) 0%, rgba(168, 85, 247, 0) 70%);
            filter: blur(60px);
            pointer-events: none;
            z-index: 0;
        }

        .neuron-bg-svg {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            opacity: 0.35;
        }

        /* Central Login Wrapper */
        .login-card-container {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 10;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.2);
            padding: 2.5rem 2.25rem;
            position: relative;
            overflow: hidden;
        }

        .login-header {
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .logos-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            padding: 10px 18px;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
            margin-bottom: 1.25rem;
        }

        .logos-wrapper img {
            height: 42px;
            width: auto;
            object-fit: contain;
        }

        .logo-divider {
            width: 1px;
            height: 32px;
            background: #e2e8f0;
        }

        .brand-title {
            font-size: 1.5rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.03em;
            line-height: 1.1;
            margin-bottom: 0.25rem;
        }

        .brand-subtitle {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Modern Segmented Navigation Tabs */
        .login-tabs {
            background: #f1f5f9;
            padding: 4px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }

        .login-tabs .nav-link {
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
            border-radius: 10px;
            padding: 8px 12px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .login-tabs .nav-link.active {
            background: #ffffff !important;
            color: #0f172a !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08) !important;
        }

        /* Form Inputs */
        .form-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.4rem;
        }

        .input-icon-wrap {
            position: relative;
        }

        .input-icon-wrap .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .form-control-custom {
            width: 100%;
            height: 46px;
            padding-left: 2.75rem;
            padding-right: 1rem;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background: #ffffff;
            font-size: 0.875rem;
            font-weight: 500;
            color: #0f172a;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
            outline: none;
        }

        .form-control-custom:focus ~ .input-icon {
            color: #2563eb;
        }

        /* Primary Button */
        .btn-submit-gradient {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 0.5rem;
        }

        .btn-submit-gradient:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
            color: #ffffff;
        }

        .btn-submit-gradient:active {
            transform: translateY(0);
        }

        /* PIN Card */
        .pin-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.25rem;
        }

        .pin-input {
            height: 50px;
            font-size: 1.25rem;
            letter-spacing: 8px;
            text-align: center;
            border-radius: 12px;
            border: 1.5px solid #cbd5e1;
            font-weight: 800;
            background: #ffffff;
        }

        .pin-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
            outline: none;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            color: #dc2626;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .remember-row label {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }

        .remember-row input[type=checkbox] {
            width: 16px;
            height: 16px;
            accent-color: #2563eb;
            cursor: pointer;
            border-radius: 4px;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.75rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f1f5f9;
        }

        .login-footer .system-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 600;
            background: #f1f5f9;
            padding: 4px 12px;
            border-radius: 99px;
            margin-bottom: 0.5rem;
        }

        .login-footer .copyright {
            font-size: 0.7rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>

{{-- Ambient Glowing Background Orbs --}}
<div class="glow-shape-1"></div>
<div class="glow-shape-2"></div>

{{-- SVG Neuron Network Connection Lines --}}
<svg class="neuron-bg-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 600" preserveAspectRatio="none">
    <defs>
        <linearGradient id="loginGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.3" />
            <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0.1" />
        </linearGradient>
    </defs>
    <path d="M 100,100 L 300,200 L 550,120 L 780,260 L 920,150" stroke="url(#loginGrad1)" stroke-width="1.5" fill="none" opacity="0.4" />
    <path d="M 80,450 L 280,380 L 500,480 L 750,390 L 900,490" stroke="url(#loginGrad1)" stroke-width="1.5" fill="none" opacity="0.4" />
    <line x1="300" y1="200" x2="280" y2="380" stroke="#3b82f6" stroke-width="1" opacity="0.2" stroke-dasharray="4,4" />
    <line x1="550" y1="120" x2="500" y2="480" stroke="#8b5cf6" stroke-width="1" opacity="0.2" />
    <line x1="780" y1="260" x2="750" y2="390" stroke="#3b82f6" stroke-width="1" opacity="0.2" stroke-dasharray="4,4" />
    <circle cx="300" cy="200" r="5" fill="#3b82f6" opacity="0.6" />
    <circle cx="550" cy="120" r="6" fill="#8b5cf6" opacity="0.7" />
    <circle cx="780" cy="260" r="5" fill="#10b981" opacity="0.6" />
    <circle cx="280" cy="380" r="5" fill="#0284c7" opacity="0.6" />
    <circle cx="500" cy="480" r="6" fill="#3b82f6" opacity="0.7" />
    <circle cx="750" cy="390" r="5" fill="#8b5cf6" opacity="0.6" />
</svg>

{{-- Main Central Login Card Container --}}
<div class="login-card-container">
    <div class="login-card">
        
        {{-- Header & Branding Logos --}}
        <div class="login-header">
            <div class="logos-wrapper">
                <img src="/images/logo-puncak-jaya.png" alt="Logo Kab. Puncak Jaya">
                <div class="logo-divider"></div>
                <img src="/images/logo-inspektorat.png" alt="Logo Inspektorat">
            </div>

            <h1 class="brand-title">INSPECTRA</h1>
            <div class="brand-subtitle">Inspektorat Kabupaten Puncak Jaya</div>
        </div>

        {{-- Error Notification Alert --}}
        @if($errors->any())
        <div class="alert-error">
            <i class="bi bi-exclamation-circle-fill fs-6"></i>
            <div>{{ $errors->first() }}</div>
        </div>
        @endif

        {{-- 2-Tab Segmented Switcher --}}
        <ul class="nav nav-pills nav-fill login-tabs" id="loginTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-email-tab" data-bs-toggle="pill" data-bs-target="#tab-email" type="button" role="tab">
                    <i class="bi bi-key-fill me-1 text-primary"></i>Email & Password
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-quick-tab" data-bs-toggle="pill" data-bs-target="#tab-quick" type="button" role="tab">
                    <i class="bi bi-lightning-charge-fill me-1 text-warning"></i>Login PIN Cepat
                </button>
            </li>
        </ul>

        <div class="tab-content" id="loginTabContent">

            {{-- TAB 1: FORMAL EMAIL & PASSWORD --}}
            <div class="tab-pane fade show active" id="tab-email" role="tabpanel" aria-labelledby="tab-email-tab">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Pengguna</label>
                        <div class="input-icon-wrap">
                            <input type="email" id="email" name="email"
                                class="form-control-custom @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="nama@puncakjayakab.go.id"
                                autofocus required>
                            <i class="bi bi-envelope-fill input-icon"></i>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <div class="input-icon-wrap">
                            <input type="password" id="password" name="password"
                                class="form-control-custom @error('password') is-invalid @enderror"
                                placeholder="••••••••" required>
                            <i class="bi bi-lock-fill input-icon"></i>
                        </div>
                    </div>

                    <div class="remember-row">
                        <div class="d-flex align-items-center gap-2">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">Ingat perangkat ini</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit-gradient">
                        <i class="bi bi-box-arrow-in-right fs-5"></i>
                        <span>Masuk ke Sistem</span>
                    </button>
                </form>
            </div>

            {{-- TAB 2: QUICK LOGIN (PIN) --}}
            <div class="tab-pane fade" id="tab-quick" role="tabpanel" aria-labelledby="tab-quick-tab">
                <div class="pin-box">
                    <div class="fw-bold text-dark mb-1 d-flex align-items-center gap-2" style="font-size:0.85rem;">
                        <i class="bi bi-shield-check text-primary fs-6"></i>PIN Akses Cepat Inspektorat
                    </div>
                    <div class="text-muted mb-3" style="font-size:0.75rem;">Masukkan 6-digit PIN keamanan untuk verifikasi cepat.</div>

                    <form method="POST" action="{{ route('quick-login') }}">
                        @csrf
                        <div class="mb-3">
                            <input type="password" id="pin_code" name="pin" class="form-control pin-input" placeholder="••••••" maxlength="6" autofocus required>
                        </div>
                        <button type="submit" class="btn-submit-gradient">
                            <i class="bi bi-shield-lock-fill fs-5"></i>
                            <span>Verifikasi PIN & Masuk</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- Security Footer --}}
        <div class="login-footer">
            <div class="system-badge">
                <i class="bi bi-shield-fill-check text-success"></i>
                <span>Sistem Informasi Terenkripsi</span>
            </div>
            <div class="copyright">
                &copy; {{ date('Y') }} Pemerintah Kab. Puncak Jaya &mdash; Inspektorat
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
