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
                        <td>
    <strong>{{ $rombel->nama_rombel }}</strong>
    <div class="mt-1">
        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editRombelModal-{{ $rombel->id }}">Edit</button>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="editRombelModal-{{ $rombel->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form method="POST" action="{{ route('admin.akademik.rombel.update_core', $rombel->id) }}">
            @csrf
            @method('PATCH')
            <div class="modal-header">
              <h5 class="modal-title">Edit Rombel: {{ $rombel->nama_rombel }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Nama Rombel</label>
                <input type="text" name="nama_rombel" class="form-control" value="{{ $rombel->nama_rombel }}">
              </div>
              <div class="mb-3">
                <label class="form-label">Jurusan (isi dengan teks/kompetensi)</label>
                <input type="text" name="jurusan_id_str" class="form-control" value="{{ $rombel->jurusan_id_str }}">
              </div>
              <div class="mb-3">
                <label class="form-label">Kurikulum (isi dengan teks)</label>
                <input type="text" name="kurikulum_id_str" class="form-control" value="{{ $rombel->kurikulum_id_str }}">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</td>

                        {{-- Data dari relasi 'wali' --}}
                        <td>{{ $rombel->wali_name ?? 'Data Wali Hilang' }}</td>

                        {{-- Jurusan disimpan pada kolom rombel (lihat `jurusan_id_str`) --}}
                        <td>{{ $rombel->jurusan_name ?? 'N/A' }}</td>

                        {{-- Data dari kolom 'tahun_ajaran' (sesuai migrasi) --}}
                        <td>{{ $rombel->tahun_ajaran ?? '-' }}</td>

                        <td>{{ $rombel->ruang_name ?? '-' }}</td>
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
