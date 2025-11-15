@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Laporan Absensi Guru & GTK</h5>
    </div>
    
    <div class="card-body">
        
        {{-- Form Filter --}}
        <form action="{{ route('admin.absensi.gtk.laporan') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" 
                           value="{{ $tanggalMulai }}">
                </div>
                <div class="col-md-3">
                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control" 
                           value="{{ $tanggalSelesai }}">
                </div>
                <div class="col-md-4">
                    <label for="gtk_id" class="form-label">Filter Guru (Opsional)</label>
                    <select id="gtk_id" name="gtk_id" class="form-select">
                        <option value="">Semua Guru & GTK</option>
                        @foreach ($gtks as $gtk)
                            <option value="{{ $gtk->id }}" {{ request('gtk_id') == $gtk->id ? 'selected' : '' }}>
                                {{ $gtk->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <hr>

        {{-- Tabel Laporan --}}
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Nama Guru / GTK</th>
                        <th>Status</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporan as $index => $absensi)
                    <tr>
                        <td>{{ $laporan->firstItem() + $index }}</td>
                        <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->isoFormat('dddd, D MMMM Y') }}</td>
                        <td>{{ $absensi->gtk->nama ?? 'N/A' }}</td>
                        <td>
                            @php
                                $badgeClass = 'bg-label-secondary';
                                if ($absensi->status == 'Hadir') $badgeClass = 'bg-label-success';
                                if ($absensi->status == 'Sakit') $badgeClass = 'bg-label-warning';
                                if ($absensi->status == 'Izin') $badgeClass = 'bg-label-info';
                                if (in_array($absensi->status, ['Cuti', 'Dinas Luar'])) $badgeClass = 'bg-label-primary';
                                if ($absensi->status == 'Alfa') $badgeClass = 'bg-label-danger';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $absensi->status }}</span>
                        </td>
                        <td>
                            @if ($absensi->jam_masuk)
                                <strong>{{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') }}</strong>
                                @if($absensi->status_kehadiran == 'Terlambat')
                                    <span class="badge bg-label-warning ms-1">Terlambat</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            {{ $absensi->jam_pulang ? \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') : '-' }}
                        </td>
                        <td>{{ $absensi->keterangan }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data absensi pada rentang tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        <div class="mt-3">
            {{ $laporan->links() }}
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
(Opsional) Jika Anda menggunakan select2 untuk filter guru
$(document).ready(function() {
    $('#gtk_id').select2({
        placeholder: 'Semua Guru & GTK',
        allowClear: true
    });
});
</script>
@endpush