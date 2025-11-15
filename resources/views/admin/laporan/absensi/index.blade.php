@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Laporan /</span> Laporan Absensi Siswa</h4>

{{-- Form Filter --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.laporan.absensi.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" name="start_date" id="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" name="end_date" id="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-3">
                    <label for="rombel_id" class="form-label">Kelas</label>
                    <select name="rombel_id" id="rombel_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->rombongan_belajar_id }}" {{ request('rombel_id') == $rombel->rombongan_belajar_id ? 'selected' : '' }}>
                                {{ $rombel->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status Kehadiran</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                        <option value="Alfa" {{ request('status') == 'Alfa' ? 'selected' : '' }}>Alfa</option>
                    </select>
                </div>
                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-filter-alt me-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('admin.laporan.absensi.index') }}" class="btn btn-outline-secondary">Reset Filter</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Kartu Rekapitulasi --}}
<div class="row g-4 mb-4">
    <div class="col"><div class="card h-100"><div class="card-body text-center"><h6 class="mb-1">Hadir</h6><h4 class="mb-0">{{ $rekap['hadir'] }}</h4></div></div></div>
    <div class="col"><div class="card h-100"><div class="card-body text-center"><h6 class="mb-1">Terlambat</h6><h4 class="mb-0">{{ $rekap['terlambat'] }}</h4></div></div></div>
    <div class="col"><div class="card h-100"><div class="card-body text-center"><h6 class="mb-1">Sakit</h6><h4 class="mb-0">{{ $rekap['sakit'] }}</h4></div></div></div>
    <div class="col"><div class="card h-100"><div class="card-body text-center"><h6 class="mb-1">Izin</h6><h4 class="mb-0">{{ $rekap['izin'] }}</h4></div></div></div>
    <div class="col"><div class="card h-100"><div class="card-body text-center"><h6 class="mb-1">Alfa</h6><h4 class="mb-0">{{ $rekap['alfa'] }}</h4></div></div></div>
</div>

{{-- Tabel Hasil --}}
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Detail Laporan Absensi</h5>
        <a href="{{ route('admin.laporan.absensi.export', request()->query()) }}" class="btn btn-success">
            <i class="bx bx-download me-1"></i> Ekspor ke Excel
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporanAbsensi as $absensi)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->isoFormat('dddd, D MMM Y') }}</td>
                    <td><strong>{{ optional($absensi->siswa)->nama }}</strong></td>
                    <td>{{ optional(optional($absensi->siswa)->rombel)->nama ?? 'N/A' }}</td>
                    <td>
                        @if ($absensi->status_kehadiran == 'Terlambat')
                            <span class="badge bg-label-warning">Terlambat</span>
                        @elseif ($absensi->status == 'Hadir')
                            <span class="badge bg-label-success">Hadir</span>
                        @elseif ($absensi->status == 'Sakit')
                            <span class="badge bg-label-info">Sakit</span>
                        @elseif ($absensi->status == 'Izin')
                            <span class="badge bg-label-primary">Izin</span>
                        @else
                            <span class="badge bg-label-danger">{{ $absensi->status }}</span>
                        @endif
                    </td>
                    <td>{{ $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') : '-' }}</td>
                    <td>{{ $absensi->jam_pulang ? \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') : '-' }}</td>
                    <td>{{ $absensi->keterangan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data absensi yang cocok dengan filter Anda.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $laporanAbsensi->appends(request()->query())->links() }}
    </div>
</div>
@endsection
