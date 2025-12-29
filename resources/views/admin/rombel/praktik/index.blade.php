@extends('layouts.admin')

@section('content')
{{-- Header Halaman --}}
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Rombongan Belajar /</span> Praktik
</h4>

{{-- Konten Card --}}
<div class="card">
    {{-- Card Header --}}
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="card-title mb-2 mb-md-0">Data Rombel Praktik</h5>

            {{-- Tombol-tombol aksi sudah dihapus --}}
            {{-- <small class="text-muted">Menampilkan data rombel yang ditarik dari server.</small> --}}
        </div>
    </div>

    {{-- Div untuk tabel responsif --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 1%;"><input class="form-check-input" type="checkbox" id="selectAll"></th>
                    <th>Nama Rombel</th>
                    <th>Tingkat Pendidikan</th>
                    <th>Tahun Ajaran</th> {{-- <-- KOLOM BARU --}}
                    <th>Prog/Komp Keahlian</th>
                    <th>Kurikulum</th>
                    <th>Wali/Guru Kelas</th>
                    <th>Ruang</th>
                    <th>Moving Class</th>
                    <th>Kebutuhan Khusus</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($rombels as $rombel)
                    <tr>
                        <td>
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $rombel->id }}">
                        </td>
                        <td><strong><a href="{{ route('admin.kesiswaan.buku_induk.rombel_show', $rombel->id) }}">{{ $rombel->nama_rombel }}</a></strong></td>
                        <td>{{ $rombel->tingkat }}</td>
                        <td>{{ $rombel->tahun_ajaran }}</td> {{-- <-- DATA BARU --}}

                        {{-- Menggunakan operator null coalescing untuk relasi yang mungkin null --}}
                        <td>{{ $rombel->jurusan_name ?? 'N/A' }}</td>
                        <td>{{ $rombel->kurikulum_name ?? 'N/A' }}</td>
                        <td>{{ $rombel->wali_name ?? 'Belum ada' }}</td>
                        <td>{{ $rombel->ruang_name ?? '-' }}</td>
                        <td>
                            @if ($rombel->is_moving_class)
                                <span class="badge bg-label-success">Ya</span>
                            @else
                                <span class="badge bg-label-secondary">Tidak</span>
                            @endif
                        </td>
                        <td>
                            @if ($rombel->melayani_kebutuhan_khusus)
                                <span class="badge bg-label-info">Ya</span>
                            @else
                                <span class="badge bg-label-secondary">Tidak</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    {{-- Tampilan jika data kosong --}}
                    {{-- Colspan disesuaikan dengan jumlah kolom (10) --}}
                    <tr>
                        <td colspan="10" class="text-center">Belum ada data rombel praktik.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginasi (Sesuai dengan kode Anda) --}}
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
