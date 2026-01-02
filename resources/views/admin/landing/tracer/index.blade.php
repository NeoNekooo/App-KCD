@extends('layouts.admin') {{-- PERBAIKAN: Disesuaikan dengan layout kamu --}}

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Landing /</span> Data Tracer Study
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Rekap Sebaran Alumni</h5>
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Alumni</th>
                        <th>Tahun Lulus</th>
                        <th>Status</th>
                        <th>Instansi / Kampus</th>
                        <th>Jabatan / Jurusan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tracers as $key => $row)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            <strong>{{ $row->siswa->nama ?? 'Data Siswa Terhapus' }}</strong>
                            <br>
                            <small class="text-muted">{{ $row->siswa->nisn ?? '-' }}</small>
                        </td>
                        <td>{{ $row->tahun_lulus }}</td>
                        <td>
                            @if($row->kegiatan_setelah_lulus == 'Bekerja')
                                <span class="badge bg-label-primary">🏢 Bekerja</span>
                            @elseif($row->kegiatan_setelah_lulus == 'Kuliah')
                                <span class="badge bg-label-info">🎓 Kuliah</span>
                            @elseif($row->kegiatan_setelah_lulus == 'Wirausaha')
                                <span class="badge bg-label-success">🏪 Wirausaha</span>
                            @else
                                <span class="badge bg-label-secondary">🔍 {{ $row->kegiatan_setelah_lulus }}</span>
                            @endif
                        </td>
                        <td>{{ $row->nama_instansi ?? '-' }}</td>
                        <td>{{ $row->jabatan_posisi ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class='bx bx-data text-muted' style="font-size: 3rem;"></i>
                            <p class="mt-2 text-muted">Belum ada data tracer masuk.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection