@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kesiswaan /</span> Cetak Kartu Massal</h4>

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
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalUploadBackgroundSiswa">
            <i class="bx bx-cog me-1"></i> Atur Background
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row justify-content-between align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Pilih Kelas untuk Mencetak Kartu ID</h5>
            </div>
            <div class="col-md-4">
                <input type="text" id="search-input" class="form-control" placeholder="Cari nama kelas...">
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Kelas</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="rombel-table-body">
                    @forelse ($rombels as $rombel)
                    {{-- Menambahkan class dan atribut data untuk pencarian --}}
                    <tr class="rombel-row" data-nama-rombel="{{ strtolower($rombel->nama) }}">
                        <td><strong>{{ $rombel->nama }}</strong></td>
                        <td class="text-center">
                            <a href="{{ route('admin.kesiswaan.siswa.cetak_massal_show', $rombel->id) }}" class="btn btn-primary" target="_blank">
                                <i class="bx bx-printer me-1"></i> Cetak Kartu Kelas Ini
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center">Tidak ada data kelas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

{{-- MODAL UPLOAD BACKGROUND KARTU SISWA --}}
<div class="modal fade" id="modalUploadBackgroundSiswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.kesiswaan.siswa.upload-background-kartu') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Atur Desain Background Kartu Siswa</h5>
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
                            <img src="{{ asset('storage/' . $sekolah->background_kartu) }}" id="previewBgDisplaySiswa" alt="Background" class="img-fluid rounded border shadow-sm" style="max-height: 250px; object-fit: contain;">
                        @else
                            <div id="placeholderBgSiswa" class="d-flex align-items-center justify-content-center border rounded bg-light text-muted mx-auto" style="height: 200px; width: 150px;">
                                <small>Belum ada desain</small>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Pilih File Desain Baru</label>
                        <input class="form-control" type="file" name="background_kartu" id="bgInputSiswa" accept="image/*" required>
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

@push('scripts')
{{-- === AWAL BAGIAN BARU: SCRIPT PENCARIAN REALTIME & PREVIEW BACKGROUND === --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search-input');
        const tableBody = document.getElementById('rombel-table-body');
        const rombelRows = tableBody.querySelectorAll('.rombel-row');

        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();

            rombelRows.forEach(row => {
                const rombelName = row.getAttribute('data-nama-rombel');

                // Jika nama kelas mengandung kata kunci pencarian, tampilkan barisnya.
                // Jika tidak, sembunyikan.
                if (rombelName.includes(searchTerm)) {
                    row.style.display = ''; // Menampilkan baris
                } else {
                    row.style.display = 'none'; // Menyembunyikan baris
                }
            });
        });

        // PREVIEW BACKGROUND
        const bgInput = document.getElementById('bgInputSiswa');
        if (bgInput) {
            bgInput.onchange = function (evt) {
                const [file] = this.files;
                if (file) {
                    const previewBg = document.getElementById('previewBgDisplaySiswa');
                    const placeholderBg = document.getElementById('placeholderBgSiswa');

                    if (placeholderBg) placeholderBg.style.display = 'none';
                    if (previewBg) {
                        previewBg.src = URL.createObjectURL(file);
                        previewBg.style.display = 'block';
                    }
                }
            };
        }
    });
</script>
@endpush
