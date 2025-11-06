@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Formulir Absensi Kelas: <strong>{{ $rombel->nama }}</strong></h5>
            <small>Tanggal: {{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') }}</small>
        </div>
        <a href="{{ $isGuru ? route('guru.absensi.index') : route('admin.absensi.siswa.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </div>
    
    <div class="card-body">

        {{-- Menambahkan kontrol pencarian & tombol bantu yang mungkin hilang --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="search-siswa-input" class="form-control" placeholder="Cari nama siswa di kelas ini...">
            </div>
            <div class="col-md-6 d-flex justify-content-end align-items-center gap-2 flex-wrap">
                <small id="student-count" class="text-muted"></small>
                <button type="button" class="btn btn-sm btn-success btn-set-all" data-status="Hadir">Set Semua Hadir</button>
                <button type="button" class="btn btn-sm btn-danger btn-set-all" data-status="Alfa">Set Semua Alfa</button>
            </div>
        </div>
        
        <form action="{{ $formAction }}" method="POST">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            <input type="hidden" name="rombel_id" value="{{ $rombel->id }}">
            @if ($jadwal)
                <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
            @endif

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Siswa</th>
                            <th class="text-center" style="width: 45%">Status Kehadiran</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="siswa-table-body">
                        @forelse ($siswas as $index => $siswa)
                        <tr class="student-row" data-nama-siswa="{{ strtolower($siswa->nama) }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $siswa->nama }}</td>
                            <td>
                                @php
                                    $absensiRecord = $absensiRecords->get($siswa->id);
                                    // --- PERBAIKAN DI SINI ---
                                    // Menggunakan optional() untuk menghindari error jika $absensiRecord adalah null
                                    $currentStatus = optional($absensiRecord)->status;
                                @endphp
                                
                                <div class="d-flex justify-content-around flex-wrap">
                                    @foreach (['Hadir', 'Sakit', 'Izin', 'Alfa'] as $status)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" 
                                               name="absensi[{{ $siswa->id }}][status]" 
                                               id="status-{{ $siswa->id }}-{{ $status }}" 
                                               value="{{ $status }}" 
                                               @if ($currentStatus == $status) checked @endif>
                                        <label class="form-check-label" for="status-{{ $siswa->id }}-{{ $status }}">{{ $status }}</label>
                                    </div>
                                    @endforeach
                                </div>

                                @if ($absensiRecord && $absensiRecord->status === 'Hadir')
                                    <div class="text-center mt-2">
                                        <small class="text-muted">
                                            Masuk: <strong>{{ \Carbon\Carbon::parse($absensiRecord->jam_masuk)->format('H:i') }}</strong>
                                        </small>
                                        
                                        @if ($absensiRecord->status_kehadiran === 'Terlambat')
                                            <span class="badge bg-label-warning ms-2">Terlambat</span>
                                        @else
                                            <span class="badge bg-label-success ms-2">Tepat Waktu</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm" 
                                       name="absensi[{{ $siswa->id }}][keterangan]" 
                                       placeholder="Keterangan..."
                                       {{-- --- PERBAIKAN KEDUA DI SINI --- --}}
                                       value="{{ optional($absensiRecord)->keterangan }}">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data siswa di kelas ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginasi dan tombol simpan --}}
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center" id="pagination-controls"></ul>
            </nav>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan Absensi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script JavaScript tidak perlu diubah --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-siswa-input');
    const tableBody = document.getElementById('siswa-table-body');
    const allStudentRows = Array.from(tableBody.querySelectorAll('.student-row'));
    const studentCount = document.getElementById('student-count');
    const paginationControls = document.getElementById('pagination-controls');
    
    let currentPage = 1;
    const rowsPerPage = 10;

    function displayData() {
        const searchTerm = searchInput.value.toLowerCase();
        const activeRows = allStudentRows.filter(row => {
            const studentName = row.getAttribute('data-nama-siswa');
            return studentName.includes(searchTerm);
        });

        studentCount.textContent = `Menampilkan ${activeRows.length} dari ${allStudentRows.length} siswa.`;
        setupPagination(activeRows.length);
        
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        
        allStudentRows.forEach(row => row.style.display = 'none');
        activeRows.slice(start, end).forEach(row => row.style.display = '');
    }

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

    let debounceTimeout;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            currentPage = 1;
            displayData();
        }, 300);
    });
    
    document.querySelectorAll('.btn-set-all').forEach(button => {
        button.addEventListener('click', function() {
            const statusToSet = this.getAttribute('data-status');
            document.querySelectorAll('tr.student-row:not([style*="display: none"]) .form-check-input[value="' + statusToSet + '"]').forEach(radio => {
                radio.checked = true;
            });
        });
    });

    displayData();
});
</script>
@endpush

