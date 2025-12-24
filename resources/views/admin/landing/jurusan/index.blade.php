@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">Landing /</span> Kompetensi Keahlian</h4>
            <small class="text-muted">Daftar Jurusan yang tersedia di sekolah</small>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bx bx-plus me-1"></i> Tambah Jurusan
        </button>
    </div>

    <div class="row g-4">
        @forelse($jurusans as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden group-action-card">
                
                {{-- Action Buttons --}}
                <div class="position-absolute top-0 end-0 p-2" style="z-index: 10;">
                    <div class="d-flex gap-1">
                        <button type="button" 
                                class="btn btn-sm btn-light btn-icon shadow-sm opacity-75 hover-100 btn-edit-action"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEdit"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->nama_jurusan }}"
                                data-singkatan="{{ $item->singkatan }}"
                                data-kajur="{{ $item->kepala_jurusan }}"
                                data-deskripsi="{{ $item->deskripsi }}"> {{-- Tambahkan Data Deskripsi --}}
                            <i class="bx bx-pencil text-warning"></i>
                        </button>

                        <form action="{{ route('admin.landing.jurusan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus jurusan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light btn-icon shadow-sm opacity-75 hover-100 text-danger">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Badge Singkatan --}}
                <div class="position-absolute top-0 start-0 bg-primary text-white px-3 py-1 rounded-bottom-end shadow-sm" style="z-index: 5;">
                    <span class="fw-bold small">{{ $item->singkatan }}</span>
                </div>

                {{-- Image Wrapper --}}
                <div class="jurusan-img-wrapper bg-light cursor-pointer" 
                     onclick="showPreview('{{ $item->nama_jurusan }}', '{{ $item->singkatan }}', '{{ $item->kepala_jurusan }}', '{{ asset('storage/jurusans/'.$item->gambar) }}', `{{ $item->deskripsi }}`)"
                     data-bs-toggle="modal" 
                     data-bs-target="#modalPreview">
                    
                    <img src="{{ asset('storage/jurusans/'.$item->gambar) }}" 
                         class="card-img-top jurusan-img" 
                         alt="{{ $item->nama_jurusan }}" 
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/400x250?text=No+Image';">
                </div>

                <div class="card-body text-center p-3 cursor-pointer"
                     onclick="showPreview('{{ $item->nama_jurusan }}', '{{ $item->singkatan }}', '{{ $item->kepala_jurusan }}', '{{ asset('storage/jurusans/'.$item->gambar) }}', `{{ $item->deskripsi }}`)"
                     data-bs-toggle="modal" 
                     data-bs-target="#modalPreview">
                    
                    <h5 class="card-title text-truncate mb-1 text-primary">{{ $item->nama_jurusan }}</h5>
                    
                    {{-- Deskripsi Singkat di Card (Optional) --}}
                    <p class="text-muted small mb-2 text-truncate">{{ $item->deskripsi ?? 'Belum ada deskripsi.' }}</p>

                    @if($item->kepala_jurusan)
                        <p class="card-text text-muted small mb-0">
                            <i class="bx bx-user-circle me-1"></i> Kaprog: {{ $item->kepala_jurusan }}
                        </p>
                    @else
                        <p class="card-text text-muted small mb-0">-</p>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card py-5 text-center border-dashed">
                <div class="card-body">
                    <i class="bx bxs-graduation fs-1 text-muted mb-3"></i>
                    <h5>Belum ada Jurusan</h5>
                    <p class="text-muted">Silakan tambahkan kompetensi keahlian sekolah.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $jurusans->links() }}
    </div>
</div>

{{-- MODAL PREVIEW / DETAIL --}}
<div class="modal fade" id="modalPreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title">Detail Jurusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0 text-center">
                <img id="previewBigImg" src="" class="img-fluid rounded mb-3 shadow-sm" style="max-height: 250px; object-fit: contain;">
                <h4 id="previewNama" class="mb-1 text-primary"></h4>
                <span id="previewSingkatan" class="badge bg-label-primary mb-3"></span>
                
                {{-- Area Deskripsi di Preview --}}
                <div class="text-start bg-light p-3 rounded mb-3">
                    <small class="text-muted d-block fw-bold mb-1">Deskripsi:</small>
                    <p id="previewDeskripsi" class="text-dark small mb-0 text-justify"></p>
                </div>

                <p class="text-muted">
                    <i class="bx bx-user-circle me-1"></i> Kepala Jurusan: 
                    <strong><span id="previewKajur">-</span></strong>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREATE --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jurusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.landing.jurusan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-8 mb-3">
                            <label class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_jurusan" placeholder="Rekayasa Perangkat Lunak" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label">Singkatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="singkatan" placeholder="RPL" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kepala Jurusan (Opsional)</label>
                        <input type="text" class="form-control" name="kepala_jurusan" placeholder="Nama Kaprog...">
                    </div>
                    {{-- Input Deskripsi Baru --}}
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Jurusan</label>
                        <textarea class="form-control" name="deskripsi" rows="3" placeholder="Jelaskan tentang jurusan ini..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto / Logo Jurusan <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="gambar" accept="image/*" required>
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

{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jurusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-8 mb-3">
                            <label class="form-label">Nama Jurusan</label>
                            <input type="text" class="form-control" id="editNama" name="nama_jurusan" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label">Singkatan</label>
                            <input type="text" class="form-control" id="editSingkatan" name="singkatan" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kepala Jurusan</label>
                        <input type="text" class="form-control" id="editKajur" name="kepala_jurusan">
                    </div>
                    {{-- Input Deskripsi Edit --}}
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Jurusan</label>
                        <textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti Foto (Opsional)</label>
                        <input type="file" class="form-control" name="gambar" accept="image/*">
                        <div class="form-text">Biarkan kosong jika tidak ingin mengubah foto.</div>
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
    .jurusan-img-wrapper {
        position: relative;
        height: 180px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .jurusan-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    .cursor-pointer { cursor: pointer; }
    .group-action-card:hover .jurusan-img {
        transform: scale(1.05);
    }
    .hover-100:hover { opacity: 1 !important; }
    .border-dashed { border: 2px dashed #d9dee3; }
</style>
@endpush

@push('scripts')
<script>
    // Fungsi Preview Detail (Klik Kartu/Gambar) - Ditambah parameter deskripsi
    function showPreview(nama, singkatan, kajur, foto, deskripsi) {
        document.getElementById('previewNama').textContent = nama;
        document.getElementById('previewSingkatan').textContent = singkatan;
        document.getElementById('previewKajur').textContent = kajur ? kajur : '-';
        document.getElementById('previewBigImg').src = foto;
        
        // Handle Deskripsi Preview
        const deskripsiEl = document.getElementById('previewDeskripsi');
        if(deskripsi && deskripsi !== 'null') {
            deskripsiEl.textContent = deskripsi;
        } else {
            deskripsiEl.textContent = "Tidak ada deskripsi.";
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi Tombol Edit (Klik Ikon Pensil)
        const editButtons = document.querySelectorAll('.btn-edit-action');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.nama;
                const singkatan = this.dataset.singkatan;
                const kajur = this.dataset.kajur;
                const deskripsi = this.dataset.deskripsi; // Ambil data deskripsi

                document.getElementById('editNama').value = nama;
                document.getElementById('editSingkatan').value = singkatan;
                document.getElementById('editKajur').value = kajur;
                document.getElementById('editDeskripsi').value = deskripsi; // Isi textarea

                let updateUrl = "{{ route('admin.landing.jurusan.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                document.getElementById('formEdit').action = updateUrl;
            });
        });
    });
</script>
@endpush

@endsection