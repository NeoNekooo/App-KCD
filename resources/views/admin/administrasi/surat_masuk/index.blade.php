@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Buku Agenda Surat Masuk</h4>

        <div class="card">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">

                {{-- SEARCH --}}
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Cari surat..." style="width:250px"
                        value="{{ request('search') }}">
                    <button class="btn btn-outline-primary">
                        <i class="bx bx-search"></i>
                    </button>
                </form>

                {{-- BUTTON TAMBAH --}}
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bx bx-plus"></i> Catat Surat Baru
                </button>
            </div>

            <div class="table-responsive text-nowrap" style="overflow-x: auto;">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tgl Surat / Terima</th>
                            <th>No. Surat / Asal</th>
                            <th>Perihal</th>
                            <th>File</th>
                            <th style="width:150px; min-width:150px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($suratMasuks as $index => $surat)
                            <tr>
                                <td>{{ $suratMasuks->firstItem() + $index }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d/m/Y') }}<br>
                                    <small class="text-muted">
                                        Terima: {{ \Carbon\Carbon::parse($surat->tanggal_diterima)->format('d/m/Y') }}
                                    </small><br>
                                    <small class="text-muted">Agenda: {{ $surat->no_agenda ?? '-' }}</small>
                                </td>

                                <td>
                                    <strong>{{ $surat->no_surat }}</strong><br>
                                    <span class="badge bg-label-info">{{ $surat->asal_surat }}</span>
                                </td>

                                <td>{{ $surat->perihal }}</td>

                                <td>
                                    @if ($surat->file_surat)
                                        <a href="{{ asset('storage/' . $surat->file_surat) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-file"></i> Lihat
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td style="white-space: nowrap;">
                                    {{-- EDIT --}}
                                    <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                                        data-bs-target="#modalEdit{{ $surat->id }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    {{-- HAPUS --}}
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#modalHapus{{ $surat->id }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- ===================== --}}
                            {{-- MODAL EDIT PER ROW --}}
                            {{-- ===================== --}}
                            <div class="modal fade" id="modalEdit{{ $surat->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">

                                        <form action="{{ route('admin.administrasi.surat-masuk.update', $surat->id) }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Surat Masuk</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body row g-3">

                                                <div class="col-md-4">
                                                    <label class="form-label">Tanggal Surat</label>
                                                    <input type="date" name="tanggal_surat"
                                                        value="{{ $surat->tanggal_surat }}" class="form-control" required>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Tanggal Diterima</label>
                                                    <input type="date" name="tanggal_diterima"
                                                        value="{{ $surat->tanggal_diterima }}" class="form-control"
                                                        required>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">No Agenda</label>
                                                    <input type="text" name="no_agenda" value="{{ $surat->no_agenda }}"
                                                        class="form-control">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">No Surat</label>
                                                    <input type="text" name="no_surat" value="{{ $surat->no_surat }}"
                                                        class="form-control" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Asal Surat</label>
                                                    <input type="text" name="asal_surat"
                                                        value="{{ $surat->asal_surat }}" class="form-control" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Tujuan Disposisi</label>
                                                    <input type="text" name="tujuan_disposisi"
                                                        value="{{ $surat->tujuan_disposisi }}" class="form-control">
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label">Perihal</label>
                                                    <textarea name="perihal" class="form-control" rows="2">{{ $surat->perihal }}</textarea>
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label">Keterangan</label>
                                                    <textarea name="keterangan" class="form-control" rows="2">{{ $surat->keterangan }}</textarea>
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label">Upload File (opsional)</label>
                                                    <input type="file" name="file_surat" class="form-control">
                                                    @if ($surat->file_surat)
                                                        <small class="text-muted">File saat ini:
                                                            {{ $surat->file_surat }}</small>
                                                    @endif
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>

                            {{-- ===================== --}}
                            {{-- MODAL HAPUS --}}
                            {{-- ===================== --}}
                            <div class="modal fade" id="modalHapus{{ $surat->id }}" tabindex="-1">
                                <div class="modal-dialog modal-sm modal-dialog-centered">
                                    <div class="modal-content">

                                        <form action="{{ route('admin.administrasi.surat-masuk.destroy', $surat->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <div class="modal-header">
                                                <h5 class="modal-title text-danger">Hapus Surat</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <p>Yakin ingin menghapus surat ini?</p>
                                                <strong>{{ $surat->no_surat }}</strong>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button class="btn btn-danger">Hapus</button>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">Belum ada surat masuk.</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $suratMasuks->links() }}
            </div>

        </div>
    </div>


    {{-- ===================== --}}
    {{-- MODAL TAMBAH --}}
    {{-- ===================== --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <form action="{{ route('admin.administrasi.surat-masuk.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Catat Surat Masuk Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Tanggal Surat</label>
                            <input type="date" name="tanggal_surat" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tanggal Diterima</label>
                            <input type="date" name="tanggal_diterima" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">No Agenda</label>
                            <input type="text" name="no_agenda" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">No Surat</label>
                            <input type="text" name="no_surat" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Asal Surat</label>
                            <input type="text" name="asal_surat" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tujuan Disposisi</label>
                            <input type="text" name="tujuan_disposisi" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Perihal</label>
                            <textarea name="perihal" class="form-control" rows="2" required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Upload File (opsional)</label>
                            <input type="file" name="file_surat" class="form-control">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
