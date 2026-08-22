@extends('layouts.app')

@section('title', 'Detail Pemeriksaan')

@section('content')
<div class="container-fluid py-3" style="max-width:1100px;">

    {{-- Header Card --}}
    <div class="card border-0 mb-3 overflow-hidden" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%); border-radius:12px; position:relative;">
        <div style="position:absolute; top:0; right:0; bottom:0; left:0; background:radial-gradient(circle at top right, rgba(255,255,255,0.07), transparent 60%); pointer-events:none;"></div>
        <div class="card-body py-3 px-4 position-relative" style="z-index:1;">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <a href="{{ route('pemeriksaan.index') }}" class="text-decoration-none" style="color:rgba(255,255,255,0.5); font-size:0.78rem;">
                            <i class="bi bi-arrow-left me-1"></i>Daftar Pemeriksaan
                        </a>
                    </div>
                    <h5 class="mb-1 text-white fw-bold d-flex align-items-center gap-2" style="font-size:1rem;">
                        <i class="bi bi-folder2-open"></i> {{ $pemeriksaan->nama }}
                    </h5>
                    <div class="d-flex align-items-center flex-wrap gap-3 mt-2" style="font-size:0.75rem; color:#cbd5e1;">
                        <span><i class="bi bi-calendar3 me-1"></i>Tahun {{ $pemeriksaan->tahun }}</span>
                        @if($pemeriksaan->tanggal_mulai)
                        <span><i class="bi bi-play-circle me-1"></i>{{ $pemeriksaan->tanggal_mulai->format('d/m/Y') }}</span>
                        @endif
                        @if($pemeriksaan->tanggal_selesai)
                        <span><i class="bi bi-stop-circle me-1"></i>{{ $pemeriksaan->tanggal_selesai->format('d/m/Y') }}</span>
                        @endif
                        @if($pemeriksaan->keterangan)
                        <span><i class="bi bi-info-circle me-1"></i>{{ $pemeriksaan->keterangan }}</span>
                        @endif
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @php
                        $stBg = $pemeriksaan->status === 'aktif' ? '#22c55e' : ($pemeriksaan->status === 'selesai' ? '#94a3b8' : '#f59e0b');
                    @endphp
                    <span class="badge rounded-pill px-3 py-1" style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2); color:#fff; font-size:0.72rem; font-weight:600;">
                        <span class="rounded-circle d-inline-block me-1" style="width:7px; height:7px; background:{{ $stBg }};"></span>
                        {{ ucfirst($pemeriksaan->status) }}
                    </span>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('pemeriksaan.edit', $pemeriksaan->id) }}"
                       class="btn btn-sm" style="background:rgba(255,255,255,0.1); color:#fff; border:1px solid rgba(255,255,255,0.2); font-size:0.75rem;">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    @endif
                </div>
            </div>

            {{-- Stats Mini --}}
            <div class="d-flex flex-wrap gap-2 mt-3 pt-3" style="border-top:1px solid rgba(255,255,255,0.08);">
                <div class="px-3 py-2 rounded-3 text-center" style="background:rgba(255,255,255,0.07); min-width:90px;">
                    <div class="fw-bold text-white" style="font-size:1.1rem;">{{ $surats->total() }}</div>
                    <div style="font-size:0.65rem; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Total Surat</div>
                </div>
                <div class="px-3 py-2 rounded-3 text-center" style="background:rgba(255,255,255,0.07); min-width:90px;">
                    <div class="fw-bold text-white" style="font-size:1.1rem;">{{ $surats->getCollection()->where('status','aktif')->count() }}</div>
                    <div style="font-size:0.65rem; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Aktif</div>
                </div>
                <div class="px-3 py-2 rounded-3 text-center" style="background:rgba(255,255,255,0.07); min-width:90px;">
                    <div class="fw-bold text-white" style="font-size:1.1rem;">{{ $surats->getCollection()->where('status','selesai')->count() }}</div>
                    <div style="font-size:0.65rem; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Selesai</div>
                </div>
                @php $avgProg = $surats->total() > 0 ? round($surats->getCollection()->avg('opd_progress')) : 0; @endphp
                <div class="px-3 py-2 rounded-3 text-center" style="background:rgba(255,255,255,0.07); min-width:90px;">
                    <div class="fw-bold" style="font-size:1.1rem; color:{{ $avgProg == 100 ? '#4ade80' : '#60a5fa' }};">{{ $avgProg }}%</div>
                    <div style="font-size:0.65rem; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Avg Progress</div>
                </div>
                @if($pemeriksaan->users->count() > 0)
                <div class="px-3 py-2 rounded-3 d-flex align-items-center gap-2" style="background:rgba(255,255,255,0.07); margin-left:auto;">
                    <i class="bi bi-people text-info" style="font-size:0.9rem;"></i>
                    <div>
                        <div style="font-size:0.65rem; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Tim Pemeriksa</div>
                        <div style="font-size:0.72rem; color:#e2e8f0;">{{ $pemeriksaan->users->pluck('name')->join(', ') }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Daftar Surat --}}
    <div class="card border-0 shadow-sm" style="border-radius:10px;">
        <div class="card-header d-flex justify-content-between align-items-center py-2 px-3" style="background:#fff; border-bottom:1px solid #f1f5f9; border-radius:10px 10px 0 0;">
            <span style="font-size:0.82rem; font-weight:700; color:#1e293b;">
                <i class="bi bi-list-ul me-2 text-primary"></i>Daftar Surat Permintaan
            </span>
            <div class="d-flex gap-2">
                @if(count($unmappedSurats) > 0)
                <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#modalAttachSurat"
                        style="background:#fffbeb; color:#d97706; border:1px solid #fde68a; font-size:0.75rem; padding:3px 10px;">
                    <i class="bi bi-link-45deg me-1"></i>Tarik Surat ({{ count($unmappedSurats) }})
                </button>
                @endif
                <a href="{{ route('surat.create') }}" class="btn btn-sm"
                   style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.75rem; padding:3px 10px;">
                    <i class="bi bi-plus-lg me-1"></i>Buat Surat
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" style="font-size:0.8rem;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th class="ps-4 py-2 border-0" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px; color:#94a3b8; font-weight:700;">No. Surat</th>
                            <th class="py-2 border-0" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px; color:#94a3b8; font-weight:700;">Perihal</th>
                            <th class="py-2 border-0" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px; color:#94a3b8; font-weight:700;">Tanggal</th>
                            <th class="py-2 border-0" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px; color:#94a3b8; font-weight:700; min-width:140px;">Progress</th>
                            <th class="py-2 border-0" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px; color:#94a3b8; font-weight:700;">Status</th>
                            <th class="py-2 border-0 text-center pe-4" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px; color:#94a3b8; font-weight:700;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($surats as $surat)
                        @php
                            $sc = match($surat->status) {
                                'aktif'   => ['bg'=>'#eff6ff','bd'=>'#bfdbfe','txt'=>'#1d4ed8','lbl'=>'Aktif'],
                                'selesai' => ['bg'=>'#f0fdf4','bd'=>'#bbf7d0','txt'=>'#15803d','lbl'=>'Selesai'],
                                'arsip'   => ['bg'=>'#f9fafb','bd'=>'#e5e7eb','txt'=>'#6b7280','lbl'=>'Arsip'],
                                default   => ['bg'=>'#f9fafb','bd'=>'#e5e7eb','txt'=>'#6b7280','lbl'=>ucfirst($surat->status)],
                            };
                            $prog = $surat->opd_progress;
                        @endphp
                        <tr>
                            <td class="ps-4 py-2">
                                <div class="fw-semibold text-dark" style="font-size:0.8rem;">{{ $surat->nomor_surat }}</div>
                                @if($surat->file_surat)
                                <span style="font-size:0.68rem; color:#94a3b8;"><i class="bi bi-paperclip me-1"></i>Ada file</span>
                                @endif
                            </td>
                            <td class="py-2 text-muted" style="max-width:280px;"><div class="text-truncate">{{ $surat->perihal }}</div></td>
                            <td class="py-2 text-muted" style="white-space:nowrap; font-size:0.75rem;">{{ $surat->tanggal_surat->format('d/m/Y') }}</td>
                            <td class="py-2" style="min-width:140px;">
                                <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:0.68rem;">
                                    <span class="text-muted">{{ $surat->opd_selesai + $surat->opd_proses }}/{{ $surat->opd_total }} OPD</span>
                                    <span class="fw-bold {{ $prog == 100 ? 'text-success' : 'text-primary' }}">{{ $prog }}%</span>
                                </div>
                                <div style="height:3px; background:#e9ecef; border-radius:4px; overflow:hidden;">
                                    <div style="height:100%; width:{{ $prog }}%; background:{{ $prog == 100 ? '#22c55e' : '#3b82f6' }}; border-radius:4px;"></div>
                                </div>
                            </td>
                            <td class="py-2">
                                <span style="font-size:0.68rem; background:{{ $sc['bg'] }}; color:{{ $sc['txt'] }}; border:1px solid {{ $sc['bd'] }}; padding:2px 8px; border-radius:999px; font-weight:600;">
                                    {{ $sc['lbl'] }}
                                </span>
                            </td>
                            <td class="py-2 text-center pe-4">
                                <a href="{{ route('surat.show', $surat->id) }}" class="btn btn-sm"
                                   style="background:#f8f9fa; color:#4b5563; border:1px solid #e5e7eb; font-size:0.72rem; padding:2px 10px;">
                                    <i class="bi bi-eye me-1"></i>Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox d-block mb-2" style="font-size:2rem; opacity:0.3;"></i>
                                <div style="font-size:0.82rem;">Belum ada surat permintaan untuk pemeriksaan ini.</div>
                                <a href="{{ route('surat.create') }}" class="btn btn-sm mt-2"
                                   style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.75rem;">
                                    <i class="bi bi-plus-lg me-1"></i>Buat Surat Sekarang
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($surats->hasPages())
        <div class="card-footer bg-white border-0 py-2 px-3">
            {{ $surats->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>

{{-- Modal Tarik Surat --}}
@if(count($unmappedSurats) > 0)
<div class="modal fade" id="modalAttachSurat" tabindex="-1" aria-labelledby="modalAttachSuratLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <form action="{{ route('pemeriksaan.attach-surat', $pemeriksaan->id) }}" method="POST">
            @csrf
            <div class="modal-content" style="border-radius:10px; overflow:hidden;">
                <div class="modal-header py-3 px-4" style="background:linear-gradient(135deg,#0b192c 0%,#1a365d 100%);">
                    <h6 class="modal-title text-white fw-bold mb-0 d-flex align-items-center gap-2" id="modalAttachSuratLabel">
                        <i class="bi bi-link-45deg"></i>Tarik Surat Lama ke Pemeriksaan Ini
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size:0.8rem;">
                        Pilih surat-surat yang belum memiliki induk pemeriksaan untuk dipindahkan ke
                        <strong>{{ $pemeriksaan->nama }}</strong>.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle" style="font-size:0.8rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px;" class="text-center">
                                        <input class="form-check-input" type="checkbox" id="checkAllSurat"
                                               onclick="document.querySelectorAll('.surat-checkbox').forEach(cb => cb.checked = this.checked)">
                                    </th>
                                    <th>Nomor Surat</th>
                                    <th>Perihal</th>
                                    <th>Tgl Surat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unmappedSurats as $us)
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input surat-checkbox" type="checkbox" name="surat_ids[]" value="{{ $us->id }}">
                                    </td>
                                    <td class="fw-semibold">{{ $us->nomor_surat }}</td>
                                    <td class="text-muted">{{ Str::limit($us->perihal, 60) }}</td>
                                    <td class="text-muted">{{ $us->tanggal_surat->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="gap:0.5rem;">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm fw-semibold"
                            style="background:#0b192c; color:#fff; border:0; font-size:0.82rem;">
                        <i class="bi bi-save me-1"></i>Simpan Pilihan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
