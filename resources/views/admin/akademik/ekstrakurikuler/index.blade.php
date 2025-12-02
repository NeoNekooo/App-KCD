@extends('layouts.admin')
@section('title', 'Data Ekstrakurikuler')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- 🔹 Header & Breadcrumb --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Akademik /</span> Ekstrakurikuler
            </h4>
            <p class="text-muted mb-0">Daftar kegiatan ekstrakurikuler yang tersedia di sekolah.</p>
        </div>
        {{-- Tombol Tambah yang kini membuka modal --}}
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEkstrakurikulerModal">
            <i class="bx bx-plus me-1"></i> Tambah Ekstrakurikuler
        </button>
    </div>

    {{-- 🔹 Notifikasi Alert (Success/Error) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Gagal menyimpan data. Silakan periksa kembali input Anda.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 🔹 Card Utama --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header text-black d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bx bx-medal me-2"></i>Data Ekstrakurikuler</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th>Nama Ekstrakurikuler</th>
                            <th>Alias</th>
                            <th>Keterangan</th>
                            <th style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ekskul as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td><i class="bx bx-trophy text-primary me-1"></i><strong>{{ $item->nama }}</strong></td>
                                <td><span class="badge bg-label-info px-3 py-2">{{ $item->alias ?? '-' }}</span></td>
                                <td>{{ $item->keterangan ?? '-' }}</td>
                                <td class="text-center">
                                    {{-- Tombol Edit (Memanggil Modal Edit) --}}
                                    <button class="btn btn-sm btn-warning edit-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editEkstrakurikulerModal"
                                        data-id="{{ $item->id }}"
                                        data-nama="{{ $item->nama }}"
                                        data-alias="{{ $item->alias }}"
                                        data-keterangan="{{ $item->keterangan }}"
                                        data-update-url="{{ route('admin.akademik.ekstrakurikuler.update', $item->id) }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    {{-- Tombol Hapus (Memanggil Modal Hapus) --}}
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- Modal Hapus (Per Item) - Sudah menggunakan ID unik --}}
                            <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah Anda yakin ingin menghapus <strong>{{ $item->nama }}</strong>? Tindakan ini tidak dapat dibatalkan.
                                        </div>
                                        <div class="modal-footer">
                                            <form action="{{ route('admin.akademik.ekstrakurikuler.destroy', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bx bx-calendar-event bx-lg text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Belum ada data ekstrakurikuler yang tercatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL TAMBAH (CREATE) --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="createEkstrakurikulerModal" tabindex="-1" aria-labelledby="createEkstrakurikulerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="createEkstrakurikulerModalLabel">Tambah Ekstrakurikuler Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.akademik.ekstrakurikuler.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create_nama" class="form-label">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="create_nama" name="nama" value="{{ old('nama') }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="create_alias" class="form-label">Alias (Singkatan)</label>
                        <input type="text" class="form-control @error('alias') is-invalid @enderror" id="create_alias" name="alias" value="{{ old('alias') }}">
                        @error('alias')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="create_keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="create_keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ========================================================================= --}}
{{-- MODAL EDIT (UPDATE) - Single Dynamic Modal --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="editEkstrakurikulerModal" tabindex="-1" aria-labelledby="editEkstrakurikulerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editEkstrakurikulerModalLabel">Edit Ekstrakurikuler</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- Form akan diisi dinamis oleh JavaScript --}}
            <form id="editForm" method="POST">
                @csrf
                @method('PUT') {{-- PENTING: Untuk method UPDATE --}}
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama" class="form-label">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_alias" class="form-label">Alias (Singkatan)</label>
                        <input type="text" class="form-control" id="edit_alias" name="alias">
                    </div>
                    <div class="mb-3">
                        <label for="edit_keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Perbarui Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- SCRIPT UNTUK MODAL EDIT --}}
{{-- ========================================================================= --}}
@push('scripts')
<script>
    // Event listener untuk tombol Edit
    document.addEventListener('DOMContentLoaded', function () {
        const editModal = document.getElementById('editEkstrakurikulerModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            // Tombol yang memicu modal
            const button = event.relatedTarget;
            
            // Ambil data dari data attributes tombol
            const nama = button.getAttribute('data-nama');
            const alias = button.getAttribute('data-alias');
            const keterangan = button.getAttribute('data-keterangan');
            const updateUrl = button.getAttribute('data-update-url');

            // Update form action dan input fields
            const form = editModal.querySelector('#editForm');
            form.action = updateUrl;
            
            editModal.querySelector('#edit_nama').value = nama;
            editModal.querySelector('#edit_alias').value = alias;
            editModal.querySelector('#edit_keterangan').value = keterangan;
            
            // Opsional: Jika ada error validation, pastikan modal dibuka dengan data yang benar setelah gagal
            // Dalam kasus ini, Anda mungkin perlu logic tambahan di controller atau AJAX jika menggunakan SPA/Livewire
        });
    });
</script>
@endpush
@endsection