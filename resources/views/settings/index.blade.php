@extends('layouts.app')

@section('content')
<div class="container-fluid py-3" style="max-width: 1000px;">
    <!-- Header -->
    <div class="card shadow-sm mb-3 border-0 overflow-hidden" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); position:relative;">
        <!-- decorative overlay -->
        <div style="position:absolute; top:0; right:0; bottom:0; left:0; background:radial-gradient(circle at top right, rgba(255,255,255,0.06), transparent 60%); pointer-events:none;"></div>
        
        <div class="card-body py-3 px-4 position-relative z-1">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                        <i class="bi bi-gear"></i> Pengaturan Sistem
                    </h5>
                    <div style="font-size:0.75rem; color:#94a3b8; margin-top:2px;">Konfigurasi integrasi Google Drive dan pengaturan aplikasi.</div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm py-2 px-3" role="alert" style="font-size:0.85rem;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-sm mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3">
        <!-- Settings Form -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header py-2 px-3 bg-white" style="border-bottom:1px solid #e9ecef;">
                    <span class="fw-semibold text-primary" style="font-size:0.85rem;">
                        <i class="bi bi-google me-1"></i> Pengaturan Google Drive
                    </span>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="gdrive_folder_id" class="form-label fw-semibold" style="font-size:0.82rem;">ID Folder Utama (Root Folder ID)</label>
                            <input type="text" 
                                   class="form-control form-control-sm bg-light @error('gdrive_folder_id') is-invalid @enderror" 
                                   id="gdrive_folder_id" 
                                   name="gdrive_folder_id" 
                                   value="{{ old('gdrive_folder_id', $gdrive_folder_id) }}" 
                                   placeholder="Contoh: 1B2A3C4D5E6F7G8H9I0J">
                            <div class="form-text" style="font-size:0.75rem;">
                                ID folder Google Drive yang menjadi tempat penyimpanan dokumen (kosongkan untuk default).
                            </div>
                            @error('gdrive_folder_id')
                                <div class="invalid-feedback" style="font-size:0.75rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="gdrive_credentials" class="form-label fw-semibold" style="font-size:0.82rem;">File Kredensial Service Account (.json)</label>
                            
                            @if($gdrive_credentials_path)
                                <div class="alert alert-info py-1 px-2 mb-2 d-flex align-items-center" style="font-size:0.75rem;">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i> 
                                    <span>Kredensial aktif saat ini.</span>
                                </div>
                            @endif

                            <input class="form-control form-control-sm bg-light @error('gdrive_credentials') is-invalid @enderror" 
                                   type="file" 
                                   id="gdrive_credentials" 
                                   name="gdrive_credentials"
                                   accept=".json">
                            <div class="form-text" style="font-size:0.75rem;">
                                Unggah file JSON dari Google Cloud Console. 
                                @if($gdrive_credentials_path) (Biarkan kosong jika tidak ingin mengubah). @endif
                            </div>
                            @error('gdrive_credentials')
                                <div class="invalid-feedback" style="font-size:0.75rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary btn-sm fw-semibold px-4">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header py-2 px-3 bg-white" style="border-bottom:1px solid #e9ecef;">
                    <span class="fw-semibold text-secondary" style="font-size:0.85rem;">
                        <i class="bi bi-info-circle me-1"></i> Petunjuk Pengaturan Google Drive
                    </span>
                </div>
                <div class="card-body p-2">
                    <div class="accordion accordion-flush" id="instructionAccordion">
                        
                        <!-- Step 1 -->
                        <div class="accordion-item border-0 mb-1">
                            <h2 class="accordion-header">
                                <button class="accordion-button rounded bg-light fw-semibold collapsed py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#step1" style="font-size:0.82rem;">
                                    <span class="badge bg-primary me-2">1</span> Buat Service Account di Google Cloud
                                </button>
                            </h2>
                            <div id="step1" class="accordion-collapse collapse" data-bs-parent="#instructionAccordion">
                                <div class="accordion-body px-3 py-2" style="font-size:0.75rem;">
                                    <ol class="mb-0 text-muted ps-3">
                                        <li class="mb-1">Buka <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a>.</li>
                                        <li class="mb-1">Buat proyek baru atau pilih proyek yang sudah ada.</li>
                                        <li class="mb-1">Pergi ke menu <strong>APIs & Services > Library</strong>, cari <strong>Google Drive API</strong>, lalu klik <strong>Enable</strong>.</li>
                                        <li class="mb-1">Pergi ke <strong>APIs & Services > Credentials</strong>.</li>
                                        <li class="mb-1">Klik <strong>Create Credentials > Service Account</strong>. Isi nama dan klik Create.</li>
                                        <li class="mb-1">Penting: <strong>Salin alamat email</strong> Service Account yang baru saja dibuat.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="accordion-item border-0 mb-1">
                            <h2 class="accordion-header">
                                <button class="accordion-button rounded bg-light fw-semibold collapsed py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#step2" style="font-size:0.82rem;">
                                    <span class="badge bg-primary me-2">2</span> Unduh Kredensial JSON
                                </button>
                            </h2>
                            <div id="step2" class="accordion-collapse collapse" data-bs-parent="#instructionAccordion">
                                <div class="accordion-body px-3 py-2" style="font-size:0.75rem;">
                                    <ol class="mb-0 text-muted ps-3">
                                        <li class="mb-1">Masih di menu Credentials, klik Service Account yang baru saja dibuat.</li>
                                        <li class="mb-1">Pilih tab <strong>Keys</strong>.</li>
                                        <li class="mb-1">Klik <strong>Add Key > Create new key</strong>.</li>
                                        <li class="mb-1">Pilih tipe <strong>JSON</strong> dan klik Create. File <code>.json</code> akan otomatis terunduh ke komputer Anda.</li>
                                        <li class="mb-1">Kembali ke aplikasi ini, unggah file JSON tersebut pada form pengaturan di samping.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="accordion-item border-0 mb-1">
                            <h2 class="accordion-header">
                                <button class="accordion-button rounded bg-light fw-semibold collapsed py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#step3" style="font-size:0.82rem;">
                                    <span class="badge bg-primary me-2">3</span> Buat & Bagikan Folder Google Drive
                                </button>
                            </h2>
                            <div id="step3" class="accordion-collapse collapse" data-bs-parent="#instructionAccordion">
                                <div class="accordion-body px-3 py-2" style="font-size:0.75rem;">
                                    <ol class="mb-0 text-muted ps-3">
                                        <li class="mb-1">Buka akun <a href="https://drive.google.com/" target="_blank">Google Drive</a> pribadi atau instansi Anda.</li>
                                        <li class="mb-1">Buat sebuah folder baru, misalnya "BPK Dokumen Sync".</li>
                                        <li class="mb-1">Klik kanan folder tersebut, pilih <strong>Share</strong> (Bagikan).</li>
                                        <li class="mb-1">Pada bagian "Add people and groups", paste <strong>email Service Account</strong> yang disalin dari langkah 1.</li>
                                        <li class="mb-1">Pastikan hak aksesnya diatur sebagai <strong>Editor</strong>, lalu klik Share. Hal ini memberikan akses bagi aplikasi untuk membuat folder dan mengunggah file.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="accordion-item border-0 mb-1">
                            <h2 class="accordion-header">
                                <button class="accordion-button rounded bg-light fw-semibold collapsed py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#step4" style="font-size:0.82rem;">
                                    <span class="badge bg-primary me-2">4</span> Dapatkan Root Folder ID
                                </button>
                            </h2>
                            <div id="step4" class="accordion-collapse collapse" data-bs-parent="#instructionAccordion">
                                <div class="accordion-body px-3 py-2" style="font-size:0.75rem;">
                                    <ol class="mb-0 text-muted ps-3">
                                        <li class="mb-1">Buka folder utama yang baru saja Anda bagikan di Google Drive.</li>
                                        <li class="mb-1">Perhatikan URL di address bar browser Anda. URL tersebut akan terlihat seperti: <br> <code>https://drive.google.com/drive/folders/<strong>1a2B3c4D5e_F6g7H8i9J0k</strong></code></li>
                                        <li class="mb-1">Salin <strong>ID folder</strong> (bagian teks setelah "folders/").</li>
                                        <li class="mb-1">Tempelkan ID tersebut pada input "ID Folder Utama" di form pengaturan sebelah kiri dan klik <strong>Simpan</strong>.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
