@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Absensi per Mata Pelajaran</h5>
    </div>
    <div class="card-body">
        
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                {{-- Form ini akan submit otomatis saat tanggal diubah (lihat JS) --}}
                <form action="{{ route('admin.absensi.mapel.index') }}" method="GET" id="date-filter-form">
                    <label for="tanggal" class="form-label">Pilih Tanggal Absensi</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ $tanggalIni }}">
                </form>
            </div>
            <div class="col-md-8">
                <label for="search-input" class="form-label">Cari Mapel atau Kelas</label>
                <input type="text" class="form-control" id="search-input" placeholder="Masukkan nama mapel, kelas, atau jam...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Jam Pelajaran</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="jadwal-table-body">
@forelse ($jadwalMengajar as $jadwal)
@php
$isFilled = $absensiTerisi->contains($jadwal->id);
@endphp
 {{-- [PERBAIKANNYA DI SINI] --}}
 <tr class="jadwal-row" 
 data-searchable-text="{{ strtolower(
$jadwal->mata_pelajaran . ' ' . 
($jadwal->rombel?->nama ?? '') . ' ' . 
$jadwal->jam_mulai . ' ' . 
 $jadwal->jam_selesai
) }}">
<td><strong>{{ $jadwal->mata_pelajaran ?? 'N/A' }}</strong></td>
<td>{{ $jadwal->rombel?->nama ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</td>
                            <td>
                                @if($isFilled)
                                    <span class="badge bg-label-success">Sudah Diabsen</span>
                                @else
                                    <span class="badge bg-label-danger">Belum Diabsen</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.absensi.mapel.show', ['jadwal_id' => $jadwal->id, 'tanggal' => $tanggalIni]) }}" 
                                   class="btn btn-sm {{ $isFilled ? 'btn-secondary' : 'btn-primary' }}">
                                    <i class="bx bx-edit-alt me-1"></i> {{ $isFilled ? 'Edit Absensi' : 'Isi Absensi' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada jadwal mengajar pada tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center" id="pagination-controls">
                {{-- Tombol paginasi akan dibuat oleh JavaScript di sini --}}
            </ul>
        </nav>
    </div>
</div>
@endsection

@push('scripts')
{{-- Ini adalah JS yang sama persis dengan yang Anda berikan, hanya diadaptasi untuk ID tabel ini --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Setup Element ---
    const dateInput = document.getElementById('tanggal');
    const dateFilterForm = document.getElementById('date-filter-form');
    const searchInput = document.getElementById('search-input');
    const tableBody = document.getElementById('jadwal-table-body');
    const allJadwalRows = Array.from(tableBody.querySelectorAll('.jadwal-row'));
    const paginationControls = document.getElementById('pagination-controls');
    
    let currentPage = 1;
    const rowsPerPage = 10; // Jumlah jadwal per halaman

    // --- Fungsi Utama untuk Menampilkan Data ---
    function displayData() {
        const searchTerm = searchInput.value.toLowerCase();
        const activeRows = allJadwalRows.filter(row => {
            const searchableText = row.getAttribute('data-searchable-text');
            return searchableText.includes(searchTerm);
        });

        setupPagination(activeRows.length);
        
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        
        // Sembunyikan semua, lalu tampilkan yang aktif untuk halaman ini
        allJadwalRows.forEach(row => row.style.display = 'none');
        activeRows.slice(start, end).forEach(row => row.style.display = ''); // Tampilkan sebagai baris tabel
    }

    // --- Fungsi untuk Membuat Tombol Paginasi ---
    function setupPagination(totalRows) {
        paginationControls.innerHTML = '';
        const pageCount = Math.ceil(totalRows / rowsPerPage);

        if (pageCount <= 1) return;

        for (let i = 1; i <= pageCount; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.innerText = i;
            a.addEventListener('click', function (e) {
                e.preventDefault();
                currentPage = i;
                displayData();
            });
            
            li.appendChild(a);
            paginationControls.appendChild(li);
        }
    }

    // --- Event Listeners ---
    // Kirim form saat tanggal diubah
    dateInput.addEventListener('change', function() {
        dateFilterForm.submit();
    });

    // Filter realtime saat mengetik
    let debounceTimeout;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            currentPage = 1;
            displayData();
        }, 300);
    });

    // --- Panggilan Awal ---
    displayData();
});
</script>
@endpush