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
                                <div class="col-md-6 item-sekolah" data-sid="{{ $s->sekolah_id }}" data-search="{{ strtolower($s->nama . ' ' . $s->npsn) }}">
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
    document.addEventListener('DOMContentLoaded', function() {
        let currentPengawasId = null;

        // 1. Klik Pengawas (Pake Vanilla JS biar galak)
        document.querySelectorAll('.btn-pengawas').forEach(button => {
            button.addEventListener('click', function() {
                // Highlight tombol
                document.querySelectorAll('.btn-pengawas').forEach(btn => btn.classList.remove('active', 'bg-label-primary'));
                this.classList.add('active', 'bg-label-primary');
                
                currentPengawasId = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                document.getElementById('selected-pengawas-name').innerText = 'Pengawas: ' + name;
                document.getElementById('placeholder-mapping').classList.add('d-none');
                document.getElementById('card-sekolah').classList.remove('d-none');

                // Reset checkboxes dan tampilkan semua dulu
                document.querySelectorAll('.check-sekolah').forEach(cb => cb.checked = false);
                document.querySelectorAll('.item-sekolah').forEach(item => item.style.display = 'block');

                // Load Mapping via AJAX (Tetap pake fetch/jQuery kalau sudah ada)
                fetch(`/admin/pkks/mapping-pengawas/get/${currentPengawasId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Sembunyikan sekolah yang dipegang pengawas LAIN
                        data.other_schools.forEach(sekolahId => {
                            const item = document.querySelector(`.item-sekolah[data-sid="${sekolahId}"]`);
                            if(item) item.style.display = 'none';
                        });

                        // Centang sekolah yang dipegang pengawas INI
                        data.my_schools.forEach(sekolahId => {
                            const item = document.querySelector(`.item-sekolah[data-sid="${sekolahId}"]`);
                            if(item) {
                                item.style.display = 'block';
                                item.querySelector('.check-sekolah').checked = true;
                            }
                        });
                    });
            });
        });

        // 2. Search Sekolah
        const searchInput = document.getElementById('search-sekolah');
        if(searchInput) {
            searchInput.addEventListener('keyup', function() {
                const val = this.value.toLowerCase();
                document.querySelectorAll('.item-sekolah').forEach(item => {
                    // Hanya filter yang tidak di-hide oleh pengawas lain
                    if (item.style.display !== 'none' || val === '') {
                        const text = item.getAttribute('data-search');
                        item.style.display = text.includes(val) ? 'block' : 'none';
                    }
                });
            });
        }

        // 3. Simpan Pemetaan (Pake jQuery Ajax karena biasanya sudah ada di template buat CSRF)
        const btnSave = document.getElementById('btn-save');
        if(btnSave) {
            btnSave.addEventListener('click', function() {
                if (!currentPengawasId) {
                    Swal.fire('Peringatan', 'Pilih pengawas terlebih dahulu!', 'warning');
                    return;
                }

                const selectedSchools = [];
                document.querySelectorAll('.check-sekolah:checked').forEach(cb => {
                    selectedSchools.push(cb.value);
                });

                const btn = $(this); // Balik ke jQuery dikit buat SweetAlert & Ajax agar simpel
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
        }
    });
</script>
@endpush

<style>
    .btn-pengawas {
        cursor: pointer !important;
    }
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
        background-color: rgba(105, 108, 255, 0.1) !important;
    }
</style>
@endsection
