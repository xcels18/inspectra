<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INSPECTRA - Kabupaten Puncak Jaya</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --bs-font-sans-serif: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            --bs-body-font-family: var(--bs-font-sans-serif);
        }
        body, input, button, select, textarea, .btn, .form-control {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif !important;
            background-color: #f8fafc;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #0b192c 0%, #1a365d 100%);
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: "";
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            background: radial-gradient(circle at top right, rgba(255,255,255,0.08), transparent 60%);
            pointer-events: none;
            z-index: 0;
        }
        
        .navbar-custom {
            padding: 1.5rem 0;
            position: relative;
            z-index: 10;
        }
        
        .navbar-brand-custom {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #fff !important;
            text-decoration: none;
        }
        
        .brand-icon {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .brand-text h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: 1.5px;
            font-size: 1.25rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .brand-text p {
            margin: 0;
            font-size: 0.75rem;
            color: #bae6fd;
            font-weight: 600;
        }
        
        .btn-login {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 0.5rem 1.5rem;
            border-radius: 99px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background: #38bdf8;
            color: #0b192c;
            border-color: #38bdf8;
        }
        
        .hero-content {
            flex: 1;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 10;
            padding-bottom: 5rem;
        }
        
        .hero-title {
            color: #ffffff;
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }
        
        .hero-title span {
            color: #38bdf8;
        }
        
        .hero-subtitle {
            color: rgba(255,255,255,0.75);
            font-size: 1.15rem;
            margin-bottom: 2.5rem;
            line-height: 1.6;
            max-width: 600px;
        }
        
        .btn-start {
            background: #38bdf8;
            color: #0b192c;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            box-shadow: 0 4px 15px rgba(56, 189, 248, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(56, 189, 248, 0.4);
            color: #0b192c;
        }

        /* Features Section */
        .features-section {
            padding: 6rem 0;
            background-color: #f8fafc;
            position: relative;
            margin-top: -60px;
            border-radius: 40px 40px 0 0;
            box-shadow: 0 -10px 30px rgba(0,0,0,0.05);
            z-index: 20;
        }
        
        .section-title {
            text-align: center;
            font-weight: 800;
            color: #0b192c;
            margin-bottom: 3.5rem;
        }
        
        .feature-card {
            background: #fff;
            border-radius: 1rem;
            padding: 2.5rem 2rem;
            height: 100%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.02);
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        
        .feature-icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            background: rgba(11, 25, 44, 0.05);
            color: #0b192c;
        }
        
        .feature-icon-wrapper i {
            font-size: 2rem;
        }
        
        .feature-card h5 {
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1e293b;
        }
        
        .feature-card p {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0;
        }
        
        /* Stats Section */
        .stats-container {
            display: flex;
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255,255,255,0.05);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .stat-item h3 {
            margin: 0;
            color: #fff;
            font-weight: 800;
            font-size: 1.5rem;
        }
        
        .stat-item p {
            margin: 0;
            color: rgba(255,255,255,0.6);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Footer */
        .footer {
            background-color: #040d1a;
            color: rgba(255,255,255,0.6);
            padding: 2rem 0;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        
        .footer p {
            margin: 0;
            font-size: 0.875rem;
        }
        
        @media (max-width: 991.98px) {
            .hero-title { font-size: 2.8rem; }
            .stats-container { flex-direction: column; gap: 1rem; }
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <!-- Navbar -->
            <nav class="navbar-custom d-flex justify-content-between align-items-center">
                <a href="/" class="navbar-brand-custom">
                    <div class="brand-icon">
                        <i class="bi bi-shield-check" style="font-size: 1.5rem; color: #38bdf8;"></i>
                    </div>
                    <div class="brand-text d-none d-sm-block">
                        <h4>INSPECTRA</h4>
                        <p>Kabupaten Puncak Jaya</p>
                    </div>
                </a>
                
                <div>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-login">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-login">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Log in
                            </a>
                        @endauth
                    @endif
                </div>
            </nav>
        </div>
        
        <div class="container hero-content">
            <div class="row align-items-center w-100">
                <div class="col-lg-7 col-xl-6 py-5">
                    <span class="badge bg-primary bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-2 mb-4 d-inline-flex align-items-center">
                        <i class="bi bi-stars me-2"></i> Inspektorat Daerah
                    </span>
                    <h1 class="hero-title">Manajemen <span>Dokumen</span> Pemeriksaan BPK</h1>
                    <p class="hero-subtitle">
                        Platform terpadu untuk mengelola, memantau, dan mendistribusikan surat permintaan data, beserta rekam jejak kepatuhan OPD secara waktu nyata (*real-time*).
                    </p>
                    
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-start">
                                Masuk ke Sistem <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-start">
                                Mulai Sekarang <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        @endauth
                    </div>
                    
                    <!-- Dekorasi Stats kecil di hero -->
                    <div class="stats-container d-none d-md-flex">
                        <div class="stat-item">
                            <i class="bi bi-building text-info fs-4"></i>
                            <div>
                                <h3>Terpusat</h3>
                                <p>Seluruh OPD</p>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-cloud-check text-info fs-4"></i>
                            <div>
                                <h3>Cloud</h3>
                                <p>Google Drive</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-xl-6 d-none d-lg-block position-relative">
                    <!-- Placeholder Ilustrasi/Mockup. Bisa diganti aset gambar BPK/Inspektorat nantinya -->
                    <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-40%, -50%); width: 120%;">
                        <div class="bg-white rounded-4 shadow-lg p-3 mx-auto" style="transform: rotate(3deg); opacity: 0.9; width: 80%; border: 1px solid rgba(0,0,0,0.1);">
                            <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                                <div class="rounded-circle" style="width: 12px; height: 12px; background: #ff5f56;"></div>
                                <div class="rounded-circle" style="width: 12px; height: 12px; background: #ffbd2e;"></div>
                                <div class="rounded-circle" style="width: 12px; height: 12px; background: #27c93f;"></div>
                            </div>
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="bg-light rounded p-2 h-100">
                                        <div class="bg-secondary bg-opacity-25 rounded mb-2" style="height: 8px; width: 60%;"></div>
                                        <div class="bg-secondary bg-opacity-25 rounded mb-2" style="height: 8px; width: 40%;"></div>
                                        <div class="bg-secondary bg-opacity-25 rounded" style="height: 8px; width: 80%;"></div>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="bg-primary bg-opacity-10 rounded p-2 mb-2 d-flex justify-content-between align-items-center">
                                        <div class="bg-primary bg-opacity-25 rounded" style="height: 10px; width: 50%;"></div>
                                        <span class="badge bg-success" style="font-size: 0.5rem;">Selesai</span>
                                    </div>
                                    <div class="bg-primary bg-opacity-10 rounded p-2 mb-2 d-flex justify-content-between align-items-center">
                                        <div class="bg-primary bg-opacity-25 rounded" style="height: 10px; width: 60%;"></div>
                                        <span class="badge bg-warning text-dark" style="font-size: 0.5rem;">Proses</span>
                                    </div>
                                    <div class="bg-primary bg-opacity-10 rounded p-2 d-flex justify-content-between align-items-center">
                                        <div class="bg-primary bg-opacity-25 rounded" style="height: 10px; width: 40%;"></div>
                                        <span class="badge bg-danger" style="font-size: 0.5rem;">Belum</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Layanan & Fitur Utama</h2>
            
            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-envelope-paper"></i>
                        </div>
                        <h5>Manajemen Surat Permintaan</h5>
                        <p>Kelola seluruh surat permintaan data dari Badan Pemeriksa Keuangan dengan mudah, terstruktur, dan tercatat riwayat progresnya.</p>
                    </div>
                </div>
                
                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-building-check"></i>
                        </div>
                        <h5>Monitoring Kepatuhan OPD</h5>
                        <p>Lacak status pemenuhan dokumen dari masing-masing Organisasi Perangkat Daerah (OPD) dan tingkat penyelesaian secara *real-time*.</p>
                    </div>
                </div>
                
                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-cloud-arrow-up"></i>
                        </div>
                        <h5>Sinkronisasi Otomatis Cloud</h5>
                        <p>Setiap dokumen yang diunggah akan tersinkronisasi dan diatur ke dalam struktur *folder* rapi di Google Drive secara otomatis.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p>&copy; {{ date('Y') }} <strong>Pemerintah Kabupaten Puncak Jaya</strong> - Inspektorat Daerah. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
