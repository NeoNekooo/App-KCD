@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">Landing /</span> Mitra Industri</h4>
            <small class="text-muted">Partner kerjasama (DUDI) dan instansi terkait</small>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bx bx-buildings me-1"></i> Tambah Mitra
        </button>
    </div>

    <div class="row g-4">
        @forelse($mitras as $item)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden group-action-card text-center p-3">
                
                <div class="position-absolute top-0 end-0 p-2" style="z-index: 10;">
                    <div class="d-flex gap-1">
                        <button type="button" 
                                class="btn btn-xs btn-light btn-icon shadow-sm opacity-75 hover-100 btn-edit-action"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEdit"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->nama_mitra }}"
                                data-bidang="{{ $item->bidang_kerjasama }}"
                                data-logo="{{ asset('storage/mitras/'.$item->logo) }}">
                            <i class="bx bx-pencil text-warning"></i>
                        </button>

                        <form action="{{ route('admin.landing.mitra.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus mitra ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-light btn-icon shadow-sm opacity-75 hover-100 text-danger">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-center mb-3" style="height: 100px;">
                    <img src="{{ asset('storage/mitras/'.$item->logo) }}" 
                         alt="{{ $item->nama_mitra }}" 
                         class="img-fluid"
                         style="max-height: 80px; max-width: 100%; object-fit: contain;"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/150x80?text=No+Logo';">
                </div>

                <h6 class="card-title text-primary mb-1 text-truncate small fw-bold">{{ $item->nama_mitra }}</h6>
                <p class="card-text text-muted small text-truncate">{{ $item->bidang_kerjasama ?? '-' }}</p>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card py-5 text-center border-dashed">
                <div class="card-body">
                    <i class="bx bx-building fs-1 text-muted mb-3"></i>
                    <h5>Belum ada Mitra</h5>
                    <p class="text-muted">Tambahkan logo perusahaan yang bekerjasama.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Mitra Industri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.landing.mitra.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Perusahaan / Instansi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_mitra" placeholder="PT. Mencari Cinta Sejati" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bidang Kerjasama (Opsional)</label>
                        <input type="text" class="form-control" name="bidang_kerjasama" placeholder="Contoh: Tempat PKL, Rekrutmen">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo Perusahaan <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="logo" accept="image/*" required>
                        <div class="form-text">Gunakan format PNG (transparan) agar lebih bagus.</div>
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

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Mitra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Perusahaan</label>
                        <input type="text" class="form-control" id="editNama" name="nama_mitra" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bidang Kerjasama</label>
                        <input type="text" class="form-control" id="editBidang" name="bidang_kerjasama">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti Logo (Opsional)</label>
                        <input type="file" class="form-control" name="logo" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .btn-xs {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
        border-radius: 0.2rem;
    }
    .hover-100:hover { opacity: 1 !important; }
    .border-dashed { border: 2px dashed #d9dee3; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit-action');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                
                document.getElementById('editNama').value = this.dataset.nama;
                document.getElementById('editBidang').value = this.dataset.bidang;

                let updateUrl = "{{ route('admin.landing.mitra.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                document.getElementById('formEdit').action = updateUrl;
            });
        });
    });
</script>
@endpush

@endsection