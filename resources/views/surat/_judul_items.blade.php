@foreach($items as $item)
@php
    $collapseId      = 'opd-collapse-' . $item->id;
    $opdSelesaiCount = $item->permintaanOpd->where('status','selesai')->count();
    $opdProsesCount  = $item->permintaanOpd->where('status','proses')->count();
    $opdTotal        = $item->permintaanOpd->count();
    $globalNo        = $items->firstItem() + $loop->index;
@endphp
<div class="py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:#f3f4f6 !important;" data-item-id="{{ $item->id }}">
    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
        <div class="d-flex align-items-start gap-2 flex-grow-1" style="font-size:0.82rem;">
            @if($isAdmin)
            <div class="form-check mt-0 pt-1">
                <input class="form-check-input js-bulk-item-checkbox" type="checkbox"
                       value="{{ $item->id }}"
                       data-item-id="{{ $item->id }}"
                       data-surat-id="{{ $item->surat_id }}">
            </div>
            @endif
            <span class="text-muted flex-shrink-0" style="min-width:28px; padding-top:1px;">{{ $globalNo }}.</span>
            <div>
                <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                    <span class="fw-medium">{{ $item->judul_permintaan }}</span>
                    @foreach($item->permintaanOpd as $opdBadge)
                    <span style="font-size:0.6rem; font-weight:500; padding:1px 6px; border-radius:999px;
                        @if($opdBadge->status === 'selesai') background:#dcfce7; color:#15803d;
                        @elseif($opdBadge->status === 'proses') background:#fef3c7; color:#b45309;
                        @else background:#f3f4f6; color:#6b7280; @endif">
                        {{ $opdBadge->opd }}
                    </span>
                    @endforeach
                </div>
                @if($item->catatan)
                <div class="text-muted fst-italic" style="font-size:0.72rem;">
                    <i class="bi bi-chat-text me-1"></i>{{ $item->catatan }}
                </div>
                @endif
            </div>
        </div>
        <div class="d-flex gap-1 flex-shrink-0 align-items-center">
            @if($opdSelesaiCount === $opdTotal && $opdTotal > 0)
            <span style="font-size:0.62rem; background:#dcfce7; color:#16a34a; padding:2px 7px; border-radius:999px;">
                <i class="bi bi-check-circle-fill"></i> Lengkap
            </span>
            @elseif($opdSelesaiCount > 0 || $opdProsesCount > 0)
            <span style="font-size:0.62rem; background:#fef3c7; color:#b45309; padding:2px 7px; border-radius:999px;">
                <i class="bi bi-clock-fill"></i> {{ $opdSelesaiCount + $opdProsesCount }}/{{ $opdTotal }} ada data
            </span>
            @else
            <span style="font-size:0.62rem; background:#fee2e2; color:#dc2626; padding:2px 7px; border-radius:999px;">
                <i class="bi bi-x-circle-fill"></i> Belum ada data
            </span>
            @endif
            @if($opdTotal > 0)
            <button class="btn btn-sm py-0 px-2"
                    style="background:#f9fafb; color:#6b7280; border:1px solid #e5e7eb; font-size:0.7rem;"
                    data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                    aria-expanded="false">
                <i class="bi bi-building me-1"></i>{{ $opdTotal }} OPD
                <i class="bi bi-chevron-down ms-1 toggle-chevron" style="font-size:0.6rem;"></i>
            </button>
            @endif
            @if($isAdmin)
            <button class="btn btn-sm py-0 px-2"
                    style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.7rem;"
                    data-bs-toggle="modal" data-bs-target="#modalUpdateStatus"
                    data-id="{{ $item->id }}"
                    data-judul="{{ $item->judul_permintaan }}"
                    data-deskripsi="{{ $item->deskripsi }}"
                    data-status="{{ $item->status }}"
                    data-catatan="{{ $item->catatan }}"
                    data-pj="{{ $item->penanggung_jawab }}"
                    data-opd="{{ json_encode($item->opd ?? []) }}">
                <i class="bi bi-pencil-square"></i>
            </button>
            <form action="{{ route('permintaan.destroy', $item) }}" method="POST" class="mb-0"
                  onsubmit="return confirm('Hapus item ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm py-0 px-2"
                        style="background:#fff5f5; color:#dc2626; border:1px solid #fecaca; font-size:0.7rem;">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
            @endif
        </div>
    </div>

    @if($opdTotal > 0)
    <div class="collapse ms-4 mt-1" id="{{ $collapseId }}">
        <div style="border-radius:8px; overflow:hidden; border:1px solid #e9ecef;">
            <table class="table table-sm mb-0" style="font-size:0.76rem;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th style="color:#6b7280; font-weight:500;">OPD</th>
                        <th style="width:100px; color:#6b7280; font-weight:500;">Status</th>
                        <th style="width:160px; color:#6b7280; font-weight:500;">Dokumen</th>
                        @if($isAdmin)
                        <th style="width:90px; color:#6b7280; font-weight:500;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($item->permintaanOpd as $opdRow)
                    <tr>
                        <td style="vertical-align:top; padding-top:8px;">
                            <i class="bi bi-building text-primary me-1"></i>
                            <span>{{ $opdRow->opd }}</span>
                            @if($opdRow->catatan)
                            <div class="text-muted fst-italic" style="font-size:0.7rem; margin-top:2px;">
                                <i class="bi bi-chat-text me-1"></i>{{ $opdRow->catatan }}
                            </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $opdRow->status_badge }}" style="font-size:0.65rem;">{{ $opdRow->status_label }}</span>
                            @if($opdRow->selesai_at)
                            <div class="text-muted" style="font-size:0.65rem; margin-top:2px;">{{ $opdRow->selesai_at->format('d/m/Y') }}</div>
                            @endif
                        </td>
                        <td style="vertical-align:top;">
                            @if($opdRow->dokumen->count() > 0)
                            @foreach($opdRow->dokumen as $dok)
                            <div class="d-flex align-items-center gap-1 mb-1">
                                <i class="bi bi-file-earmark text-muted" style="font-size:0.7rem;"></i>
                                <a href="{{ route('dokumen.download', $dok) }}" class="text-decoration-none" style="font-size:0.72rem;">
                                    {{ Str::limit($dok->nama_file, 20) }}
                                </a>
                                @if($isAdmin)
                                <form action="{{ route('dokumen.destroy', $dok) }}" method="POST" class="d-inline mb-0"
                                      onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-link p-0 text-danger" style="font-size:0.65rem; line-height:1;">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                            @endforeach
                            @else
                            <span class="text-muted" style="font-size:0.72rem;">-</span>
                            @endif
                        </td>
                        @if($isAdmin)
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm py-0 px-2"
                                        style="background:#f9fafb; color:#6b7280; border:1px solid #e5e7eb; font-size:0.68rem;"
                                        data-bs-toggle="modal" data-bs-target="#modalUpload"
                                        data-opd-id="{{ $opdRow->id }}"
                                        data-opd-nama="{{ $opdRow->opd }}">
                                    <i class="bi bi-upload"></i>
                                </button>
                                <button class="btn btn-sm py-0 px-2"
                                        style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.68rem;"
                                        data-bs-toggle="modal" data-bs-target="#modalUpdateOpd"
                                        data-opd-id="{{ $opdRow->id }}"
                                        data-opd-nama="{{ $opdRow->opd }}"
                                        data-status="{{ $opdRow->status }}"
                                        data-catatan="{{ $opdRow->catatan }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="/permintaan-opd/{{ $opdRow->id }}" method="POST" class="mb-0"
                                      onsubmit="return confirm('Hapus tag OPD {{ $opdRow->opd }} dari permintaan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm py-0 px-2"
                                            style="background:#fff5f5; color:#dc2626; border:1px solid #fecaca; font-size:0.68rem;">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endforeach
