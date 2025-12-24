@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">Landing /</span> Ekstrakurikuler</h4>
            <small class="text-muted">Kegiatan pengembangan diri siswa</small>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bx bx-plus me-1"></i> Tambah Ekskul
        </button>
    </div>

    <div class="row g-4">
        @forelse($ekskuls as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden group-action-card">
                
                <div class="position-absolute top-0 end-0 p-2" style="z-index: 10;">
                    <div class="d-flex gap-1">
                        <button type="button" 
                                class="btn btn-sm btn-light btn-icon shadow-sm opacity-75 hover-100 btn-edit-action"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEdit"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->nama_ekskul }}"
                                data-pembina="{{ $item->pembina }}"
                                data-jadwal="{{ $item->jadwal }}"
                                data-foto="{{ asset('storage/ekstrakurikulers/'.$item->foto) }}">
                            <i class="bx bx-pencil text-warning"></i>
                        </button>

                        <form action="{{ route('admin.landing.ekstrakurikuler.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus ekskul ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light btn-icon shadow-sm opacity-75 hover-100 text-danger">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="ekskul-img-wrapper bg-light">
                    <img src="{{ asset('storage/ekstrakurikulers/'.$item->foto) }}" 
                         class="card-img-top ekskul-img" 
                         alt="{{ $item->nama_ekskul }}" 
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/400x250?text=No+Image';">
                </div>

                <div class="card-body p-3">
                    <h5 class="card-title text-primary mb-2 text-truncate">{{ $item->nama_ekskul }}</h5>
                    
                    <div class="d-flex align-items-center text-muted small mb-1">
                        <i class="bx bx-user me-2"></i> 
                        <span class="text-truncate">{{ $item->pembina ?? 'Belum ada pembina' }}</span>
                    </div>
                    
                    <div class="d-flex align-items-center text-muted small">
                        <i class="bx bx-time me-2"></i> 
                        <span class="text-truncate">{{ $item->jadwal ?? 'Jadwal menyusul' }}</span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card py-5 text-center border-dashed">
                <div class="card-body">
                    <i class="bx bx-ball fs-1 text-muted mb-3"></i>
                    <h5>Belum ada Ekstrakurikuler</h5>
                    <p class="text-muted">Tambahkan kegiatan ekskul yang ada di sekolah.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $ekskuls->links() }}
    </div>
</div>

<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Ekskul</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.landing.ekstrakurikuler.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_ekskul" placeholder="Contoh: Pramuka / Futsal" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Pembina</label>
                        <input type="text" class="form-control" name="pembina" placeholder="Nama Guru Pembina">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jadwal Latihan</label>
                        <input type="text" class="form-control" name="jadwal" placeholder="Contoh: Setiap Jumat Sore">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Kegiatan <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="foto" accept="image/*" required>
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
                <h5 class="modal-title">Edit Ekskul</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Ekstrakurikuler</label>
                        <input type="text" class="form-control" id="editNama" name="nama_ekskul" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Pembina</label>
                        <input type="text" class="form-control" id="editPembina" name="pembina">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jadwal Latihan</label>
                        <input type="text" class="form-control" id="editJadwal" name="jadwal">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti Foto (Opsional)</label>
                        <input type="file" class="form-control" name="foto" accept="image/*">
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
    .ekskul-img-wrapper {
        position: relative;
        height: 180px;
        overflow: hidden;
    }
    .ekskul-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    .group-action-card:hover .ekskul-img {
        transform: scale(1.05);
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
                document.getElementById('editPembina').value = this.dataset.pembina;
                document.getElementById('editJadwal').value = this.dataset.jadwal;

                let updateUrl = "{{ route('admin.landing.ekstrakurikuler.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                document.getElementById('formEdit').action = updateUrl;
            });
        });
    });
</script>
@endpush

@endsection