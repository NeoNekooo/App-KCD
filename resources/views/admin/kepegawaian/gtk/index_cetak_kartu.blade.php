@extends('layouts.admin')

@section('content')
<style>
    /* Mencegah elemen UI terpotong di dalam tabel */
    .table-responsive {
        overflow: visible !important;
    }
    #photoPreview {
        max-width: 100%;
        max-height: 200px;
        border-radius: 8px;
        display: none;
        margin-top: 10px;
        border: 2px dashed #ddd;
    }
    /* Mempercantik tampilan badge No Photo agar bisa diklik */
    .btn-upload-missing {
        cursor: pointer;
        transition: 0.3s;
    }
    .btn-upload-missing:hover {
        opacity: 0.8;
        transform: scale(1.05);
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kepegawaian /</span> Cetak ID Card</h4>

    {{-- CARD: ATUR BACKGROUND --}}
   {{-- CARD: PENGATURAN BACKGROUND --}}
<div class="card mb-4">
    <div class="card-body d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-image-alt"></i></span>
            </div>
            <div>
                <h6 class="mb-0">Desain Background Kartu</h6>
                <small class="text-muted">Gunakan gambar ukuran 638 x 1011 pixel untuk hasil terbaik.</small>
            </div>
        </div>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalUploadBackground">
            <i class="bx bx-cog me-1"></i> Atur Background
        </button>
    </div>
</div>
    {{-- CARD: DAFTAR PEGAWAI --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-3">Daftar Pegawai Siap Cetak</h5>
            <form action="{{ route('admin.kepegawaian.gtk.index-cetak-kartu') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari Nama / NIP..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="btn-group w-100">
                            <input type="radio" class="btn-check" name="status" id="statusAll" value="" {{ request('status') == '' ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="btn btn-outline-primary" for="statusAll">Semua</label>
                            <input type="radio" class="btn-check" name="status" id="statusGuru" value="Guru" {{ request('status') == 'Guru' ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="btn btn-outline-primary" for="statusGuru">Guru</label>
                            <input type="radio" class="btn-check" name="status" id="statusTendik" value="Tenaga Kependidikan" {{ request('status') == 'Tenaga Kependidikan' ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="btn btn-outline-primary" for="statusTendik">Tendik</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.kepegawaian.gtk.print-all', request()->all()) }}" target="_blank" class="btn btn-dark w-100"><i class="bx bx-printer"></i> Semua</a>
                            <button type="button" id="btnCetakTerpilih" class="btn btn-primary w-100" disabled><i class="bx bx-check-double"></i> Terpilih</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%"><input type="checkbox" class="form-check-input" id="checkAll"></th>
                        <th>Nama Pegawai</th>
                        <th>Identitas</th>
                        <th>Status</th>
                        <th>Foto</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gtks as $gtk)
                    <tr>
                        <td><input type="checkbox" class="form-check-input checkItem" value="{{ $gtk->id }}"></td>
                        <td><strong>{{ $gtk->nama }}</strong></td>
                        <td>{{ $gtk->nip ?? ($gtk->nuptk ?? $gtk->nik) }}</td>
                        <td><span class="badge {{ str_contains($gtk->jenis_ptk_id_str, 'Guru') ? 'bg-label-primary' : 'bg-label-info' }}">{{ str_contains($gtk->jenis_ptk_id_str, 'Guru') ? 'Guru' : 'Tendik' }}</span></td>
                        <td>
                            @if(!empty($gtk->foto))
                                <img src="{{ asset('storage/' . $gtk->foto) }}" class="rounded-circle btn-upload-foto"
                                     style="width: 35px; height: 35px; object-fit: cover; cursor: pointer;"
                                     data-bs-toggle="modal" data-bs-target="#modalUploadFoto"
                                     data-id="{{ $gtk->id }}" data-nama="{{ $gtk->nama }}">
                            @else
                                <span class="badge bg-label-warning btn-upload-missing btn-upload-foto"
                                      data-bs-toggle="modal" data-bs-target="#modalUploadFoto"
                                      data-id="{{ $gtk->id }}" data-nama="{{ $gtk->nama }}">
                                    <i class="bx bx-plus me-1"></i> No Photo
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('admin.kepegawaian.gtk.print-kartu', $gtk->id) }}" target="_blank">
                                        <i class="bx bx-printer me-1"></i> Cetak Kartu
                                    </a>
                                    <a class="dropdown-item btn-upload-foto" href="javascript:void(0);"
                                       data-bs-toggle="modal" data-bs-target="#modalUploadFoto"
                                       data-id="{{ $gtk->id }}" data-nama="{{ $gtk->nama }}">
                                        <i class="bx bx-image-add me-1"></i> Ganti Foto
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $gtks->withQueryString()->links() }}</div>
    </div>
</div>
{{-- MODAL UPLOAD FOTO PEGAWAI --}}
<div class="modal fade" id="modalUploadFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formUploadFotoPegawai" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Foto Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Pegawai</label>
                        <input type="text" id="target_nama" class="form-control" readonly style="background-color: #f5f5f5;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih File Foto</label>
                        <input class="form-control" type="file" name="foto" id="fotoInput" accept="image/*" required>
                        <div class="text-center">
                            <img id="photoPreview" src="#" alt="Preview">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- MODAL UPLOAD BACKGROUND KARTU --}}
<div class="modal fade" id="modalUploadBackground" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.kepegawaian.gtk.upload-background-kartu') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Atur Desain Background</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
        @if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible" role="alert">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
                    <div class="text-center mb-4">
                        <label class="form-label d-block fw-bold mb-3">Preview Saat Ini</label>
                        @if(isset($sekolah) && $sekolah->background_kartu)
                            <img src="{{ asset('storage/' . $sekolah->background_kartu) }}" id="previewBgDisplay" alt="Background" class="img-fluid rounded border shadow-sm" style="max-height: 250px; object-fit: contain;">
                        @else
                            <div id="placeholderBg" class="d-flex align-items-center justify-content-center border rounded bg-light text-muted mx-auto" style="height: 200px; width: 150px;">
                                <small>Belum ada desain</small>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Pilih File Desain Baru</label>
                        <input class="form-control" type="file" name="background_kartu" id="bgInput" accept="image/*" required>
                        <div class="form-text mt-2">Format: JPG/PNG. Rekomendasi: <strong>638 x 1011 px</strong>.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-upload me-1"></i> Simpan Desain</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    // --- 1. LOGIKA MODAL UPLOAD FOTO PEGAWAI ---
    const modalUploadFoto = document.getElementById('modalUploadFoto');
    const formUploadFoto = document.getElementById('formUploadFotoPegawai');

    modalUploadFoto.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const nama = button.getAttribute('data-nama');

        // Update teks nama di modal
        document.getElementById('target_nama').value = nama;

        // UPDATE DYNAMIS ACTION URL
        // Ganti 'ID_PLACEHOLDER' dengan ID asli pegawai
        let urlAction = "{{ route('admin.kepegawaian.gtk.upload_media', ':id') }}";
        formUploadFoto.setAttribute('action', urlAction.replace(':id', id));

        // Reset preview
        document.getElementById('fotoInput').value = '';
        document.getElementById('photoPreview').style.display = 'none';
    });

    // Preview Foto Pegawai
    document.getElementById('fotoInput').onchange = function (evt) {
        const [file] = this.files;
        if (file) {
            const preview = document.getElementById('photoPreview');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    };

    // --- 2. LOGIKA MODAL BACKGROUND ---
    const bgInput = document.getElementById('bgInput');
    if (bgInput) {
        bgInput.onchange = function (evt) {
            const [file] = this.files;
            if (file) {
                const previewBg = document.getElementById('previewBgDisplay');
                const placeholderBg = document.getElementById('placeholderBg');

                if (placeholderBg) placeholderBg.style.display = 'none';
                if (previewBg) {
                    previewBg.src = URL.createObjectURL(file);
                    previewBg.style.display = 'block';
                }
            }
        };
    }
    // 2. CHECKBOX & CETAK TERPILIH
    const checkAll = document.getElementById('checkAll');
    const checkItems = document.querySelectorAll('.checkItem');
    const btnCetakTerpilih = document.getElementById('btnCetakTerpilih');

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            checkItems.forEach(item => item.checked = this.checked);
            togglePrintButton();
        });
    }

    checkItems.forEach(item => {
        item.addEventListener('change', togglePrintButton);
    });

    function togglePrintButton() {
        const anyChecked = Array.from(checkItems).some(item => item.checked);
        btnCetakTerpilih.disabled = !anyChecked;
    }

    btnCetakTerpilih.addEventListener('click', function () {
        const selectedIds = Array.from(checkItems).filter(item => item.checked).map(item => item.value);
        if (selectedIds.length > 0) {
            window.open("{{ route('admin.kepegawaian.gtk.print-all') }}?ids=" + selectedIds.join(','), '_blank');
        }
    });
});
</script>
@endpush
