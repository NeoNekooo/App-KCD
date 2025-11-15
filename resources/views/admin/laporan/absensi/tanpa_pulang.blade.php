@extends('layouts.admin')

@section('content')
<div class="card">
    <h5 class="card-header">{{ $title ?? 'Laporan Siswa Tanpa Absen Pulang' }}</h5>
    <div class="card-body">
        <p class="mb-4">
            Halaman ini menampilkan daftar siswa yang tercatat absen masuk tetapi tidak melakukan scan saat jam pulang pada hari-hari yang telah berlalu.
            Data ini dapat mengindikasikan siswa yang lupa absen atau pulang sebelum waktunya.
        </p>

        <div class="table-responsive text-nowrap">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 10px;">#</th>
                        <th>Tanggal</th>
                        <th>Nama Siswa</th>
                        <th>Jam Masuk</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporan as $item)
                        <tr>
                            <td>{{ $loop->iteration + $laporan->firstItem() - 1 }}</td>
                            {{-- Format tanggal ke dalam Bahasa Indonesia --}}
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('dddd, D MMMM Y') }}</td>
                            <td>{{ $item->siswa->nama ?? 'Siswa Telah Dihapus' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }} WIB</td>
                            <td>
                                <span class="badge bg-label-danger"><i class="bx bx-error-circle me-1"></i> Tidak Ada Absen Pulang</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="bx bx-check-circle bx-lg text-success mb-2"></i>
                                <p class="mb-0">Tidak ada data untuk ditampilkan. Semua siswa tercatat absen pulang dengan benar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tampilkan tombol pagination jika data lebih dari satu halaman --}}
        @if ($laporan->hasPages())
            <div class="mt-4">
                {{ $laporan->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
