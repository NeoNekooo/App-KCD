@extends('layouts.admin')

@section('content')
{{-- Formulir ini membungkus seluruh card --}}
<form action="{{ route('admin.absensi.mapel.store') }}" method="POST">
    @csrf
    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
    <input type="hidden" name="tanggal" value="{{ $tanggal }}">

    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="mb-2 mb-md-0">
                    <h5 class="mb-1">Formulir Absensi Mata Pelajaran</h5>
                    <small class="text-muted">
                        {{ $jadwal->mata_pelajaran }} | {{ $rombel->nama }} | {{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') }}
                    </small>
                </div>
                <a href="{{ route('admin.absensi.mapel.index', ['tanggal' => $tanggal]) }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar Jadwal
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="search-siswa" class="form-label">Cari Siswa</label>
                    <input type="text" id="search-siswa" class="form-control" placeholder="Ketik nama siswa untuk filter...">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="button" id="tandai-semua-hadir" class="btn btn-info w-100">
                        <i class="bx bx-check-double me-1"></i> Tandai Semua Hadir
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 30%;">Nama Siswa</th>
                            <th style="width: 45%;">Status Kehadiran</th>
                            <th style="width: 20%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="siswa-table-body">
                        @forelse($siswas as $siswa)
                            @php
                                // Ambil data absensi jika sudah ada
                                $absen = $absensiRecords->get($siswa->id);
                                // Default-kan status ke 'Hadir' jika belum diabsen
                                $status = $absen?->status ?? 'Hadir'; 
                            @endphp
                            
                            {{-- Baris siswa, memiliki data-nama untuk JS pencarian --}}
                            <tr class="siswa-row" data-nama="{{ strtolower($siswa->nama) }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $siswa->nama }}</strong><br>
                                    <small class="text-muted">NIS: {{ $siswa->nis ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    {{-- Grup radio button untuk satu siswa --}}
                                    <div class="d-flex flex-wrap" data-siswa-id="{{ $siswa->id }}">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input status-radio" type="radio" name="absensi[{{ $siswa->id }}][status]" 
                                                   id="hadir-{{ $siswa->id }}" value="Hadir" @checked($status == 'Hadir')>
                                            <label class="form-check-label" for="hadir-{{ $siswa->id }}">Hadir</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input status-radio" type="radio" name="absensi[{{ $siswa->id }}][status]" 
                                                   id="sakit-{{ $siswa->id }}" value="Sakit" @checked($status == 'Sakit')>
                                            <label class="form-check-label" for="sakit-{{ $siswa->id }}">Sakit</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input status-radio" type="radio" name="absensi[{{ $siswa->id }}][status]" 
                                                   id="izin-{{ $siswa->id }}" value="Izin" @checked($status == 'Izin')>
                                            <label class="form-check-label" for="izin-{{ $siswa->id }}">Izin</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input status-radio" type="radio" name="absensi[{{ $siswa->id }}][status]" 
                                                   id="alfa-{{ $siswa->id }}" value="Alfa" @checked($status == 'Alfa')>
                                            <label class="form-check-label" for="alfa-{{ $siswa->id }}">Alfa</label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="absensi[{{ $siswa->id }}][keterangan]" 
                                           value="{{ $absen?->keterangan ?? '' }}" placeholder="Opsional...">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data siswa di rombongan belajar ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bx bx-save me-1"></i> Simpan Data Absensi
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-siswa');
    const tableBody = document.getElementById('siswa-table-body');
    const allSiswaRows = Array.from(tableBody.querySelectorAll('.siswa-row'));
    const tandaiSemuaButton = document.getElementById('tandai-semua-hadir');

    // --- 1. Fitur Pencarian Siswa (Realtime) ---
    searchInput.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        
        allSiswaRows.forEach(row => {
            const namaSiswa = row.getAttribute('data-nama');
            if (namaSiswa.includes(searchTerm)) {
                row.style.display = ''; // Tampilkan sebagai baris tabel
            } else {
                row.style.display = 'none'; // Sembunyikan
            }
        });
    });

    // --- 2. Fitur "Tandai Semua Hadir" ---
    tandaiSemuaButton.addEventListener('click', function() {
        // Hanya tandai siswa yang terlihat (hasil filter pencarian)
        allSiswaRows.forEach(row => {
            if (row.style.display !== 'none') {
                // Cari radio button 'Hadir' di dalam baris ini
                const hadirRadio = row.querySelector('input[value="Hadir"]');
                if (hadirRadio) {
                    hadirRadio.checked = true;
                }
            }
        });
    });
});
</script>
@endpush