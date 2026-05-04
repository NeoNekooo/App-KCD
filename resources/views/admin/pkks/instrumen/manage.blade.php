@extends('layouts.admin')

@section('title', 'Kelola Soal PKKS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">PKKS / Instrumen /</span> Kelola Struktur & Soal</h4>
            <p class="text-muted mb-0 small">Susun hirarki kompetensi (Point Utama & Sub) serta butir indikator</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.pkks.instrumen.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
            <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                <i class="bx bx-upload me-1"></i> Import Excel
            </button>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKompetensi">
                <i class="bx bx-plus-circle me-1"></i> Tambah Point Utama
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    @forelse($kompetensis as $parent)
    <div class="mb-5">
        {{-- LEVEL 1: POINT UTAMA --}}
        <div class="d-flex align-items-center mb-3 bg-white p-3 rounded shadow-sm border-start border-primary border-4">
            <div class="avatar avatar-sm bg-primary rounded me-3">
                <span class="avatar-initial fw-bold"><i class="bx bx-layer"></i></span>
            </div>
            <h5 class="mb-0 fw-bold text-primary flex-grow-1">{{ $parent->nama }}</h5>
            <div class="btn-group">
                <button class="btn btn-xs btn-label-primary px-3 me-2" data-bs-toggle="modal" data-bs-target="#modalTambahSub-{{ $parent->id }}">
                    <i class="bx bx-plus me-1"></i> Tambah Sub
                </button>
                <button class="btn btn-icon btn-xs btn-label-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEditKompetensi-{{ $parent->id }}"><i class="bx bx-edit-alt"></i></button>
                <button class="btn btn-icon btn-xs btn-label-danger" onclick="tampilKonfirmasiHapus('{{ route('admin.pkks.instrumen.kompetensi.destroy', $parent->id) }}', 'Point utama ini dan semua sub-kategori di dalamnya akan dihapus!')"><i class="bx bx-trash"></i></button>
            </div>
        </div>

        {{-- LEVEL 2: SUB KATEGORI (MANAJERIAL, DLL) --}}
        @foreach($parent->children as $child)
        <div class="card mb-4 shadow-sm border-0 rounded-3 overflow-hidden ms-lg-5">
            <div class="card-header bg-light py-2 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="bx bx-subdirectory-right text-muted me-2 fs-4"></i>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">{{ $child->nama }}</h6>
                        <small class="text-muted">{{ $child->indikators->count() }} Butir Soal</small>
                    </div>
                    <div class="ms-3 border-start ps-3">
                        <button class="btn btn-icon btn-xs btn-label-warning border-0" data-bs-toggle="modal" data-bs-target="#modalEditKompetensi-{{ $child->id }}"><i class="bx bx-edit-alt"></i></button>
                        <button class="btn btn-icon btn-xs btn-label-danger border-0 ms-1" onclick="tampilKonfirmasiHapus('{{ route('admin.pkks.instrumen.kompetensi.destroy', $child->id) }}')"><i class="bx bx-trash"></i></button>
                    </div>
                </div>
                <button class="btn btn-sm btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahIndikator-{{ $child->id }}">
                    <i class="bx bx-plus me-1"></i> Tambah Soal
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="bg-light">
                            <tr class="small text-uppercase">
                                <th width="70" class="ps-4">No</th>
                                <th width="450">Kriteria / Indikator</th>
                                <th>Bukti Teridentifikasi</th>
                                <th width="120" class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($child->indikators as $ind)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">{{ $ind->nomor }}</td>
                                <td class="py-3" style="line-height: 1.6;">{{ $ind->kriteria }}</td>
                                <td class="small text-muted py-3">{{ $ind->bukti_identifikasi ?: '-' }}</td>
                                <td class="text-center pe-4">
                                    <div class="btn-group">
                                        <button class="btn btn-icon btn-sm btn-label-warning" data-bs-toggle="modal" data-bs-target="#modalEditIndikator-{{ $ind->id }}"><i class="bx bx-edit-alt"></i></button>
                                        <button class="btn btn-icon btn-sm btn-label-danger" onclick="tampilKonfirmasiHapus('{{ route('admin.pkks.instrumen.indikator.destroy', $ind->id) }}')"><i class="bx bx-trash"></i></button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Edit Indikator -->
                            <div class="modal fade" id="modalEditIndikator-{{ $ind->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('admin.pkks.instrumen.indikator.update', $ind->id) }}" method="POST" class="modal-content border-0 shadow-lg">
                                        @csrf @method('PUT')
                                        <div class="modal-header bg-warning py-3">
                                            <h5 class="modal-title text-white fw-bold">Edit Soal</h5>
                                            <button type="button" class="btn-close-custom" data-bs-dismiss="modal">×</button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nomor</label>
                                                <input type="text" name="nomor" class="form-control" value="{{ $ind->nomor }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Kriteria</label>
                                                <textarea name="kriteria" class="form-control" rows="4" required>{{ $ind->kriteria }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Bukti Identifikasi</label>
                                                <textarea name="bukti_identifikasi" class="form-control" rows="3">{{ $ind->bukti_identifikasi }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer p-3">
                                            <button type="submit" class="btn btn-warning w-100 text-white shadow">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted small fst-italic">Belum ada butir soal.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Modal Tambah Soal buat Sub --}}
        <div class="modal fade" id="modalTambahIndikator-{{ $child->id }}" tabindex="-1" aria-hidden="true">
            <form action="{{ route('admin.pkks.instrumen.indikator.store', $child->id) }}" method="POST" class="modal-dialog modal-dialog-centered">
                @csrf
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary py-3">
                        <h5 class="modal-title text-white fw-bold">Tambah Soal ke {{ $child->nama }}</h5>
                        <button type="button" class="btn-close-custom" data-bs-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor</label>
                            <input type="text" name="nomor" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kriteria</label>
                            <textarea name="kriteria" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bukti</label>
                            <textarea name="bukti_identifikasi" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer p-3">
                        <button type="submit" class="btn btn-primary w-100 shadow">Simpan Soal</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Modal Edit Sub-Kategori (Child) --}}
        <div class="modal fade" id="modalEditKompetensi-{{ $child->id }}" tabindex="-1" aria-hidden="true">
            <form action="{{ route('admin.pkks.instrumen.kompetensi.update', $child->id) }}" method="POST" class="modal-dialog modal-dialog-centered">
                @csrf @method('PUT')
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-warning py-3">
                        <h5 class="modal-title text-white fw-bold">Edit Sub-Kategori</h5>
                        <button type="button" class="btn-close-custom" data-bs-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body p-4">
                        <label class="form-label fw-bold">Nama Sub</label>
                        <input type="text" name="nama" class="form-control" value="{{ $child->nama }}" required>
                    </div>
                    <div class="modal-footer p-3">
                        <button type="submit" class="btn btn-warning w-100 text-white shadow">Update Sub</button>
                    </div>
                </div>
            </form>
        </div>
        @endforeach

        {{-- Modal Tambah Sub (L2) --}}
        <div class="modal fade" id="modalTambahSub-{{ $parent->id }}" tabindex="-1" aria-hidden="true">
            <form action="{{ route('admin.pkks.instrumen.kompetensi.store', $instrumen->id) }}" method="POST" class="modal-dialog modal-dialog-centered">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary py-3">
                        <h5 class="modal-title text-white fw-bold">Tambah Sub di {{ $parent->nama }}</h5>
                        <button type="button" class="btn-close-custom" data-bs-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body p-4">
                        <label class="form-label fw-bold">Nama Sub-Kategori</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Manajerial" required>
                    </div>
                    <div class="modal-footer p-3">
                        <button type="submit" class="btn btn-primary w-100 shadow">Simpan Sub</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Modal Edit Point Utama (Parent) --}}
        <div class="modal fade" id="modalEditKompetensi-{{ $parent->id }}" tabindex="-1" aria-hidden="true">
            <form action="{{ route('admin.pkks.instrumen.kompetensi.update', $parent->id) }}" method="POST" class="modal-dialog modal-dialog-centered">
                @csrf @method('PUT')
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-warning py-3">
                        <h5 class="modal-title text-white fw-bold">Edit Point Utama</h5>
                        <button type="button" class="btn-close-custom" data-bs-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body p-4">
                        <label class="form-label fw-bold">Nama Point</label>
                        <input type="text" name="nama" class="form-control form-control-lg" value="{{ $parent->nama }}" required>
                    </div>
                    <div class="modal-footer p-3">
                        <button type="submit" class="btn btn-warning w-100 text-white shadow">Update Point Utama</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
    @empty
    <div class="card shadow-none border-dashed text-center py-5 bg-transparent" style="border: 2px dashed #d9dee3 !important;">
        <div class="card-body">
            <i class="bx bx-error-circle text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
            <h5>Struktur Masih Kosong</h5>
            <p class="text-muted mb-4">Mulai dengan menambah "Point Utama" (Level 1) terlebih dahulu.</p>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKompetensi">
                <i class="bx bx-plus-circle me-1"></i> Tambah Point Utama
            </button>
        </div>
    </div>
    @endforelse

    {{-- Modal Tambah Point Utama (L1) --}}
    <div class="modal fade" id="modalTambahKompetensi" tabindex="-1" aria-hidden="true">
        <form action="{{ route('admin.pkks.instrumen.kompetensi.store', $instrumen->id) }}" method="POST" class="modal-dialog modal-dialog-centered">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary py-3">
                    <h5 class="modal-title text-white fw-bold">Tambah Point Utama (Level 1)</h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal">×</button>
                </div>
                <div class="modal-body p-4">
                    <label class="form-label fw-bold">Nama Point Utama</label>
                    <input type="text" name="nama" class="form-control form-control-lg" placeholder="Contoh: POINT UTAMA I" required>
                </div>
                <div class="modal-footer p-3">
                    <button type="submit" class="btn btn-primary w-100 shadow">Simpan Point Utama</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Modal Import Excel --}}
    <div class="modal fade" id="modalImportExcel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.pkks.instrumen.import', $instrumen->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
                @csrf
                <div class="modal-header bg-success py-3">
                    <h5 class="modal-title text-white fw-bold"><i class="bx bx-upload me-2"></i>Import Hirarki via Excel</h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal">×</button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-label-success border-0 mb-4 p-3">
                        <div class="d-flex mb-2">
                            <i class="bx bx-info-circle me-2 fs-4"></i>
                            <span class="fw-bold">Petunjuk Kolom Excel:</span>
                        </div>
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            <span class="badge bg-success">point_utama</span>
                            <span class="badge bg-success">kompetensi</span>
                            <span class="badge bg-success">no</span>
                            <span class="badge bg-success">kriteria</span>
                            <span class="badge bg-success">bukti</span>
                        </div>
                        <small class="text-muted d-block fst-italic">* Biarkan point_utama/kompetensi kosong jika baris soal masih dalam kategori yang sama.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih File Excel</label>
                        <input type="file" name="file" class="form-control form-control-lg" accept=".xlsx,.xls" required>
                    </div>
                </div>
                <div class="modal-footer p-3 border-top">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 shadow">Upload & Proses</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg text-center">
                <div class="modal-header bg-danger py-3">
                    <h5 class="modal-title text-white fw-bold">Konfirmasi</h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal">×</button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle">
                        <span class="avatar-initial rounded-circle"><i class="bx bx-error fs-1"></i></span>
                    </div>
                    <h5 class="fw-bold mb-2">Yakin Hapus?</h5>
                    <p class="text-muted small" id="teks-konfirmasi">Data akan hilang permanen.</p>
                </div>
                <div class="modal-footer border-top p-3 d-flex justify-content-center">
                    <button type="button" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <form id="form-hapus-fix" action="" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4 shadow">Ya, Hapus!</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-close-custom { background-color: #ffffff; border: none; color: #333333; font-size: 1.5rem; font-weight: bold; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.2s; }
    .btn-close-custom:hover { background-color: #f8f9fa; color: #ff3e1d; transform: scale(1.1); }
</style>
@endsection

@push('scripts')
<script>
    function tampilKonfirmasiHapus(url, text = 'Data ini akan dihapus secara permanen.') {
        const modal = new bootstrap.Modal(document.getElementById('modalKonfirmasiHapus'));
        const form = document.getElementById('form-hapus-fix');
        const teks = document.getElementById('teks-konfirmasi');
        form.action = url;
        teks.innerText = text;
        modal.show();
    }
</script>
@endpush
