@extends('layouts.admin')
@section('title', 'Layanan Tamu KCD')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">Dashboard /</span> Layanan Tamu</h4>
            <div class="text-muted small mt-1">Kelola Tiket Antrian Tamu Harian Kantor Cabang Dinas</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.display.antrian') }}" target="_blank" class="btn btn-dark shadow-sm rounded-pill fw-bold">
                <i class='bx bx-tv me-2'></i>Buka Layar TV
            </a>
            <a href="{{ route('guest.buku-tamu') }}" target="_blank" class="btn btn-primary shadow-sm rounded-pill fw-bold">
                <i class='bx bx-qr-scan me-2'></i>Lihat QR Form
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bx bx-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-warning alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bx bx-error-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 fw-bold"><i class="bx bx-list-ul me-2 text-primary"></i>Daftar Tunggu Hari Ini</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="fw-semibold text-uppercase font-size-13 py-3" style="width: 50px;">No</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">No Antrian</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">Tamu</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">Detail & Keperluan</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">Status</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3 text-center">Aksi Panggilan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($antrians as $index => $item)
                        <tr class="{{ $item->status == 'dipanggil' ? 'bg-primary-subtle' : ($item->status == 'selesai' ? 'text-muted' : '') }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="badge bg-label-dark fs-6">{{ $item->nomor_antrian }}</span>
                            </td>
                            <td>
                                <div class="fw-bold mb-1 {{ $item->status == 'selesai' ? 'text-muted' : 'text-dark' }}">{{ $item->nama }}</div>
                                <div class="small text-muted"><i class='bx bxs-institution me-1'></i>{{ $item->asal_instansi }}</div>
                                @if($item->nik)
                                    <div class="small text-muted"><i class='bx bx-id-card me-1'></i>{{ $item->nik }}</div>
                                @endif
                            </td>
                            <td style="white-space: normal; min-width: 250px;">
                                <div class="mb-1">
                                    <span class="badge bg-label-info small"><i class='bx bx-user-pin me-1'></i> Tjn: {{ $item->tujuanPegawai->nama ?? 'Tidak Diketahui' }}</span>
                                </div>
                                <div class="small fst-italic">"{{ Str::limit($item->keperluan, 100) }}"</div>
                            </td>
                            <td>
                                @if($item->status == 'menunggu')
                                    <span class="badge bg-label-warning"><i class='bx bx-time me-1'></i>Menunggu</span>
                                @elseif($item->status == 'dipanggil')
                                    <span class="badge bg-label-primary blink"><i class='bx bx-broadcast me-1'></i>Dipanggil ({{ $item->jumlah_panggilan }}x)</span>
                                @elseif($item->status == 'selesai')
                                    <span class="badge bg-label-success"><i class='bx bx-check-double me-1'></i>Selesai</span>
                                @else
                                    <span class="badge bg-label-danger"><i class='bx bx-x me-1'></i>Batal</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->status != 'selesai' && $item->status != 'batal')
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Tombol Panggil -->
                                        <form action="{{ route('admin.antrian.panggil', $item->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <button class="btn btn-icon btn-primary shadow-sm" data-bs-toggle="tooltip" title="Panggil ke TV" {{ $item->jumlah_panggilan >= 3 ? 'disabled' : '' }}>
                                                <i class="bx bxs-megaphone"></i>
                                            </button>
                                        </form>

                                        <!-- Tombol Selesai -->
                                        <form action="{{ route('admin.antrian.selesai', $item->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <button class="btn btn-icon btn-success shadow-sm" data-bs-toggle="tooltip" title="Tandai Selesai">
                                                <i class="bx bx-check"></i>
                                            </button>
                                        </form>

                                        <!-- Tombol Hapus/Batal -->
                                        <form action="{{ route('admin.antrian.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Batalkan antrian ini?');">
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
                            <td colspan="6" class="text-center py-5">
                                <i class='bx bx-inbox fs-1 text-muted opacity-50 mb-3 block'></i>
                                <h6 class="text-muted fw-semibold">Belum ada antrian tamu hari ini.</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
