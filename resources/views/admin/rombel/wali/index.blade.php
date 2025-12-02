@extends('layouts.admin')

@section('content')

<h4 class="fw-bold py-3 mb-4">
<span class="text-muted fw-light">Rombongan Belajar /</span> Wali Kelas
</h4>

<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="card-title mb-2 mb-md-0">Data Wali Kelas</h5>
            
            {{-- Tombol-tombol aksi sudah dihapus --}}
            {{-- <small class="text-muted">Menampilkan data rombel dari server.</small> --}}
        </div>
    </div>

    <div class="table-responsive text-nowrap"> 
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 1%;"><input class="form-check-input" type="checkbox" id="selectAll"></th>
                    <th>Nama Rombel</th>
                    <th>Wali</th>
                    <th>Jurusan</th> {{-- <-- KOLOM BARU --}}
                    <th>Tahun Ajaran</th> {{-- <-- KOLOM BARU --}}
                    <th>Ruang</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($rombels as $rombel)
                    <tr>
                        <td>
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $rombel->id }}">
                        </td>
                        <td><strong>{{ $rombel->nama_rombel }}</strong></td>
                        
                        {{-- Data dari relasi 'wali' --}}
                        <td>{{ $rombel->wali->nama ?? 'Data Wali Hilang' }}</td>

                        {{-- Data dari relasi 'jurusan' (sudah diambil di controller) --}}
                        <td>{{ $rombel->jurusan->nama_jurusan ?? 'N/A' }}</td>

                        {{-- Data dari kolom 'tahun_ajaran' (sesuai migrasi) --}}
                        <td>{{ $rombel->tahun_ajaran }}</td>

                        <td>{{ $rombel->ruang ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        {{-- Colspan disesuaikan dengan jumlah kolom (6) --}}
                        <td colspan="6" class="text-center">Belum ada data wali kelas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginasi --}}
    <div class="card-footer">
        {{ $rombels->links('pagination::simple-bootstrap-5') }}
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
