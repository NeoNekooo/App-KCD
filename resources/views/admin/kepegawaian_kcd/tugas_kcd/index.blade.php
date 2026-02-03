@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Penugasan Pegawai (Internal KCD)</h4>
            <span class="text-muted small">Atur siapa yang memegang layanan verifikasi</span>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bx bx-plus me-1"></i> Tambah Tugas
        </button>
    </div>

    {{-- Tabel Data --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Nama Pegawai</th>
                        <th>Jabatan/Tugas</th>
                        <th>Akses Layanan</th>
                        {{-- <th>No. SK</th> --}} {{-- Hide Column Header --}}
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tugas as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->pegawai->nama ?? 'Pegawai Hilang' }}</div>
                            <small class="text-muted">{{ $item->pegawai->nip ?? '-' }}</small>
                        </td>
                        <td>{{ $item->nama_tugas }}</td>
                        
                        {{-- Kolom Kategori Layanan --}}
                        <td>
                            @if(!empty($item->kategori_layanan))
                                @foreach($item->kategori_layanan as $kategori)
                                    <span class="badge bg-label-primary me-1 mb-1">
                                        {{ strtoupper(str_replace('-', ' ', $kategori)) }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted small fst-italic">- Tugas Umum -</span>
                            @endif
                        </td>
                        
                        {{-- <td>{{ $item->no_sk ?? '-' }}</td> --}} {{-- Hide Column Data --}}
                        <td class="text-center">
                            <form action="{{ route('admin.kepegawaian.tugas-kcd.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus penugasan ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-label-danger text-danger">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada pegawai yang diberi tugas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH TUGAS --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom pb-3">
                <h5 class="modal-title fw-bold">Input Tugas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kepegawaian.tugas-kcd.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    
                    {{-- 1. Pilih Pegawai --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Pegawai KCD</label>
                        <select name="pegawai_kcd_id" class="form-select" required>
                            <option value="">-- Cari Nama Pegawai --</option>
                            @foreach($pegawais as $p)
                                <option value="{{ $p->id }}">{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Nama Tugas --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Jabatan Tim</label>
                        <input type="text" name="nama_tugas" class="form-control" placeholder="Contoh: Verifikator E-Kinerja" required>
                    </div>

                    {{-- 3. PILIH KATEGORI LAYANAN (PENTING) --}}
                    <div class="mb-3 p-3 bg-label-primary rounded">
                        <label class="form-label fw-bold text-primary mb-2">Hak Akses Layanan (Bisa lebih dari satu)</label>
                        @foreach($listKategori as $slug => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="kategori_layanan[]" value="{{ $slug }}" id="kategori_{{ $slug }}">
                                <label class="form-check-label" for="kategori_{{ $slug }}">
                                    {{ $label }}
                                </label>
                            </div>
                        @endforeach
                        <div class="form-text mt-2 small text-dark">
                            <i class='bx bx-info-circle'></i> Jika dipilih, pegawai ini akan memiliki akses khusus untuk memverifikasi layanan tersebut. Kosongkan jika ini tugas umum.
                        </div>
                    </div>

                    {{-- 4. Lainnya --}}
                    {{-- Nomor SK Hidden --}}
                    {{-- <div class="mb-3">
                        <label class="form-label">Nomor SK</label>
                        <input type="text" name="no_sk" class="form-control" placeholder="No. Surat Perintah">
                    </div> --}}

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Opsional"></textarea>
                    </div>

                </div>
                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Penugasan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection