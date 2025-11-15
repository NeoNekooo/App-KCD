    @extends('layouts.admin')

    @section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manajemen Izin Siswa</h5>
            <a href="{{ route('admin.absensi.izin-siswa.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Buat Izin Baru
            </a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Tipe Izin</th>
                        <th>Status</th>
                        <th>Dicatat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($izinSiswa as $izin)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($izin->tanggal_izin)->isoFormat('D MMMM Y') }}</td>
                            <td><strong>{{ $izin->siswa->nama ?? 'N/A' }}</strong></td>
                            <td>
                                @if($izin->tipe_izin == 'DATANG_TERLAMBAT') <span class="badge bg-label-warning">Datang Terlambat</span>
                                @elseif($izin->tipe_izin == 'PULANG_AWAL') <span class="badge bg-label-info">Pulang Awal</span>
                                @else <span class="badge bg-label-primary">Keluar Sementara</span>
                                @endif
                            </td>
                            <td><span class="badge bg-label-secondary">{{ $izin->status }}</span></td>
                            <td>{{ $izin->pencatat->name ?? 'N/A' }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.absensi.izin-siswa.show', $izin->id) }}"><i class="bx bx-show-alt me-1"></i> Lihat Detail</a>
                                        <form action="{{ route('admin.absensi.izin-siswa.destroy', $izin->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus izin ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item"><i class="bx bx-trash me-1"></i> Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data izin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3 mx-3">
            {{ $izinSiswa->links() }}
        </div>
    </div>
    @endsection
    
