@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">Landing /</span> Testimoni</h4>
            <small class="text-muted">Moderasi ulasan dari alumni atau wali murid</small>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bx bx-message-square-add me-1"></i> Tambah Manual
        </button>
    </div>

    <div class="row g-4">
        @forelse($testimonis as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm position-relative overflow-hidden group-action-card p-3 {{ !$item->is_published ? 'border border-warning bg-label-warning' : 'border-0' }}">
                
                @if($item->is_published)
                    <div class="position-absolute top-0 start-0 px-2 py-1 bg-success text-white small rounded-bottom-end">
                        <i class="bx bx-check-circle"></i> Published
                    </div>
                @else
                    <div class="position-absolute top-0 start-0 px-2 py-1 bg-warning text-dark small rounded-bottom-end fw-bold">
                        <i class="bx bx-time"></i> Menunggu Persetujuan
                    </div>
                @endif

                <div class="position-absolute top-0 end-0 p-2" style="z-index: 10;">
                    <div class="d-flex gap-1">
                        <button type="button" 
                                class="btn btn-sm btn-light btn-icon shadow-sm opacity-75 hover-100 btn-edit-action"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEdit"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->nama }}"
                                data-status="{{ $item->status }}"
                                data-isi="{{ $item->isi }}"
                                data-foto="{{ asset('storage/testimonis/'.$item->foto) }}">
                            <i class="bx bx-pencil text-warning"></i>
                        </button>

                        <form action="{{ route('admin.landing.testimoni.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus testimoni ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light btn-icon shadow-sm opacity-75 hover-100 text-danger">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center pt-4">
                    <img src="{{ $item->foto ? asset('storage/testimonis/'.$item->foto) : 'https://ui-avatars.com/api/?name='.urlencode($item->nama).'&background=random' }}" 
                         alt="{{ $item->nama }}" 
                         class="rounded-circle shadow-sm border border-3 border-white mb-2"
                         style="width: 80px; height: 80px; object-fit: cover;">
                    
                    <h5 class="card-title text-primary mb-0">{{ $item->nama }}</h5>
                    <span class="badge {{ $item->is_published ? 'bg-label-secondary' : 'bg-white text-warning' }} mt-1">{{ $item->status }}</span>
                </div>

                <div class="card-body text-center pb-0">
                    <p class="card-text fst-italic small text-muted">
                        "{{ Str::limit($item->isi, 120) }}"
                    </p>
                </div>

                <div class="card-footer bg-transparent border-0 pt-0 text-center mt-2">
                    <form action="{{ route('admin.landing.testimoni.toggle', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @if(!$item->is_published)
                            <button type="submit" class="btn btn-success btn-sm w-100 shadow-sm">
                                <i class="bx bx-check-double me-1"></i> Setujui & Publish
                            </button>
                        @else
                            <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bx bx-hide me-1"></i> Sembunyikan (Draft)
                            </button>
                        @endif
                    </form>
                </div>

            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card py-5 text-center border-dashed">
                <div class="card-body">
                    <i class="bx bx-message-square-dots fs-1 text-muted mb-3"></i>
                    <h5>Belum ada Testimoni</h5>
                    <p class="text-muted">Testimoni baru dari website akan muncul di sini untuk disetujui.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $testimonis->links() }}
    </div>
</div>

<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Testimoni Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.landing.testimoni.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status / Jabatan</label>
                        <input type="text" class="form-control" name="status" placeholder="Ex: Alumni 2020" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi Testimoni</label>
                        <textarea class="form-control" name="isi" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto (Opsional)</label>
                        <input type="file" class="form-control" name="foto" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan & Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Testimoni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="editNama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status / Jabatan</label>
                        <input type="text" class="form-control" id="editStatus" name="status" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi Testimoni</label>
                        <textarea class="form-control" id="editIsi" name="isi" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti Foto (Opsional)</label>
                        <input type="file" class="form-control" name="foto" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
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
                document.getElementById('editStatus').value = this.dataset.status;
                document.getElementById('editIsi').value = this.dataset.isi;
                let updateUrl = "{{ route('admin.landing.testimoni.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                document.getElementById('formEdit').action = updateUrl;
            });
        });
    });
</script>
@endpush

@endsection