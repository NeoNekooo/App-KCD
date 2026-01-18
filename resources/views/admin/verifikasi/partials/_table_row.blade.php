<tr>
    <td class="ps-4">
        <div class="d-flex align-items-center">
            <div class="avatar avatar-sm me-3">
                <span class="avatar-initial rounded-circle bg-label-primary">
                    <i class='bx bxs-school'></i>
                </span>
            </div>
            <div>
                <span class="fw-bold d-block text-dark small">{{ $item->nama_sekolah }}</span>
                <small class="text-muted">{{ $item->nama_guru }}</small>
            </div>
        </div>
    </td>
    <td>
        <span class="badge bg-label-secondary mb-1" style="font-size: 0.7rem;">
            {{ strtoupper(str_replace('-', ' ', $item->kategori)) }}
        </span>
        <div class="text-wrap small text-dark fw-semibold" style="max-width: 250px;">
            {{ \Illuminate\Support\Str::limit($item->judul, 45) }}
        </div>
    </td>
    <td>
        @php
            $statusClass = match ($item->status) {
                'Proses' => 'bg-label-primary',
                'Verifikasi Berkas' => 'bg-label-info',
                'Verifikasi Kasubag' => 'bg-label-warning',
                'Verifikasi Kepala' => 'bg-label-danger',
                'ACC' => 'bg-label-success',
                'Revisi' => 'bg-label-danger',
                default => 'bg-label-secondary',
            };
        @endphp
        <span class="badge {{ $statusClass }}">{{ $item->status }}</span>
    </td>
    <td class="text-end pe-4">
        @if ($item->status == 'Proses')
            <button class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSyarat{{ $item->id }}">
                <i class='bx bx-list-check me-1'></i> Atur Syarat
            </button>
        @elseif(in_array($item->status, ['Verifikasi Berkas', 'Verifikasi Kasubag', 'Verifikasi Kepala']))
            <button class="btn btn-sm btn-info shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCek{{ $item->id }}">
                <i class='bx bx-search-alt me-1'></i> Periksa
            </button>
        @elseif($item->status == 'ACC')
            <a href="{{ route('cetak.sk', $item->uuid) }}" target="_blank" class="btn btn-sm btn-success shadow-sm">
                <i class='bx bx-printer me-1'></i> Cetak SK
            </a>
        @else
            <span class="badge bg-label-secondary">Menunggu</span>
        @endif
    </td>
</tr>