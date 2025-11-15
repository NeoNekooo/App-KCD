@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Indisipliner / Siswa /</span> Pengaturan</h4>

{{-- Menampilkan notifikasi sukses atau error --}}
<div class="card">
    <div class="card-header border-bottom">
         <div class="nav-align-top">
            <ul class="nav nav-pills" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-jenis-pelanggaran" aria-controls="tab-jenis-pelanggaran" aria-selected="true">
                        <i class="tf-icons bx bx-detail me-1"></i> Jenis Pelanggaran
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sanksi" aria-controls="tab-sanksi" aria-selected="false">
                        <i class="tf-icons bx bx-gavel me-1"></i> Sanksi Pelanggaran
                    </button>
                </li>
            </ul>
        </div>
    </div>
    <div class="card-body pt-4">
        <div class="tab-content p-0">
            <!-- Tab Jenis Pelanggaran -->
            <div class="tab-pane fade show active" id="tab-jenis-pelanggaran" role="tabpanel">
                <div class="d-flex justify-content-end align-items-center mb-4">
                    <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                        <i class="bx bx-plus me-1"></i> Tambah Kategori
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPelanggaran">
                        <i class="bx bx-plus me-1"></i> Tambah Pelanggaran
                    </button>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Nama Pelanggaran</th>
                                <th>Poin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @php $counter = 1; @endphp
                            @forelse($kategoriList->flatMap->pelanggaranPoin as $poin)
                                <tr>
                                    <td>{{ $counter++ }}</td>
                                    <td><span class="badge bg-label-secondary">{{ $poin->kategori->nama }}</span></td>
                                    <td style="white-space: normal;"><strong>{{ $poin->nama }}</strong></td>
                                    <td><span class="badge bg-label-danger rounded-pill">{{ $poin->poin }}</span></td>
                                    <td>
                                        <div class="d-flex">
                                            <button class="btn btn-icon btn-sm btn-outline-primary me-2 edit-poin-btn" 
                                                data-id="{{ $poin->ID }}"
                                                data-kategori-id="{{ $poin->IDpelanggaran_kategori }}"
                                                data-nama="{{ $poin->nama }}"
                                                data-poin="{{ $poin->poin }}"
                                                data-tindakan="{{ $poin->tindakan }}"
                                                data-update-url="{{ route('admin.indisipliner.siswa.pengaturan.poin.update', $poin->ID) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Pelanggaran">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>
                                            <form action="{{ route('admin.indisipliner.siswa.pengaturan.poin.destroy', $poin->ID) }}" method="POST" class="d-inline form-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-sm btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Pelanggaran">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-muted">Tidak ada jenis pelanggaran.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Tab Sanksi -->
            <div class="tab-pane fade" id="tab-sanksi" role="tabpanel">
                 <div class="d-flex justify-content-end align-items-center mb-4">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSanksi">
                        <i class="bx bx-plus me-1"></i> Tambah Sanksi
                    </button>
                </div>
                 <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Rentang Poin</th>
                                <th>Nama Sanksi</th>
                                <th>Penindak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($sanksiList as $key => $sanksi)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td><span class="badge bg-label-warning">{{ $sanksi->poin_min }} - {{ $sanksi->poin_max }}</span></td>
                                <td style="white-space: normal;"><strong>{{ $sanksi->nama }}</strong></td>
                                <td style="white-space: normal;">{{ str_replace('|', ', ', $sanksi->penindak) }}</td>
                                <td>
                                    <div class="d-flex">
                                        <button class="btn btn-icon btn-sm btn-outline-primary me-2 edit-sanksi-btn"
                                            data-id="{{ $sanksi->ID }}"
                                            data-poin_min="{{ $sanksi->poin_min }}"
                                            data-poin_max="{{ $sanksi->poin_max }}"
                                            data-nama="{{ $sanksi->nama }}"
                                            data-penindak="{{ $sanksi->penindak }}"
                                            data-update-url="{{ route('admin.indisipliner.siswa.pengaturan.sanksi.update', $sanksi->ID) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Sanksi">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                        <form action="{{ route('admin.indisipliner.siswa.pengaturan.sanksi.destroy', $sanksi->ID) }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-sm btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Sanksi">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <p class="text-muted">Tidak ada data sanksi.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- MODALS TAMBAH -->
@include('admin.indisipliner.siswa.pengaturan.partials.modal-tambah')

<!-- MODALS EDIT -->
@include('admin.indisipliner.siswa.pengaturan.partials.modal-edit')

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi Tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Konfirmasi Hapus
        const deleteForms = document.querySelectorAll('.form-delete');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                    this.submit();
                }
            });
        });
        
        // Logika Modal Edit Pelanggaran (Poin)
        const editPoinModal = document.getElementById('modalEditPelanggaran');
        const editPoinButtons = document.querySelectorAll('.edit-poin-btn');
        editPoinButtons.forEach(button => {
            button.addEventListener('click', function () {
                const modal = new bootstrap.Modal(editPoinModal);
                const form = editPoinModal.querySelector('form');
                
                form.action = this.dataset.updateUrl;
                form.querySelector('select[name="IDpelanggaran_kategori"]').value = this.dataset.kategoriId;
                form.querySelector('textarea[name="nama"]').value = this.dataset.nama;
                form.querySelector('input[name="poin"]').value = this.dataset.poin;
                form.querySelector('textarea[name="tindakan"]').value = this.dataset.tindakan;
                
                modal.show();
            });
        });

        // Logika Modal Edit Sanksi
        const editSanksiModal = document.getElementById('modalEditSanksi');
        const editSanksiButtons = document.querySelectorAll('.edit-sanksi-btn');
        editSanksiButtons.forEach(button => {
            button.addEventListener('click', function () {
                const modal = new bootstrap.Modal(editSanksiModal);
                const form = editSanksiModal.querySelector('form');

                form.action = this.dataset.updateUrl;
                form.querySelector('input[name="poin_min"]').value = this.dataset.poin_min;
                form.querySelector('input[name="poin_max"]').value = this.dataset.poin_max;
                form.querySelector('textarea[name="nama"]').value = this.dataset.nama;
                form.querySelector('input[name="penindak"]').value = this.dataset.penindak;
                
                modal.show();
            });
        });
    });
</script>
@endpush

