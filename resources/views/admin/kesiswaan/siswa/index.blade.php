@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kesiswaan /</span> Data Siswa</h4>

<div class="card">
    <div class="card-header">
        {{-- Memberi ID pada form untuk JavaScript --}}
        <form id="filter-form" action="{{ route('admin.kesiswaan.siswa.index') }}" method="GET">
            <div class="row g-3 align-items-center">
                {{-- Filter Kelas --}}
                <div class="col-md-5">
                    <select id="rombel-filter" name="rombel_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->rombongan_belajar_id }}" {{ request('rombel_id') == $rombel->rombongan_belajar_id ? 'selected' : '' }}>
                                {{ $rombel->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Kotak Pencarian --}}
                <div class="col-md-7">
                    <input id="search-input" type="text" name="search" class="form-control" placeholder="Cari Nama, NISN, atau NIK Siswa..." value="{{ request('search') }}">
                </div>
                {{-- Tombol "Cari" tidak lagi diperlukan, jadi kita bisa menyembunyikannya atau menghapusnya --}}
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
                    <td>{{ $siswas->firstItem() + $key }}</td>
                    <td>
                        @php
                            // Membuat inisial nama untuk placeholder
                            $namaParts = explode(' ', $siswa->nama);
                            $initials = count($namaParts) > 1 
                                ? strtoupper(substr($namaParts[0], 0, 1) . substr(end($namaParts), 0, 1))
                                : strtoupper(substr($siswa->nama, 0, 2));
                        @endphp
                        <img src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : 'https://placehold.co/40x40/696cff/FFFFFF?text=' . $initials }}" 
                             alt="Foto {{ $siswa->nama }}" 
                             class="rounded-circle" 
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
                            {{ $siswa->rombel->nama ?? 'Belum ada kelas' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex">
                            <a class="btn btn-icon btn-sm btn-outline-primary me-2" href="{{ route('admin.kesiswaan.siswa.edit', $siswa->id) }}" title="Edit Siswa">
                                <i class="bx bx-edit-alt"></i>
                            </a>
                            <a href="{{ route('admin.kesiswaan.siswa.cetak_kartu', $siswa->id) }}" class="btn btn-icon btn-sm btn-info" target="_blank" title="Cetak Kartu">
                                <i class="bx bx-id-card"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">Tidak ada data siswa yang cocok dengan pencarian Anda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{-- Link Paginasi --}}
        {{ $siswas->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Menunggu seluruh halaman dimuat sebelum menjalankan script
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filter-form');
        const rombelFilter = document.getElementById('rombel-filter');
        const searchInput = document.getElementById('search-input');

        // 1. Kirim form secara otomatis saat filter kelas diubah
        rombelFilter.addEventListener('change', function () {
            filterForm.submit();
        });

        // 2. Kirim form saat pengguna mengetik di kotak pencarian,
        //    tapi dengan sedikit jeda (debounce) agar tidak terlalu sering.
        let debounceTimeout;
        searchInput.addEventListener('input', function () {
            // Hapus timer sebelumnya setiap kali ada input baru
            clearTimeout(debounceTimeout);
            
            // Set timer baru. Form akan dikirim setelah 500ms pengguna berhenti mengetik.
            debounceTimeout = setTimeout(() => {
                filterForm.submit();
            }, 500); 
        });
    });
</script>
@endpush

