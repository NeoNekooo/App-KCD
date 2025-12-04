@extends('layouts.admin')

@section('content')
<style>
    /* Perbaikan agar dropdown di dalam modal tidak tertutup */
    .modal-content .form-select {
        position: relative;
        z-index: 1050;
    }
    .modal-body {
        padding: 1.5rem;
    }
    .modal-dialog {
        margin-top: 2rem;
    }
</style>

<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Rombongan Belajar /</span> Ekstrakurikuler
</h4>


{{-- CARD UTAMA UNTUK TABEL DATA --}}
<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="card-title mb-2 mb-md-0">Data Ekstrakurikuler</h5>
            
            {{-- Tombol Tambah (Trigger Modal) --}}
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> Tambah Ekstrakurikuler
            </button>
        </div>
    </div>
    
    {{-- UPDATE: Ditambahkan class 'p-4' untuk memberikan jarak (gap) di kiri, kanan, atas, dan bawah tabel --}}
    <div class="table-responsive text-nowrap p-4"> 
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Ekskul</th>
                    <th>Pembina</th>
                    <th>Prasarana</th>
                    <th style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($ekskul as $item)
                    <tr>
                        <td>{{ $loop->iteration + $ekskul->firstItem() - 1 }}</td>
                        <td>
                            {{-- Menampilkan Nama dari Relasi ke tabel Master --}}
                            <strong>{{ $item->daftar->nama ?? 'Master Data Terhapus' }}</strong>
                        </td>
                        <td>{{ $item->pembina->nama ?? 'Belum ada pembina' }}</td>
                        <td>{{ $item->prasarana ?? '-' }}</td>
                        <td>
                            {{-- Tombol Edit --}}
                            <button type="button" class="btn btn-sm btn-warning" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal"
                                    data-id="{{ $item->id }}"
                                    {{-- Penting: Kita kirim ID dari tabel master untuk select option --}}
                                    data-master-id="{{ $item->daftar_ekstrakurikuler_id }}" 
                                    data-pembina="{{ $item->pembina_id }}"
                                    data-prasarana="{{ $item->prasarana }}"
                                    onclick="editEkskul(this)">
                                <i class="bx bx-pencil"></i>
                            </button>

                            {{-- Tombol Hapus --}}
                            <button type="button" class="btn btn-sm btn-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->daftar->nama ?? 'Item ini' }}" 
                                    onclick="confirmDelete(this)">
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="text-muted">Belum ada data ekstrakurikuler.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    {{-- <div class="card-footer d-flex justify-content-end">
        {{ $ekskul->links() }}
    </div> --}}
</div>

{{-- ============== MODAL TAMBAH (CREATE) ============== --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Ekstrakurikuler</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form method="POST" action="{{ route('admin.rombel.ekstrakurikuler.store') }}">
                @csrf
                <div class="modal-body">
                    {{-- Input Nama Ekskul (Dropdown dari Master Data) --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                        <select name="daftar_ekstrakurikuler_id" class="form-select" required>
                            <option value="">-- Pilih Ekstrakurikuler --</option>
                            @foreach($daftarEkskul as $daftar)
                                <option value="{{ $daftar->id }}">{{ $daftar->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Input Pembina (Dropdown dari GTK) --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Pembina</label>
                        <select name="pembina_id" class="form-select">
                            <option value="">-- Pilih Pembina --</option>
                            @foreach($pembinas as $pembina)
                                <option value="{{ $pembina->id }}">{{ $pembina->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Input Prasarana --}}
                    <div class="mb-3">
                        <label class="form-label">Prasarana</label>
                        <input type="text" name="prasarana" class="form-control" placeholder="Contoh: Lapangan Basket">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============== MODAL UBAH (EDIT) ============== --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Ekstrakurikuler</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="editForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    {{-- Edit Nama Ekskul --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                        <select name="daftar_ekstrakurikuler_id" id="edit_master_id" class="form-select" required>
                            <option value="">-- Pilih Ekstrakurikuler --</option>
                            @foreach($daftarEkskul as $daftar)
                                <option value="{{ $daftar->id }}">{{ $daftar->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Edit Pembina --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Pembina</label>
                        <select name="pembina_id" id="edit_pembina_id" class="form-select">
                            <option value="">-- Pilih Pembina --</option>
                            @foreach($pembinas as $pembina)
                                <option value="{{ $pembina->id }}">{{ $pembina->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Edit Prasarana --}}
                    <div class="mb-3">
                        <label class="form-label">Prasarana</label>
                        <input type="text" name="prasarana" id="edit_prasarana" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============== MODAL HAPUS (DELETE) ============== --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus ekstrakurikuler <strong id="deleteNama" class="text-danger"></strong>?</p>
                <small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Fungsi Mengisi Data ke Modal Edit
    function editEkskul(button) {
        // Ambil data dari atribut tombol
        const id = button.dataset.id;
        const masterId = button.dataset.masterId; // ID dari tabel daftar_ekstrakurikuler
        const pembina = button.dataset.pembina;
        const prasarana = button.dataset.prasarana;

        // Set URL action form update dinamis
        document.getElementById('editForm').action = `/admin/rombel/ekstrakurikuler/${id}`;

        // Set value ke input/select di modal
        document.getElementById('edit_master_id').value = masterId;
        document.getElementById('edit_pembina_id').value = pembina || ''; // Handle jika null
        document.getElementById('edit_prasarana').value = prasarana || '';
    }

    // Fungsi Mengisi Data ke Modal Delete
    function confirmDelete(button) {
        const id = button.dataset.id;
        const nama = button.dataset.nama;

        document.getElementById('deleteForm').action = `/admin/rombel/ekstrakurikuler/${id}`;
        document.getElementById('deleteNama').textContent = nama;
    }
</script>
@endpush