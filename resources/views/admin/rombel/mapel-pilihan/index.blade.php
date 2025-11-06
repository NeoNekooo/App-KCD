@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Rombongan Belajar /</span> Mapel Pilihan
</h4>

<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="card-title mb-2 mb-md-0">Data Rombel Mapel Pilihan</h5>
            <div class="d-flex flex-wrap" style="gap: 0.5rem;">
                <a href="{{ route('admin.rombel.mapel-pilihan.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Tambah</a>
                <button type="button" class="btn btn-secondary"><i class="bx bx-pencil me-1"></i> Ubah</button>
                <button type="button" class="btn btn-success"><i class="bx bx-save me-1"></i> Simpan</button>
                <button type="button" class="btn btn-danger"><i class="bx bx-trash me-1"></i> Hapus</button>
            </div>
        </div>
    </div>
    
    <div class="table-responsive text-nowrap"> 
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 1%;"><input class="form-check-input" type="checkbox" id="selectAll"></th>
                    <th>Tingkat Pendidikan</th>
                    <th>Prog/Komp Keahlian</th>
                    <th>Kurikulum</th>
                    <th>Nama Rombel</th>
                    <th>Wali/Guru Kelas</th>
                    <th>Ruang</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($rombels as $rombel)
                    <tr>
                        <td>
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $rombel->id }}">
                        </td>
                        <td>{{ $rombel->tingkat }}</td>
                        <td>{{ $rombel->jurusan->nama_jurusan ?? 'N/A' }}</td>
                        <td>{{ $rombel->kurikulum->nama_kurikulum ?? 'N/A' }}</td>
                        <td><strong>{{ $rombel->nama_rombel }}</strong></td>
                        <td>{{ $rombel->wali->nama ?? 'Belum ada wali' }}</td>
                        <td>{{ $rombel->ruang }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data rombel mapel pilihan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- PERBAIKAN PAGINATION: Menggunakan simple pagination untuk menghilangkan angka halaman --}}
    <div class="card-footer">
        {{ $rombels->links('pagination::simple-bootstrap-5') }}
    </div>
</div>

@endsection

@push('scripts')

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