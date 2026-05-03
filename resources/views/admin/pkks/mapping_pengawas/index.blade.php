@extends('layouts.admin')

@section('title', 'Mapping Pengawas Pembina')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">PKKS /</span> Mapping Pengawas Pembina</h4>

    <div class="row">
        <!-- Kolom Kiri: Daftar Pengawas -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Pengawas</h5>
                    <small class="text-muted float-end">Pilih satu pengawas</small>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="list-pengawas">
                        @forelse($pengawas as $p)
                        <a href="javascript:void(0);" 
                           class="list-group-item list-group-item-action border-0 d-flex align-items-center btn-pengawas"
                           data-id="{{ $p->id }}"
                           data-name="{{ $p->name }}">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($p->name, 0, 1) }}</span>
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-0">{{ $p->name }}</h6>
                                </div>
                                <small class="text-muted">{{ $p->role }}</small>
                            </div>
                        </a>
                        @empty
                        <div class="p-4 text-center">
                            <p class="text-muted mb-0">Belum ada user dengan jabatan Pengawas.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Sekolah -->
        <div class="col-md-8">
            <div class="card mb-4 d-none" id="card-sekolah">
                <div class="card-header sticky-top bg-white border-bottom d-flex justify-content-between align-items-center" style="z-index: 10;">
                    <div>
                        <h5 class="mb-0">Daftar Sekolah Binaan</h5>
                        <small class="text-primary" id="selected-pengawas-name"></small>
                    </div>
                    <button type="button" class="btn btn-primary" id="btn-save">
                        <i class="bx bx-save me-1"></i> Simpan Pemetaan
                    </button>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3">
                        <div class="col-12 mb-2">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input type="text" class="form-control" id="search-sekolah" placeholder="Cari nama sekolah atau NPSN...">
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="row" id="container-sekolah">
                                @foreach($sekolahs as $s)
                                <div class="col-md-6 item-sekolah" data-search="{{ strtolower($s->nama . ' ' . $s->npsn) }}">
                                    <div class="form-check custom-option custom-option-basic">
                                        <label class="form-check-label custom-option-content" for="sekolah-{{ $s->sekolah_id }}">
                                            <input class="form-check-input check-sekolah" type="checkbox" value="{{ $s->sekolah_id }}" id="sekolah-{{ $s->sekolah_id }}">
                                            <span class="custom-option-header">
                                                <span class="h6 mb-0">{{ $s->nama }}</span>
                                                <small class="text-muted">{{ $s->npsn }}</small>
                                            </span>
                                            <span class="custom-option-body">
                                                <small>{{ $s->status_sekolah_str }} - {{ $s->kecamatan }}</small>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Placeholder -->
            <div class="card mb-4" id="placeholder-mapping">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="bx bx-user-pin text-primary" style="font-size: 100px; opacity: 0.2;"></i>
                    </div>
                    <h5>Pilih Pengawas Terlebih Dahulu</h5>
                    <p class="text-muted">Silakan klik salah satu nama pengawas di kolom kiri untuk mulai memetakan sekolah binaan.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let currentPengawasId = null;

        // 1. Klik Pengawas
        $('.btn-pengawas').on('click', function() {
            $('.btn-pengawas').removeClass('active bg-label-primary');
            $(this).addClass('active bg-label-primary');
            
            currentPengawasId = $(this).data('id');
            const name = $(this).data('name');

            $('#selected-pengawas-name').text('Pengawas: ' + name);
            $('#placeholder-mapping').addClass('d-none');
            $('#card-sekolah').removeClass('d-none');

            // Reset checkboxes
            $('.check-sekolah').prop('checked', false);

            // Load Mapping via AJAX
            $.get(`/admin/pkks/mapping-pengawas/get/${currentPengawasId}`, function(data) {
                data.forEach(function(sekolahId) {
                    $(`#sekolah-${sekolahId}`).prop('checked', true);
                });
            });
        });

        // 2. Search Sekolah
        $('#search-sekolah').on('keyup', function() {
            const val = $(this).val().toLowerCase();
            $('.item-sekolah').each(function() {
                const text = $(this).data('search');
                $(this).toggle(text.includes(val));
            });
        });

        // 3. Simpan Pemetaan
        $('#btn-save').on('click', function() {
            if (!currentPengawasId) return;

            const selectedSchools = [];
            $('.check-sekolah:checked').each(function() {
                selectedSchools.push($(this).val());
            });

            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

            $.ajax({
                url: '{{ route("admin.pkks.mapping-pengawas.update") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    pengawas_id: currentPengawasId,
                    sekolah_ids: selectedSchools
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', response.message, 'success');
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Simpan Pemetaan');
                }
            });
        });
    });
</script>
@endpush

<style>
    .custom-option-content {
        padding: 0.75rem !important;
        cursor: pointer;
    }
    .custom-option-header {
        display: flex;
        flex-direction: column;
        margin-bottom: 0 !important;
    }
    .list-group-item.active {
        color: #696cff;
    }
</style>
@endsection
