@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">Landing /</span> Prestasi Sekolah</h4>
            <small class="text-muted">Kelola data kejuaraan dan album dokumentasinya</small>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bx bx-trophy me-1"></i> Tambah Prestasi
        </button>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        @forelse($prestasis as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden group-action-card hover-shadow transition-all">
                
                {{-- Badge Tingkat --}}
                @php
                    $badgeColor = match($item->tingkat) {
                        'Internasional' => 'bg-info',
                        'Nasional'      => 'bg-danger',
                        'Provinsi'      => 'bg-warning',
                        'Kabupaten'     => 'bg-primary',
                        default         => 'bg-secondary',
                    };
                @endphp
                <div class="position-absolute top-0 start-0 px-3 py-1 rounded-bottom-end shadow-sm text-white small fw-bold {{ $badgeColor }}" style="z-index: 5;">
                    {{ $item->tingkat }}
                </div>

                {{-- Cover Image --}}
                <div class="prestasi-img-wrapper bg-light position-relative">
                    <img src="{{ asset('storage/prestasis/covers/'.$item->foto) }}" 
                         class="card-img-top prestasi-img" 
                         alt="{{ $item->judul }}" 
                         onerror="this.src='https://via.placeholder.com/400x250?text=No+Cover'">
                    
                    {{-- Overlay Count --}}
                    <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-gradient-dark text-white d-flex align-items-center">
                        <i class='bx bx-images me-1'></i> 
                        <span class="small fw-bold">{{ $item->items_count ?? 0 }} Foto</span>
                    </div>
                </div>

                <div class="card-body p-3 d-flex flex-column">
                    <div class="mb-2">
                        <small class="text-muted d-block mb-1">
                            <i class="bx bx-calendar me-1"></i> {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}
                        </small>
                        <h5 class="card-title text-primary mb-1 text-truncate" title="{{ $item->judul }}">{{ $item->judul }}</h5>
                        <p class="mb-0 fw-semibold text-dark text-truncate">
                            <i class="bx bx-user-circle me-1"></i> {{ $item->nama_pemenang }}
                        </p>
                    </div>
                    
                    <p class="card-text text-muted small text-truncate mb-3">{{ $item->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                    
                    <div class="mt-auto d-flex gap-2">
                        {{-- TOMBOL UTAMA: KELOLA DOKUMENTASI --}}
                        <a href="{{ route('admin.landing.prestasi.show', $item->id) }}" class="btn btn-sm btn-primary flex-grow-1">
                            <i class='bx bx-images me-1'></i> Dokumentasi
                        </a>

                        {{-- Edit --}}
                        <button type="button" class="btn btn-sm btn-outline-warning btn-icon btn-edit-action"
                                data-bs-toggle="modal" data-bs-target="#modalEdit"
                                data-id="{{ $item->id }}" data-judul="{{ $item->judul }}"
                                data-pemenang="{{ $item->nama_pemenang }}" data-tingkat="{{ $item->tingkat }}"
                                data-deskripsi="{{ $item->deskripsi }}" data-tanggal="{{ $item->tanggal ? $item->tanggal->format('Y-m-d') : '' }}">
                            <i class="bx bx-pencil"></i>
                        </button>

                        {{-- Hapus --}}
                        <form action="{{ route('admin.landing.prestasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus prestasi ini beserta seluruh foto dokumentasinya?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger btn-icon">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card py-5 text-center border-dashed bg-lighter">
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-label-secondary p-3 rounded-circle">
                            <i class="bx bx-trophy fs-1"></i>
                        </span>
                    </div>
                    <h5 class="mb-1">Belum ada Prestasi</h5>
                    <p class="text-muted mb-3">Ayo tambahkan pencapaian siswa dan guru!</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
                        <i class="bx bx-plus me-1"></i> Tambah Prestasi Baru
                    </button>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $prestasis->links() }}</div>
</div>

{{-- MODAL CREATE --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Prestasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.landing.prestasi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul Juara / Prestasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="judul" placeholder="Contoh: Juara 1 Lomba Coding" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Nama Pemenang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_pemenang" placeholder="Nama Siswa/Guru" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                            <select class="form-select" name="tingkat" required>
                                <option value="" disabled selected>- Pilih -</option>
                                <option value="Sekolah">Sekolah</option>
                                <option value="Kecamatan">Kecamatan</option>
                                <option value="Kabupaten">Kabupaten</option>
                                <option value="Provinsi">Provinsi</option>
                                <option value="Nasional">Nasional</option>
                                <option value="Internasional">Internasional</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Singkat</label>
                        <textarea class="form-control" name="deskripsi" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cover Utama <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="foto" accept="image/*" required>
                        <div class="form-text">Upload foto utama/cover untuk prestasi ini.</div>
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
                <h5 class="modal-title">Edit Prestasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul Juara / Prestasi</label>
                        <input type="text" class="form-control" id="editJudul" name="judul" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Nama Pemenang</label>
                            <input type="text" class="form-control" id="editPemenang" name="nama_pemenang" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tingkat</label>
                            <select class="form-select" id="editTingkat" name="tingkat" required>
                                <option value="Sekolah">Sekolah</option>
                                <option value="Kecamatan">Kecamatan</option>
                                <option value="Kabupaten">Kabupaten</option>
                                <option value="Provinsi">Provinsi</option>
                                <option value="Nasional">Nasional</option>
                                <option value="Internasional">Internasional</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="editTanggal" name="tanggal">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Singkat</label>
                        <textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti Cover (Opsional)</label>
                        <input type="file" class="form-control" name="foto" accept="image/*">
                        <div class="form-text">Biarkan kosong jika tidak ingin mengubah cover.</div>
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
    .prestasi-img-wrapper {
        position: relative;
        height: 200px;
        overflow: hidden;
        border-bottom: 1px solid #eee;
    }
    .prestasi-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .group-action-card:hover .prestasi-img {
        transform: scale(1.08);
    }
    .bg-gradient-dark {
        background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    }
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
    .border-dashed { border: 2px dashed #d9dee3; }
    .bg-lighter { background-color: #fcfdfd; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit-action');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                
                document.getElementById('editJudul').value = this.dataset.judul;
                document.getElementById('editPemenang').value = this.dataset.pemenang;
                document.getElementById('editTingkat').value = this.dataset.tingkat;
                document.getElementById('editDeskripsi').value = this.dataset.deskripsi;
                document.getElementById('editTanggal').value = this.dataset.tanggal;

                let updateUrl = "{{ route('admin.landing.prestasi.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                document.getElementById('formEdit').action = updateUrl;
            });
        });
    });
</script>
@endpush

@endsection