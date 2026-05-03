@extends('layouts.admin')

@section('title', 'Kelola Soal PKKS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">PKKS / Instrumen /</span> Kelola Soal</h4>
            <p class="text-muted mb-0 small">Susun kompetensi dan butir indikator penilaian</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.pkks.instrumen.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
            <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                <i class="bx bx-upload me-1"></i> Import Excel
            </button>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKompetensi">
                <i class="bx bx-plus-circle me-1"></i> Tambah Kompetensi
            </button>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="card mb-4 shadow-sm border-0 bg-primary bg-opacity-10 overflow-hidden">
        <div class="card-body p-4 position-relative">
            <i class="bx bx-cog position-absolute text-primary opacity-10" style="font-size: 8rem; right: -20px; top: -20px;"></i>
            <div class="d-flex align-items-center">
                <div class="avatar avatar-lg bg-primary rounded me-3 shadow-sm">
                    <span class="avatar-initial"><i class="bx bx-file"></i></span>
                </div>
                <div>
                    <h5 class="mb-1 fw-bold text-primary">{{ $instrumen->nama }}</h5>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-white text-primary shadow-sm rounded-pill"><i class="bx bx-calendar me-1"></i> Tahun {{ $instrumen->tahun }}</span>
                        <span class="badge bg-white text-success shadow-sm rounded-pill"><i class="bx bx-star me-1"></i> Skala 1 - {{ $instrumen->skor_maks }}</span>
                        <span class="text-muted small"><i class="bx bx-list-ul me-1"></i> {{ $instrumen->kompetensis->count() }} Kategori Utama</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @forelse($instrumen->kompetensis as $index => $komp)
    <div class="card mb-5 shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-sm bg-label-primary rounded me-3">
                    <span class="avatar-initial fw-bold">{{ $index + 1 }}</span>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark fs-5">{{ $komp->nama }}</h6>
                    <small class="text-muted">{{ $komp->indikators->count() }} Butir Indikator</small>
                </div>
                <div class="ms-3 border-start ps-3">
                    <button class="btn btn-icon btn-xs btn-label-warning border-0" data-bs-toggle="modal" data-bs-target="#modalEditKompetensi-{{ $komp->id }}"><i class="bx bx-edit-alt"></i></button>
                    <button class="btn btn-icon btn-xs btn-label-danger border-0 ms-1" onclick="confirmDelete('{{ route('admin.pkks.instrumen.kompetensi.destroy', $komp->id) }}', 'Kategori ini dan semua soal di dalamnya akan dihapus!')"><i class="bx bx-trash"></i></button>
                </div>
            </div>
            <button class="btn btn-sm btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahIndikator-{{ $komp->id }}">
                <i class="bx bx-plus me-1"></i> Tambah Soal
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th width="70" class="ps-4">No</th>
                            <th width="450">Kriteria / Indikator Penilaian</th>
                            <th>Bukti Yang Teridentifikasi</th>
                            <th width="120" class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($komp->indikators as $ind)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">{{ $ind->nomor }}</td>
                            <td class="py-3" style="line-height: 1.6;">{{ $ind->kriteria }}</td>
                            <td class="small text-muted py-3">
                                @if($ind->bukti_identifikasi)
                                    <div class="d-flex">
                                        <i class="bx bx-info-circle me-2 text-info mt-1"></i>
                                        <span>{{ $ind->bukti_identifikasi }}</span>
                                    </div>
                                @else
                                    <span class="fst-italic opacity-50">- Tidak ada bukti spesifik -</span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <div class="btn-group">
                                    <button class="btn btn-icon btn-sm btn-label-warning" data-bs-toggle="modal" data-bs-target="#modalEditIndikator-{{ $ind->id }}"><i class="bx bx-edit-alt"></i></button>
                                    <button class="btn btn-icon btn-sm btn-label-danger" onclick="confirmDelete('{{ route('admin.pkks.instrumen.indikator.destroy', $ind->id) }}')"><i class="bx bx-trash"></i></button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit Indikator -->
                        <div class="modal fade" id="modalEditIndikator-{{ $ind->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="{{ route('admin.pkks.instrumen.indikator.update', $ind->id) }}" method="POST" class="modal-content border-0 shadow-lg">
                                    @csrf @method('PUT')
                                    <div class="modal-header bg-warning py-3">
                                        <h5 class="modal-title text-white fw-bold"><i class="bx bx-edit me-2"></i>Edit Soal</h5>
                                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Nomor Urut</label>
                                            <input type="text" name="nomor" class="form-control" value="{{ $ind->nomor }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Kriteria / Soal</label>
                                            <textarea name="kriteria" class="form-control" rows="4" required>{{ $ind->kriteria }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Bukti Identifikasi</label>
                                            <textarea name="bukti_identifikasi" class="form-control" rows="3">{{ $ind->bukti_identifikasi }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top p-3">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning px-4 text-white shadow">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted fst-italic">
                                <i class="bx bx-info-circle mb-2 d-block fs-2"></i>
                                Belum ada indikator untuk kategori ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kompetensi -->
    <div class="modal fade" id="modalEditKompetensi-{{ $komp->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.pkks.instrumen.kompetensi.update', $komp->id) }}" method="POST" class="modal-content border-0 shadow-lg">
                @csrf @method('PUT')
                <div class="modal-header bg-warning py-3">
                    <h5 class="modal-title text-white fw-bold"><i class="bx bx-folder me-2"></i>Edit Kompetensi</h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kompetensi</label>
                        <input type="text" name="nama" class="form-control form-control-lg" value="{{ $komp->nama }}" required>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning px-4 text-white shadow">Update Nama</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Indikator -->
    <div class="modal fade" id="modalTambahIndikator-{{ $komp->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.pkks.instrumen.indikator.store', $komp->id) }}" method="POST" class="modal-content border-0 shadow-lg">
                @csrf
                <div class="modal-header bg-primary py-3">
                    <h5 class="modal-title text-white fw-bold"><i class="bx bx-plus-circle me-2"></i>Tambah Soal Baru</h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor Urut</label>
                        <input type="text" name="nomor" class="form-control" placeholder="Contoh: 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kriteria / Soal</label>
                        <textarea name="kriteria" class="form-control" rows="4" placeholder="Masukkan butir kriteria penilaian..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bukti Identifikasi (Opsional)</label>
                        <textarea name="bukti_identifikasi" class="form-control" rows="3" placeholder="Dokumen atau bukti yang dibutuhkan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary px-4 shadow">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>
    @empty
    <div class="card shadow-none border-dashed text-center py-5 bg-transparent" style="border: 2px dashed #d9dee3 !important;">
        <div class="card-body">
            <i class="bx bx-error-circle text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
            <h5>Belum ada Kompetensi</h5>
            <p class="text-muted mb-4">Klik tombol "Tambah Kompetensi" di atas untuk mulai menyusun soal.</p>
        </div>
    </div>
    @endforelse

    <!-- Modal Tambah Kompetensi -->
    <div class="modal fade" id="modalTambahKompetensi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.pkks.instrumen.kompetensi.store', $instrumen->id) }}" method="POST" class="modal-content border-0 shadow-lg">
                @csrf
                <div class="modal-header bg-primary py-3">
                    <h5 class="modal-title text-white fw-bold"><i class="bx bx-folder-plus me-2"></i>Tambah Kompetensi Baru</h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kompetensi</label>
                        <input type="text" name="nama" class="form-control form-control-lg border-2" placeholder="Contoh: Kompetensi 1: Manajemen" required>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary px-4 shadow">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Import Excel -->
    <div class="modal fade" id="modalImportExcel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.pkks.instrumen.import', $instrumen->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
                @csrf
                <div class="modal-header bg-success py-3">
                    <h5 class="modal-title text-white fw-bold"><i class="bx bx-upload me-2"></i>Import Massal via Excel</h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-label-success border-0 d-flex align-items-start mb-4">
                        <i class="bx bx-spreadsheet me-3 fs-3"></i>
                        <div class="small">
                            Pastikan format file Excel memiliki header kolom berikut agar terbaca sistem: <br>
                            <span class="badge bg-success mt-2">kompetensi</span> 
                            <span class="badge bg-success mt-2">no</span> 
                            <span class="badge bg-success mt-2">kriteria</span> 
                            <span class="badge bg-success mt-2">bukti</span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold">Pilih File Excel</label>
                        <input type="file" name="file" class="form-control form-control-lg border-2" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text mt-2 small">Format yang didukung: .xlsx, .xls, .csv (Maks 2MB)</div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success px-4 shadow">Mulai Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hidden Delete Form --}}
<form id="form-delete" action="" method="POST" class="d-none">
    @csrf @method('DELETE')
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(url, text = 'Data yang dihapus tidak bisa dikembalikan!') {
        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#ff3e1d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('form-delete');
                form.action = url;
                form.submit();
            }
        })
    }
</script>
@endpush
