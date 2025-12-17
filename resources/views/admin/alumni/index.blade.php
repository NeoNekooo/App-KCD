@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Alumni /</span> Data Siswa</h4>

<div class="card">
    <div class="card-header">
        {{-- Form filter --}}
        <form id="filter-form" action="{{ route('admin.alumni.dataAlumni') }}" method="GET">
            <div class="row g-3 align-items-center">
                {{-- Filter Kelas --}}
                <div class="col-md-5">
                    <select id="rombel-filter" name="rombel_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->nama_rombel }}" {{ request('rombel_id') == $k->nama_rombel ? 'selected' : '' }}>
                                {{ $k->nama_rombel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Kotak Pencarian --}}
                <div class="col-md-7">
                    <input id="search-input" type="text" name="search" class="form-control"
                        placeholder="Cari Nama, NISN, atau NIK Siswa..." value="{{ request('search') }}">
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama Lengkap</th>
                    <th>L/P</th>
                    <th>NISN</th>
                    <th>Tempat, Tgl Lahir</th>
                    <th>Kelas Saat Ini</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($siswas as $key => $siswa)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @php
                                $namaParts = explode(' ', $siswa->nama);
                                $initials = count($namaParts) > 1
                                    ? strtoupper(substr($namaParts[0],0,1) . substr(end($namaParts),0,1))
                                    : strtoupper(substr($siswa->nama,0,2));
                            @endphp
                            <img src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : 'https://placehold.co/40x40/696cff/FFFFFF?text='.$initials }}"
                                alt="Foto {{ $siswa->nama }}" class="rounded-circle"
                                style="width: 40px; height: 40px; object-fit: cover;">
                        </td>
                        <td><strong>{{ $siswa->nama }}</strong></td>
                        <td>{{ $siswa->jenis_kelamin }}</td>
                        <td>{{ $siswa->nisn ?? '-' }}</td>
                        <td>
                            {{ $siswa->tempat_lahir ?? '-' }},
                            {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->isoFormat('D MMM Y') : '-' }}
                        </td>
                        <td>
                            <span class="badge bg-label-primary me-1">
                                {{ $siswa->nama_rombel ?? 'Belum ada kelas' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex">
                                <a class="btn btn-icon btn-sm btn-outline-primary me-2"
                                    href="{{ route('admin.alumni.pelulusan', $siswa->id) }}" title="Edit Siswa">
                                    <i class="bx bx-edit-alt"></i>
                                </a>
                                <a href="{{ route('admin.alumni.pelulusan', $siswa->id) }}"
                                    class="btn btn-icon btn-sm btn-info" target="_blank" title="Cetak Kartu">
                                    <i class="bx bx-id-card"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data siswa yang cocok dengan pencarian Anda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form');
    const rombelFilter = document.getElementById('rombel-filter');
    const searchInput = document.getElementById('search-input');

    rombelFilter.addEventListener('change', function() {
        filterForm.submit();
    });

    let debounceTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            filterForm.submit();
        }, 500);
    });
});
</script>
@endpush
