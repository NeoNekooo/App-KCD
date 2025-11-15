@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Indisipliner / Guru /</span> Daftar Indisipliner
</h4>

{{-- ✅ SweetAlert Notifikasi --}}
@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
    </script>
@endif

@if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: `{!! implode('<br>', $errors->all()) !!}`,
        });
    </script>
@endif

<div class="card">
    {{-- Header --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Riwayat Pelanggaran Guru</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalInputPelanggaran">
            <i class="bx bx-plus me-1"></i> Input Pelanggaran
        </button>
    </div>

    {{-- Filter --}}
    <div class="card-body border-top">
        <form action="{{ route('admin.indisipliner.guru.daftar.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Tahun Pelajaran - Semester</label>
                    <select name="semester_id" id="semester_id" class="form-select select2">
                        <option value="">- Semua Semester -</option>
                        @foreach ($semesterList as $item)
                            <option value="{{ $item['semester_id'] }}" 
                                {{ request('semester_id') == $item['semester_id'] ? 'selected' : '' }}>
                                {{ $item['tahun_pelajaran'] }} - {{ $item['semester'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Guru</label>
                    <select name="nama_guru" id="nama_guru_filter" class="form-select select2">
                        <option value="">- Semua Guru -</option>
                        @foreach ($gurus as $guru)
                            <option value="{{ $guru->nama }}" 
                                {{ request('nama_guru') == $guru->nama ? 'selected' : '' }}>
                                {{ $guru->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bx bx-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.indisipliner.guru.daftar.index') }}" 
                       class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bx bx-refresh"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel Data --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Guru</th>
                    <th>Pelanggaran</th>
                    <th>Tahun Pelajaran</th>
                    <th>Semester</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Poin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pelanggaranList as $key => $pelanggaran)
                    <tr>
                        <td>{{ $pelanggaranList->firstItem() + $key }}</td>
                        <td><strong>{{ $pelanggaran->nama_guru }}</strong></td>
                        <td>{{ $pelanggaran->detailPoinGtk->nama ?? 'Tidak diketahui' }}</td>
                        <td>{{ $pelanggaran->tapel ?? '-' }}</td>
                        <td>{{ ucfirst($pelanggaran->semester) ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d M Y') }}</td>
                        <td>{{ $pelanggaran->jam }}</td>
                        <td><span class="badge bg-danger rounded-pill">{{ $pelanggaran->poin }}</span></td>
                        <td>
                            <form action="{{ route('admin.indisipliner.guru.daftar.destroy', $pelanggaran->ID) }}" 
                                  method="POST" class="d-inline form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-sm btn-outline-danger"
                                        data-bs-toggle="tooltip" title="Hapus Data">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bx bx-info-circle bx-lg d-block mb-2"></i>
                            Tidak ada data pelanggaran guru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="card-footer d-flex justify-content-between align-items-center">
        @if($pelanggaranList->total() > 0)
            <small class="text-muted">
                Menampilkan {{ $pelanggaranList->firstItem() }} sampai {{ $pelanggaranList->lastItem() }} 
                dari {{ $pelanggaranList->total() }} data
            </small>
        @endif
        {{ $pelanggaranList->links() }}
    </div>
</div>

{{-- Modal Input Pelanggaran --}}
@include('admin.indisipliner.guru.daftar._modal-form')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    // 🔹 Inisialisasi Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        placeholder: '- Pilih -',
        width: '100%'
    });

    // 🔹 Tooltip Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

    // 🔹 SweetAlert konfirmasi hapus
    $('.form-delete').on('submit', function (e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Hapus Data?',
            text: 'Data pelanggaran ini akan dihapus secara permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                form.submit();
            }
        });
    });

    // 🔹 Auto-select guru di modal berdasarkan filter aktif
    const selectedGuru = "{{ request('nama_guru') }}";
    if (selectedGuru) {
        $('#modalInputPelanggaran').on('shown.bs.modal', function() {
            $('#nama_guru').val(selectedGuru).trigger('change');
        });
    }
});
</script>
@endpush
