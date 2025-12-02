@extends('layouts.admin')

@section('content')
{{-- Header Halaman --}}
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Rombongan Belajar /</span> Reguler
</h4>

{{-- Konten Card --}}
<div class="card">
    {{-- Card Header --}}
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="card-title mb-2 mb-md-0">Data Rombel Reguler</h5>
            
            {{-- TOMBOL-TOMBOL AKSI SUDAH DIHAPUS --}}
            {{-- Kita bisa tambahkan info, misal: "Data ditarik dari Sistem Pusat" --}}
            {{-- <small class="text-muted">Menampilkan data rombel yang ditarik dari server.</small> --}}
        </div>
    </div>
    
    {{-- Div untuk tabel responsif --}}
    <div class="table-responsive text-nowrap"> 
        <table class="table table-hover">
            <thead>
                <tr>
                    {{-- Kolom Checkbox untuk seleksi --}}
                    <th style="width: 1%;">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                    </th>
                    <th>Nama Rombel</th>
                    <th>Tingkat</th>
                    <th>Tahun Ajaran</th> {{-- <-- KOLOM BARU --}}
                    <th>Prog/Komp Keahlian</th>
                    <th>Kurikulum</th>
                    <th>Wali/Guru Kelas</th>
                    <th>Ruang</th>
                    <th>Jumlah Siswa</th> {{-- <-- KOLOM BARU --}}
                    <th>Moving Class</th>
                    <th>Kebutuhan Khusus</th>
                    {{-- Kolom Jenis Rombel dihapus karena sudah pasti 'Reguler' --}}
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($rombels as $rombel)
                    <tr>
                        {{-- Checkbox untuk setiap baris data --}}
                        <td>
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $rombel->id }}">
                        </td>
                        <td><strong>{{ $rombel->nama_rombel }}</strong></td>
                        <td>{{ $rombel->tingkat }}</td>
                        <td>{{ $rombel->tahun_ajaran }}</td> {{-- <-- DATA BARU --}}
                        
                        {{-- Menampilkan data relasi dengan aman (jika null) --}}
                        <td>{{ $rombel->jurusan->nama_jurusan ?? 'N/A' }}</td>
                        <td>{{ $rombel->kurikulum->nama_kurikulum ?? 'N/A' }}</td>
                        <td>{{ $rombel->wali->nama ?? 'Belum Diatur' }}</td>
                        
                        <td>{{ $rombel->ruang ?? '-' }}</td>

                        {{-- Menghitung jumlah siswa dari relasi 'siswa' yang sudah di-load --}}
                        <td>{{ $rombel->siswa->count() }} Siswa</td> {{-- <-- DATA BARU --}}
                        
                        <td>
                            @if ($rombel->is_moving_class)
                                <span class="badge bg-label-success me-1">Ya</span>
                            @else
                                <span class="badge bg-label-secondary me-1">Tidak</span>
                            @endif
                        </td>
                        <td>
                            @if ($rombel->melayani_kebutuhan_khusus)
                                <span class="badge bg-label-info me-1">Ya</span>
                            @else
                                <span class="badge bg-label-secondary me-1">Tidak</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        {{-- Colspan disesuaikan dengan jumlah kolom (11) --}}
                        <td colspan="11" class="text-center">Belum ada data rombel reguler.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Link Paginasi --}}
    <div class="card-footer">
        {{ $rombels->links() }}
    </div>
</div>

@endsection

@push('scripts')
{{-- Script untuk fungsionalitas checkbox "select all" --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    selectAllCheckbox.addEventListener('change', function () {
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});
</script>
@endpush
