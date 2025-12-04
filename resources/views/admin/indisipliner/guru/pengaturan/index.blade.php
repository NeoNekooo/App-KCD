@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Indisipliner / Guru /</span> Pengaturan</h4>

<div class="card">
    <div class="card-header border-bottom">
        <div class="nav-align-top">
            <ul class="nav nav-pills" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-jenis-pelanggaran" role="tab">
                        <i class="tf-icons bx bx-detail me-1"></i> Jenis Pelanggaran
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-sanksi" role="tab">
                        <i class="tf-icons bx bx-gavel me-1"></i> Sanksi Pelanggaran
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <div class="card-body pt-4">
        <div class="tab-content p-0">
            {{-- ============================ TAB PELANGGARAN ============================ --}}
            <div class="tab-pane fade show active" id="tab-jenis-pelanggaran" role="tabpanel">

                <div class="d-flex justify-content-end align-items-center mb-4">
                    <button class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                        <i class="bx bx-plus me-1"></i> Tambah Kategori
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPelanggaran">
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
                        <tbody>
                            @php $counter = 1; @endphp
                            @forelse($kategoriList->flatMap->pelanggaranPoinGtk as $poin)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td><span class="badge bg-label-secondary">{{ $poin->kategoriGtk->nama }}</span></td>
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
                                            data-update-url="{{ route('admin.indisipliner.guru.pengaturan.poin.update', $poin->ID) }}"
                                            data-bs-toggle="tooltip" title="Edit Pelanggaran">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>

                                        {{-- 🔥 tombol hapus baru --}}
                                        <button type="button"
                                            class="btn btn-icon btn-sm btn-outline-danger btn-hapus"
                                            data-url="{{ route('admin.indisipliner.guru.pengaturan.poin.destroy', $poin->ID) }}"
                                            data-bs-toggle="tooltip" title="Hapus Pelanggaran">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Tidak ada jenis pelanggaran.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ============================ TAB SANKSI ============================ --}}
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
                        <tbody>
                            @forelse($sanksiList as $key => $sanksi)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td><span class="badge bg-label-warning">{{ $sanksi->poin_min }} - {{ $sanksi->poin_max }}</span></td>
                                <td><strong>{{ $sanksi->nama }}</strong></td>
                                <td>{{ str_replace('|', ', ', $sanksi->penindak) }}</td>
                                <td>
                                    <div class="d-flex">
                                        <button class="btn btn-icon btn-sm btn-outline-primary me-2 edit-sanksi-btn"
                                            data-id="{{ $sanksi->ID }}"
                                            data-poin_min="{{ $sanksi->poin_min }}"
                                            data-poin_max="{{ $sanksi->poin_max }}"
                                            data-nama="{{ $sanksi->nama }}"
                                            data-penindak="{{ $sanksi->penindak }}"
                                            data-update-url="{{ route('admin.indisipliner.guru.pengaturan.sanksi.update', $sanksi->ID) }}"
                                            data-bs-toggle="tooltip" title="Edit Sanksi">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>

                                        {{-- 🔥 tombol hapus baru --}}
                                        <button type="button"
                                            class="btn btn-icon btn-sm btn-outline-danger btn-hapus"
                                            data-url="{{ route('admin.indisipliner.guru.pengaturan.sanksi.destroy', $sanksi->ID) }}"
                                            data-bs-toggle="tooltip" title="Hapus Sanksi">
                                            <i class="bx bx-trash"></i>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Tidak ada data sanksi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

@include('admin.indisipliner.guru.pengaturan.partials.modal-tambah')
@include('admin.indisipliner.guru.pengaturan.partials.modal-edit')

{{-- 🔥 modal hapus global --}}
@include('admin.indisipliner.guru.pengaturan.partials.modal-hapus')

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Tooltip
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el))

    // ========================= Modal Edit Pelanggaran =========================
    document.querySelectorAll('.edit-poin-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const modal = new bootstrap.Modal(document.getElementById('modalEditPelanggaran'));
            const form = modal._element.querySelector('form');

            form.action = this.dataset.updateUrl;
            form.querySelector('select[name="IDpelanggaran_kategori"]').value = this.dataset.kategoriId;
            form.querySelector('textarea[name="nama"]').value = this.dataset.nama;
            form.querySelector('input[name="poin"]').value = this.dataset.poin;
            form.querySelector('textarea[name="tindakan"]').value = this.dataset.tindakan;

            modal.show();
        });
    });

    // ========================= Modal Edit Sanksi =========================
    document.querySelectorAll('.edit-sanksi-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const modal = new bootstrap.Modal(document.getElementById('modalEditSanksi'));
            const form = modal._element.querySelector('form');

            form.action = this.dataset.updateUrl;
            form.querySelector('input[name="poin_min"]').value = this.dataset.poin_min;
            form.querySelector('input[name="poin_max"]').value = this.dataset.poin_max;
            form.querySelector('textarea[name="nama"]').value = this.dataset.nama;
            form.querySelector('input[name="penindak"]').value = this.dataset.penindak;

            modal.show();
        });
    });

    // ========================= Modal Hapus Global =========================
    document.querySelectorAll('.btn-hapus').forEach(btn => {
        btn.addEventListener('click', function () {
            const modal = new bootstrap.Modal(document.getElementById('modalHapus'));
            const form = document.getElementById('formHapusGlobal');

            form.action = this.dataset.url;
            modal.show();
        });
    });

    // ========================= Aktifkan Tab Bedasarkan Hash =========================
    if (window.location.hash) {
        var hash = window.location.hash;
        var triggerEl = document.querySelector('button[data-bs-toggle="tab"][data-bs-target="' + hash + '"]');
        if (triggerEl) new bootstrap.Tab(triggerEl).show();
    }
});
</script>
@endpush
