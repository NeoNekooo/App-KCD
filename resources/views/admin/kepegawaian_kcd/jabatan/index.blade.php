@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- HERO --}}
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(120deg, #696cff, #8592a3); border-radius: 12px;">
                <div class="card-body d-flex align-items-center text-white p-3">
                    <div class="me-3 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.2);">
                        <i class='bx bx-briefcase-alt-2 text-white' style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 class="text-white fw-bold mb-0">Pengaturan Jabatan KCD</h5>
                        <small class="text-white opacity-75">Tambah, edit, atau hapus jabatan untuk pegawai.</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="d-block text-muted text-uppercase fw-bold small">Total Jabatan</span>
                        <h4 class="mb-0 fw-bolder text-primary">{{ $jabatans->total() }}</h4>
                    </div>
                    <div class="avatar avatar-md">
                        <span class="avatar-initial rounded bg-label-primary d-flex align-items-center justify-content-center">
                            <i class="bx bx-briefcase fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CARD --}}
    <div class="card border-0 shadow-lg" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom">
            <button class="btn btn-sm btn-primary fw-bold rounded-pill shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class='bx bx-plus me-1'></i> Tambah Jabatan
            </button>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle table-borderless">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Nama Jabatan</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Role Access</th>
                        <th class="pe-4 py-3 text-end text-uppercase small fw-bold text-muted">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jabatans as $jabatan)
                    <tr class="border-bottom">
                        <td class="ps-4 fw-semibold">{{ $jabatan->nama }}</td>
                        <td>
                            <span class="badge bg-label-secondary rounded px-2 text-uppercase fw-bold">{{ $jabatan->role }}</span>
                        </td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-sm btn-icon btn-light rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $jabatan->id }}">
                                <i class="bx bx-edit-alt text-warning"></i>
                            </button>
                            <form action="{{ route('admin.kepegawaian_kcd.jabatan.destroy', $jabatan->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-light rounded-circle" onclick="return confirm('Yakin ingin menghapus jabatan ini?')">
                                    <i class="bx bx-trash text-danger"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="modalEdit{{ $jabatan->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-sm modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-bottom py-3 bg-light">
                                    <h6 class="modal-title fw-bold">Edit Jabatan</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.kepegawaian_kcd.jabatan.update', $jabatan->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4 text-start">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Nama Jabatan</label>
                                            <input type="text" name="nama" class="form-control" value="{{ $jabatan->nama }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Role</label>
                                            <input type="text" name="role" class="form-control" value="{{ $jabatan->role }}" required>
                                            <small class="form-text">Role ini akan menentukan hak akses user.</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top bg-light py-2">
                                        <button type="button" class="btn btn-sm btn-label-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <i class='bx bx-folder-open text-muted fs-1'></i>
                            <p class="text-muted mt-2">Belum ada data jabatan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jabatans->hasPages())
        <div class="card-footer border-0 bg-white py-3">
            {{ $jabatans->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 bg-light">
                <h6 class="modal-title fw-bold">Tambah Jabatan Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.kepegawaian_kcd.jabatan.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Jabatan</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Staff Keuangan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Role</label>
                        <input type="text" name="role" class="form-control" placeholder="Contoh: Staff" required>
                        <small class="form-text">Role ini akan menentukan hak akses user.</small>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2">
                    <button type="button" class="btn btn-sm btn-label-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
