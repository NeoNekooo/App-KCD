@extends('layouts.admin')

@section('content')

<h4 class="fw-bold py-3 mb-4">
<span class="text-muted fw-light">Rombongan Belajar /</span> Wali Kelas
</h4>

<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="card-title mb-2 mb-md-0">Data Wali Kelas</h5>
            <div class="d-flex flex-wrap" style="gap: 0.5rem;">
                <a href="{{ route('admin.rombel.wali.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Tambah</a>
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
                    <th>Nama Rombel</th>
                    <th>Wali</th>
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
                        {{-- Pastikan relasi 'wali' tersedia --}}
                        <td>{{ $rombel->wali->nama ?? 'Data Wali Hilang' }}</td>
                        <td>{{ $rombel->ruang }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data wali kelas.</td>
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