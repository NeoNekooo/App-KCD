@forelse($antrians as $index => $item)
    <tr
        class="{{ $item->status == 'dipanggil' ? 'bg-primary-subtle' : ($item->status == 'selesai' ? 'text-muted' : '') }}">
        <td>{{ $index + 1 }}</td>
        <td>
            <span class="badge bg-label-dark fs-6">{{ $item->nomor_antrian }}</span>
        </td>
        <td>
            <div class="fw-bold">{{ $item->created_at->format('d/m/Y') }}</div>
            <div class="small text-muted">{{ $item->created_at->format('H:i') }} WIB</div>
        </td>
        <td>
            <div class="fw-bold mb-1 {{ $item->status == 'selesai' ? 'text-muted' : 'text-dark' }}">
                {{ $item->nama }}
            </div>
            <div class="small text-muted mb-1"><i class='bx bxs-briefcase me-2'></i>{{ $item->jabatan_pengunjung }}</div>
            <div class="small text-muted mb-1"><i class='bx bxs-institution me-2'></i>{{ $item->asal_instansi }}</div>
        </td>
        <td>
            @if ($item->nomor_hp)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->nomor_hp) }}" target="_blank"
                    class="badge bg-label-success text-decoration-none">
                    <i class='bx bxl-whatsapp me-1'></i>{{ $item->nomor_hp }}
                </a>
            @else
                <span class="text-muted small">-</span>
            @endif
        </td>
        <td style="white-space: normal; min-width: 250px;">
            @if ($item->npsn)
                <div class="small fw-bold text-primary mb-1">NPSN: {{ $item->npsn }}</div>
            @endif
            <div class="small fst-italic">"{{ Str::limit($item->keperluan, 100) }}"</div>
        </td>
        <td>
            @if ($item->status == 'menunggu')
                <span class="badge bg-label-warning"><i class='bx bx-time me-1'></i>Menunggu</span>
            @elseif($item->status == 'dipanggil')
                <span class="badge bg-label-primary blink"><i class='bx bx-broadcast me-1'></i>Dipanggil
                    ({{ $item->jumlah_panggilan }}x)
                </span>
            @elseif($item->status == 'selesai')
                <span class="badge bg-label-success"><i class='bx bx-check-double me-1'></i>Selesai</span>
            @else
                <span class="badge bg-label-danger"><i class='bx bx-x me-1'></i>Batal</span>
            @endif
        </td>
        <td class="text-center">
            @if ($item->status != 'selesai' && $item->status != 'batal')
                <div class="d-flex justify-content-center gap-2">
                    <!-- Tombol Panggil -->
                    <form action="{{ route('admin.antrian.panggil', $item->id) }}" method="POST">
                        @csrf @method('PUT')
                        <button class="btn btn-icon btn-primary shadow-sm" data-bs-toggle="tooltip"
                            title="Panggil ke TV" {{ $item->jumlah_panggilan >= 3 ? 'disabled' : '' }}>
                            <i class="bx bxs-megaphone"></i>
                        </button>
                    </form>

                    <!-- Tombol Selesai -->
                    <form action="{{ route('admin.antrian.selesai', $item->id) }}" method="POST">
                        @csrf @method('PUT')
                        <button class="btn btn-icon btn-success shadow-sm" data-bs-toggle="tooltip"
                            title="Tandai Selesai">
                            <i class="bx bx-check"></i>
                        </button>
                    </form>

                    <!-- Tombol Hapus/Batal -->
                    <form action="{{ route('admin.antrian.destroy', $item->id) }}" method="POST"
                        onsubmit="return confirm('Batalkan antrian ini?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-icon btn-outline-danger" data-bs-toggle="tooltip" title="Batalkan">
                            <i class="bx bx-x"></i>
                        </button>
                    </form>
                </div>
            @else
                <span class="small text-muted fst-italic">- Closed -</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5">
            <i class='bx bx-inbox fs-1 text-muted opacity-50 mb-3 block'></i>
            <h6 class="text-muted fw-semibold">Belum ada antrian tamu hari ini.</h6>
        </td>
    </tr>
@endforelse
