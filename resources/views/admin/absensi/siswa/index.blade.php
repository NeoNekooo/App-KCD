@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Absensi Harian Siswa</h5>
    </div>
    <div class="card-body">
        {{-- Kontrol untuk tanggal dan pencarian --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                {{-- Form ini hanya untuk mengubah tanggal --}}
                <form action="{{ route('admin.absensi.siswa.index') }}" method="GET" id="date-filter-form">
                    <label for="tanggal" class="form-label">Pilih Tanggal Absensi</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ $tanggal }}">
                </form>
            </div>
            <div class="col-md-8">
                <label for="search-input" class="form-label">Cari Kelas atau Wali Kelas</label>
                <input type="text" class="form-control" id="search-input" placeholder="Masukkan nama kelas atau wali kelas...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Wali Kelas</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="rombel-table-body">
                    @forelse ($rombels as $rombel)
                    {{-- Menambahkan atribut data untuk pencarian JS --}}
                    <tr class="rombel-row" data-searchable-text="{{ strtolower($rombel->nama . ' ' . optional($rombel->waliKelas)->nama) }}">
                        <td><strong>{{ $rombel->nama }}</strong></td>
                        <td>{{ optional($rombel->waliKelas)->nama ?? 'N/A' }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.absensi.siswa.show_form', ['rombel_id' => $rombel->id, 'tanggal' => $tanggal]) }}" class="btn btn-sm btn-primary">
                                <i class="bx bx-edit-alt me-1"></i> Lakukan Absensi
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data kelas dengan wali kelas.</td>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Setup Element ---
    const dateInput = document.getElementById('tanggal');
    const dateFilterForm = document.getElementById('date-filter-form');
    const searchInput = document.getElementById('search-input');
    const tableBody = document.getElementById('rombel-table-body');
    const allRombelRows = Array.from(tableBody.querySelectorAll('.rombel-row'));
    const paginationControls = document.getElementById('pagination-controls');
    
    let currentPage = 1;
    const rowsPerPage = 10; // Jumlah kelas per halaman

    // --- Fungsi Utama untuk Menampilkan Data ---
    function displayData() {
        const searchTerm = searchInput.value.toLowerCase();
        const activeRows = allRombelRows.filter(row => {
            const searchableText = row.getAttribute('data-searchable-text');
            return searchableText.includes(searchTerm);
        });

        setupPagination(activeRows.length);
        
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        
        allRombelRows.forEach(row => row.style.display = 'none');
        activeRows.slice(start, end).forEach(row => row.style.display = '');
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

