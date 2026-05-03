@extends('layouts.admin')

@section('title', 'Kelola Soal PKKS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">
            <span class="text-muted fw-light">PKKS / Instrumen /</span> Kelola Soal
        </h4>
        <div>
            <a href="{{ route('admin.pkks.instrumen.index') }}" class="btn btn-outline-secondary btn-sm me-2">Kembali</a>
            <button class="btn btn-success btn-sm me-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                <i class="bx bx-upload me-1"></i> Import Excel
            </button>
            <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKompetensi">
                <i class="bx bx-plus me-1"></i> Tambah Kompetensi
            </button>
        </div>
    </div>

    <div class="card mb-4 shadow-none border bg-primary bg-opacity-10 border-primary">
        <div class="card-body py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-primary">{{ $instrumen->nama }} (Tahun {{ $instrumen->tahun }})</h5>
            <span class="badge bg-primary">Skala Penilaian: 1 - {{ $instrumen->skor_maks }}</span>
        </div>
    </div>

    @forelse($instrumen->kompetensis as $komp)
    <div class="card mb-4 shadow-none border overflow-hidden">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3 border-bottom">
            <div class="d-flex align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="bx bx-folder me-2 text-primary"></i>{{ $komp->nama }}</h6>
                <div class="ms-3">
                    <button class="btn btn-xs btn-outline-warning border-0" data-bs-toggle="modal" data-bs-target="#modalEditKompetensi-{{ $komp->id }}"><i class="bx bx-edit-alt"></i></button>
                    <button class="btn btn-xs btn-outline-danger border-0" onclick="confirmDelete('{{ route('admin.pkks.instrumen.kompetensi.destroy', $komp->id) }}', 'Kategori ini dan semua soal di dalamnya akan dihapus!')"><i class="bx bx-trash"></i></button>
                </div>
            </div>
            <button class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahIndikator-{{ $komp->id }}">
                <i class="bx bx-plus me-1"></i> Tambah Soal
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Kriteria / Indikator</th>
                            <th>Bukti Identifikasi</th>
                            <th width="100" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($komp->indikators as $ind)
                        <tr>
                            <td class="fw-bold text-primary">{{ $ind->nomor }}</td>
                            <td>{{ $ind->kriteria }}</td>
                            <td class="small text-muted">{{ $ind->bukti_identifikasi ?? '-' }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-icon btn-xs btn-label-warning" data-bs-toggle="modal" data-bs-target="#modalEditIndikator-{{ $ind->id }}"><i class="bx bx-edit-alt"></i></button>
                                    <button class="btn btn-icon btn-xs btn-label-danger" onclick="confirmDelete('{{ route('admin.pkks.instrumen.indikator.destroy', $ind->id) }}')"><i class="bx bx-trash"></i></button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit Indikator -->
                        <div class="modal fade" id="modalEditIndikator-{{ $ind->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.pkks.instrumen.indikator.update', $ind->id) }}" method="POST" class="modal-content">
                                    @csrf @method('PUT')
                                    <div class="modal-header border-bottom py-3">
                                        <h5 class="modal-title">Edit Soal</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body pt-4">
                                        <div class="mb-3">
                                            <label class="form-label">Nomor Urut</label>
                                            <input type="text" name="nomor" class="form-control" value="{{ $ind->nomor }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Kriteria / Soal</label>
                                            <textarea name="kriteria" class="form-control" rows="3" required>{{ $ind->kriteria }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Bukti Identifikasi</label>
                                            <textarea name="bukti_identifikasi" class="form-control" rows="2">{{ $ind->bukti_identifikasi }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top py-3">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada indikator.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kompetensi -->
    <div class="modal fade" id="modalEditKompetensi-{{ $komp->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.pkks.instrumen.kompetensi.update', $komp->id) }}" method="POST" class="modal-content">
                @csrf @method('PUT')
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title">Edit Nama Kompetensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    <div class="mb-3">
                        <label class="form-label">Nama Kompetensi</label>
                        <input type="text" name="nama" class="form-control" value="{{ $komp->nama }}" required>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Indikator -->
    <div class="modal fade" id="modalTambahIndikator-{{ $komp->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.pkks.instrumen.indikator.store', $komp->id) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title">Tambah Soal ke {{ $komp->nama }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    <div class="mb-3">
                        <label class="form-label">Nomor Urut</label>
                        <input type="text" name="nomor" class="form-control" placeholder="Contoh: 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kriteria / Soal</label>
                        <textarea name="kriteria" class="form-control" rows="3" placeholder="Masukkan butir kriteria penilaian..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bukti Identifikasi (Opsional)</label>
                        <textarea name="bukti_identifikasi" class="form-control" rows="2" placeholder="Dokumen atau bukti yang dibutuhkan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <i class="bx bx-error-circle text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
        <h5>Belum ada Kompetensi</h5>
        <p class="text-muted">Klik tombol "Tambah Kompetensi" untuk mulai menyusun soal.</p>
    </div>
    @endforelse

    <!-- Modal Tambah Kompetensi -->
    <div class="modal fade" id="modalTambahKompetensi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.pkks.instrumen.kompetensi.store', $instrumen->id) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title">Tambah Kompetensi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    <div class="mb-3">
                        <label class="form-label">Nama Kompetensi</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Kompetensi 1: Manajemen" required>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Import Excel -->
    <div class="modal fade" id="modalImportExcel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.pkks.instrumen.import', $instrumen->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title">Import Soal dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    <div class="alert alert-info d-flex align-items-start mb-3">
                        <i class="bx bx-info-circle me-2 mt-1"></i>
                        <div class="small">
                            Pastikan format file Excel memiliki header: <br>
                            <strong>kompetensi | no | kriteria | bukti</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Mulai Import</button>
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
            cancelButtonText: 'Batal'
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
