<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspectra &mdash; Login System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Geist', 'Segoe UI', Roboto, sans-serif !important;
            background-color: #eef2f6;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        input, button, select, textarea {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Geist', 'Segoe UI', Roboto, sans-serif !important;
        }

        /* Outer Frame Card (Mockup Canvas Style) */
        .login-wrapper-frame {
            width: 100%;
            max-width: 1140px;
            min-height: 640px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        /* Top Bar Header Navigation */
        .top-navbar {
            padding: 1.25rem 2.5rem 0.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: transparent;
            z-index: 10;
        }

        .top-nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .top-nav-links a {
            font-size: 0.78rem;
            font-weight: 700;
            color: #475569;
            text-decoration: none;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            transition: color 0.15s;
        }

        .top-nav-links a:hover, .top-nav-links a.active {
            color: #0f172a;
        }

        /* Main Content Layout (Split Screen) */
        .main-split-content {
            flex: 1;
            display: flex;
            position: relative;
        }

        /* Left Column: Form Section */
        .form-column {
            flex: 1;
            max-width: 520px;
            padding: 1.5rem 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            z-index: 2;
        }

        .brand-header-box {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 2rem;
        }

        .brand-logos {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logos img {
            height: 44px;
            width: auto;
            object-fit: contain;
        }

        .brand-divider {
            width: 2px;
            height: 36px;
            background: #0f172a;
        }

        .brand-text-title {
            font-size: 1.15rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .brand-text-sub {
            font-size: 0.68rem;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .login-title-heading {
            font-size: 2.2rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.03em;
            margin-bottom: 0.25rem;
        }

        .login-subtitle {
            font-size: 0.88rem;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 0.75rem;
        }

        .heading-accent-bar {
            width: 42px;
            height: 4px;
            background: #0f172a;
            border-radius: 99px;
            margin-bottom: 1.75rem;
        }

        /* Form Controls */
        .form-group-custom {
            margin-bottom: 1.1rem;
        }

        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0.35rem;
            display: block;
        }

        .form-control-custom {
            width: 100%;
            height: 44px;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            font-size: 0.88rem;
            font-weight: 500;
            color: #0f172a;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }

        .form-control-custom:focus {
            border-color: #0f172a;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
            outline: none;
        }

        .form-options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
        }

        .remember-check-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-check-wrap input[type=checkbox] {
            width: 16px;
            height: 16px;
            accent-color: #0f172a;
            cursor: pointer;
        }

        .remember-check-wrap label {
            font-size: 0.78rem;
            color: #64748b;
            cursor: pointer;
            user-select: none;
        }

        .forgot-link {
            font-size: 0.78rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
        }

        .forgot-link:hover {
            color: #0f172a;
        }

        /* Buttons Row (Matching exact layout image) */
        .buttons-row {
            display: flex;
            gap: 12px;
            margin-bottom: 2rem;
        }

        .btn-main-dark {
            flex: 1;
            height: 42px;
            background: #0b192c;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            transition: background 0.15s, transform 0.1s;
        }

        .btn-main-dark:hover {
            background: #1e293b;
            color: #ffffff;
        }

        .btn-outlined-light {
            flex: 1;
            height: 42px;
            background: transparent;
            color: #64748b;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            transition: all 0.15s;
        }

        .btn-outlined-light:hover, .btn-outlined-light.active {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }

        .quick-login-footer-row {
            font-size: 0.75rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .quick-login-footer-row a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        /* Right Column: Dynamic Vector Wavy Illustration Panel */
        .illustration-column {
            flex: 1.1;
            position: relative;
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            overflow: hidden;
        }

        /* Layered SVG Curves & Vector Background */
        .waves-svg-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .illustration-column { display: none; }
            .form-column { max-width: 100%; padding: 2rem; }
            .top-nav-links { display: none; }
        }
    </style>
</head>
<body>

<div class="login-wrapper-frame">
    
    {{-- Top Navbar --}}
    <header class="top-navbar">
        <ul class="top-nav-links">
            <li><a href="#" class="active">BERANDA</a></li>
            <li><a href="#">PEMERIKSAAN</a></li>
            <li><a href="#">DOKUMEN</a></li>
            <li><a href="#">MONITORING OPD</a></li>
            <li><a href="#">LOGIN</a></li>
        </ul>
        <div class="top-nav-links">
            <a href="#">KONTAK INSPEKTORAT</a>
        </div>
    </header>

    {{-- Main Split View --}}
    <main class="main-split-content">
        
        {{-- Left Form Section --}}
        <section class="form-column">
            
            {{-- Brand Logo --}}
            <div class="brand-header-box">
                <div class="brand-logos">
                    <img src="/images/logo-puncak-jaya.png" alt="Logo Kab. Puncak Jaya">
                    <img src="/images/logo-inspektorat.png" alt="Logo Inspektorat">
                </div>
                <div class="brand-divider"></div>
                <div>
                    <div class="brand-text-title">INSPECTRA</div>
                    <div class="brand-text-sub">Inspektorat Kab. Puncak Jaya</div>
                </div>
            </div>

            {{-- Titles --}}
            <h1 class="login-title-heading">Login</h1>
            <p class="login-subtitle">Selamat datang kembali, silakan masuk ke akun Anda</p>
            <div class="heading-accent-bar"></div>

            {{-- Alert Error --}}
            @if($errors->any())
            <div class="alert alert-danger py-2 px-3 border-0 rounded-3 mb-3" style="font-size:0.8rem;">
                <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $errors->first() }}
            </div>
            @endif

            {{-- Dynamic Login View Switcher --}}
            <div class="tab-content" id="loginFormTabs">

                {{-- TAB 1: EMAIL & PASSWORD FORM --}}
                <div class="tab-pane fade show active" id="tabEmailForm" role="tabpanel">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group-custom">
                            <label for="email" class="form-label-custom">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control-custom" value="{{ old('email') }}" placeholder="nama@puncakjayakab.go.id" required autofocus>
                        </div>

                        <div class="form-group-custom">
                            <label for="password" class="form-label-custom">Password</label>
                            <input type="password" id="password" name="password" class="form-control-custom" placeholder="••••••••••••" required>
                        </div>

                        <div class="form-options-row">
                            <div class="remember-check-wrap">
                                <input type="checkbox" name="remember" id="remember">
                                <label for="remember">Remember me</label>
                            </div>
                            <a href="#" class="forgot-link">Forgot password?</a>
                        </div>

                        <div class="buttons-row">
                            <button type="submit" class="btn-main-dark">LOGIN</button>
                            <button type="button" class="btn-outlined-light" data-bs-toggle="tab" data-bs-target="#tabPinForm">PIN CEPAT</button>
                        </div>
                    </form>
                </div>

                {{-- TAB 2: QUICK PIN FORM --}}
                <div class="tab-pane fade" id="tabPinForm" role="tabpanel">
                    <form method="POST" action="{{ route('quick-login') }}">
                        @csrf
                        <div class="form-group-custom">
                            <label for="pin_code" class="form-label-custom">PIN Akses Cepat (6-Digit)</label>
                            <input type="password" id="pin_code" name="pin" class="form-control-custom text-center fw-bold fs-5" placeholder="••••••" maxlength="6" required autofocus>
                        </div>

                        <div class="form-options-row">
                            <div class="remember-check-wrap">
                                <i class="bi bi-shield-check text-primary"></i>
                                <span style="font-size:0.75rem; color:#64748b;">Verifikasi Keamanan PIN</span>
                            </div>
                        </div>

                        <div class="buttons-row">
                            <button type="submit" class="btn-main-dark">MASUK PIN</button>
                            <button type="button" class="btn-outlined-light active" data-bs-toggle="tab" data-bs-target="#tabEmailForm">EMAIL LOGIN</button>
                        </div>
                    </form>
                </div>

            </div>

            {{-- Footer info --}}
            <div class="quick-login-footer-row">
                <span>Atau masuk via:</span>
                <a href="#" data-bs-toggle="tab" data-bs-target="#tabPinForm">PIN Akses Cepat</a>
                <span>&bull;</span>
                <span class="text-muted">Inspektorat Puncak Jaya</span>
            </div>

        </section>

        {{-- Right Illustration Section (Organic Wavy Vector Art inspired by uploaded image) --}}
        <section class="illustration-column">
            
            <svg class="waves-svg-bg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 700" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="waveGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#38bdf8" />
                        <stop offset="50%" stop-color="#0284c7" />
                        <stop offset="100%" stop-color="#0369a1" />
                    </linearGradient>
                    <linearGradient id="waveGrad2" x1="0%" y1="100%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#0284c7" />
                        <stop offset="100%" stop-color="#1d4ed8" />
                    </linearGradient>
                    <linearGradient id="waveGrad3" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#60a5fa" />
                        <stop offset="100%" stop-color="#2563eb" />
                    </linearGradient>
                </defs>

                <!-- Back Layer Light Wave -->
                <path d="M 150,0 Q 280,200 120,400 T 350,700 L 600,700 L 600,0 Z" fill="url(#waveGrad3)" opacity="0.3" />

                <!-- Middle Wave Layer -->
                <path d="M 220,0 C 350,150 180,320 300,500 C 400,620 280,700 350,700 L 600,700 L 600,0 Z" fill="url(#waveGrad1)" opacity="0.75" />

                <!-- Front Dynamic Organic Wave -->
                <path d="M 320,0 C 450,180 250,350 400,550 C 480,640 420,700 480,700 L 600,700 L 600,0 Z" fill="url(#waveGrad2)" />

                <!-- Leaf / Tropical Plant Decorative Vector Elements -->
                <g fill="#10b981" opacity="0.25">
                    <path d="M 80,480 C 120,430 180,440 200,490 C 150,520 100,510 80,480 Z" />
                    <path d="M 120,530 C 170,470 240,490 250,560 C 190,580 140,560 120,530 Z" />
                    <path d="M 50,550 C 90,500 160,520 170,580 C 120,600 70,580 50,550 Z" />
                </g>

                <!-- Subtle Decorative Nodes -->
                <circle cx="280" cy="220" r="18" fill="#ffffff" opacity="0.2" />
                <circle cx="420" cy="140" r="32" fill="#ffffff" opacity="0.15" />
                <circle cx="480" cy="380" r="45" fill="#ffffff" opacity="0.1" />
            </svg>

            <!-- Vector Person Illustration Overlay -->
            <div style="position:relative; z-index:3; text-align:center; padding:3rem 2rem; color:#ffffff;">
                <div style="background:rgba(255,255,255,0.15); backdrop-filter:blur(12px); border-radius:20px; padding:1.75rem 2rem; border:1px solid rgba(255,255,255,0.25); box-shadow:0 15px 35px rgba(0,0,0,0.15); max-width:380px; margin:0 auto 2rem;">
                    <i class="bi bi-shield-check fs-1 text-white mb-2 d-block"></i>
                    <h5 class="fw-bold text-white mb-1" style="font-size:1.1rem;">Sistem Pengawasan Dokumen</h5>
                    <p class="mb-0 text-white-50" style="font-size:0.8rem; line-height:1.5;">Inspektorat Kabupaten Puncak Jaya &mdash; Platform Terpadu Pemantauan & Pemenuhan Dokumen Pemeriksaan Daerah</p>
                </div>
            </div>

        </section>

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
