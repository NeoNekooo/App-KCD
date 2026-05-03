@extends('layouts.admin')

@section('title', 'Mapping Pengawas Pembina')

@section('content')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }
    .btn-pengawas {
        cursor: pointer !important;
        transition: all 0.3s ease;
        border-radius: 12px !important;
        margin: 4px 10px;
        border: 1px solid transparent !important;
    }
    .btn-pengawas:hover {
        background-color: rgba(105, 108, 255, 0.05) !important;
        transform: translateX(5px);
    }
    .btn-pengawas.active {
        background-color: #696cff !important;
        color: white !important;
        box-shadow: 0 4px 15px rgba(105, 108, 255, 0.4);
    }
    .btn-pengawas.active .text-muted, .btn-pengawas.active h6 {
        color: white !important;
    }
    .school-card {
        transition: all 0.2s ease;
        border: 1px solid #eee;
        border-radius: 12px;
    }
    .school-card:hover {
        border-color: #696cff;
        background-color: rgba(105, 108, 255, 0.02);
    }
    .custom-option-content {
        border-radius: 12px !important;
        border: 2px solid #f1f1f1 !important;
    }
    .form-check-input:checked + .custom-option-content {
        border-color: #696cff !important;
        background-color: rgba(105, 108, 255, 0.05) !important;
    }
    .sticky-header {
        position: sticky;
        top: 0;
        z-index: 100;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(5px);
        padding: 15px 0;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0"><span class="text-muted fw-light">PKKS /</span> Mapping Pengawas Pembina</h4>
        <div id="action-buttons" class="d-none">
            <button type="button" class="btn btn-outline-secondary btn-sm me-2" id="btn-select-all">Select All</button>
            <button type="button" class="btn btn-primary" id="btn-save">
                <i class="bx bx-save me-1"></i> Simpan Pemetaan
            </button>
        </div>
    </div>

    <div class="row h-100">
        <!-- Kolom Kiri: Daftar Pengawas -->
        <div class="col-md-4 h-100">
            <div class="card glass-card h-100 border-0 overflow-hidden">
                <div class="card-header border-bottom">
                    <div class="input-group input-group-merge shadow-none border-0 bg-light rounded-pill">
                        <span class="input-group-text bg-transparent border-0"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control bg-transparent border-0" id="search-pengawas" placeholder="Cari nama pengawas...">
                    </div>
                </div>
                <div class="card-body p-0 overflow-auto" style="max-height: 70vh;">
                    <div class="list-group list-group-flush pt-2" id="list-pengawas">
                        @forelse($pengawas as $p)
                        <div class="list-group-item list-group-item-action btn-pengawas d-flex align-items-center mb-1"
                           data-id="{{ $p->id }}"
                           data-name="{{ $p->name }}"
                           data-search="{{ strtolower($p->name) }}">
                            <div class="avatar avatar-md me-3">
                                @if($p->foto)
                                    <img src="{{ asset('storage/'.$p->foto) }}" class="rounded-circle">
                                @else
                                    <span class="avatar-initial rounded-circle bg-label-primary shadow-sm">{{ substr($p->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="w-100">
                                <h6 class="mb-0 fw-bold">{{ $p->name }}</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $p->role }}</small>
                                    <span class="badge badge-center rounded-pill bg-label-secondary small count-badge" data-pid="{{ $p->id }}">0</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="p-5 text-center">
                            <i class="bx bx-user-x text-muted mb-2" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">Belum ada user Pengawas.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Sekolah -->
        <div class="col-md-8">
            <!-- Placeholder -->
            <div class="card glass-card border-0" id="placeholder-mapping">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bx bx-map-alt text-primary" style="font-size: 120px; opacity: 0.1;"></i>
                    </div>
                    <h4 class="fw-bold">Pilih Pengawas Terlebih Dahulu</h4>
                    <p class="text-muted mx-auto" style="max-width: 400px;">
                        Silakan pilih salah satu pengawas di sebelah kiri untuk mengatur daftar sekolah binaan mereka.
                    </p>
                </div>
            </div>

            <!-- Card Sekolah -->
            <div class="card glass-card border-0 d-none" id="card-sekolah">
                <div class="card-header border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded" id="initial-name">?</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" id="selected-pengawas-name">Nama Pengawas</h5>
                            <small class="text-primary">Kelola sekolah binaan untuk pengawas ini</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div class="input-group input-group-merge mb-4 rounded-pill bg-light border-0 px-2">
                        <span class="input-group-text bg-transparent border-0"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control bg-transparent border-0" id="search-sekolah" placeholder="Cari nama sekolah atau NPSN...">
                    </div>

                    <div class="row g-3" id="container-sekolah">
                        @foreach($sekolahs as $s)
                        <div class="col-md-6 item-sekolah" data-sid="{{ $s->sekolah_id }}" data-search="{{ strtolower($s->nama . ' ' . $s->npsn) }}">
                            <div class="form-check custom-option custom-option-basic school-card">
                                <label class="form-check-label custom-option-content shadow-none" for="sekolah-{{ $s->sekolah_id }}">
                                    <input class="form-check-input check-sekolah" type="checkbox" value="{{ $s->sekolah_id }}" id="sekolah-{{ $s->sekolah_id }}">
                                    <span class="custom-option-header pb-0">
                                        <span class="h6 mb-1 fw-bold text-truncate" style="max-width: 250px;">{{ $s->nama }}</span>
                                        <span class="badge bg-label-info mb-2" style="width: fit-content; font-size: 10px;">{{ $s->npsn }}</span>
                                    </span>
                                    <span class="custom-option-body">
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="bx bx-buildings me-1"></i> {{ $s->status_sekolah_str }}
                                            <span class="mx-1">•</span>
                                            <i class="bx bx-map-pin me-1"></i> {{ $s->kecamatan }}
                                        </div>
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
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentPengawasId = null;

        // 1. Search Pengawas
        const searchPengawas = document.getElementById('search-pengawas');
        searchPengawas.addEventListener('keyup', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.btn-pengawas').forEach(item => {
                const text = item.getAttribute('data-search');
                item.style.display = text.includes(val) ? 'flex' : 'none';
            });
        });

        // 2. Klik Pengawas
        document.querySelectorAll('.btn-pengawas').forEach(button => {
            button.addEventListener('click', function() {
                // UI Changes
                document.querySelectorAll('.btn-pengawas').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                currentPengawasId = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                document.getElementById('selected-pengawas-name').innerText = name;
                document.getElementById('initial-name').innerText = name.substring(0, 1);
                document.getElementById('placeholder-mapping').classList.add('d-none');
                document.getElementById('card-sekolah').classList.remove('d-none');
                document.getElementById('action-buttons').classList.remove('d-none');

                // Reset dan Load
                document.querySelectorAll('.check-sekolah').forEach(cb => cb.checked = false);
                document.querySelectorAll('.item-sekolah').forEach(item => item.style.display = 'block');

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
                        
                        // Update badge count
                        const badge = this.querySelector('.count-badge');
                        if(badge) badge.innerText = data.my_schools.length;
                    });
            });
        });

        // 3. Search Sekolah
        const searchSekolahInput = document.getElementById('search-sekolah');
        searchSekolahInput.addEventListener('keyup', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.item-sekolah').forEach(item => {
                const text = item.getAttribute('data-search');
                // Hanya filter yang aslinya memang terlihat (bukan di-hide pengawas lain)
                const isHiddenByOther = item.getAttribute('data-hidden') === 'true';
                if (!isHiddenByOther) {
                    item.style.display = text.includes(val) ? 'block' : 'none';
                }
            });
        });

        // 4. Select All
        document.getElementById('btn-select-all').addEventListener('click', function() {
            const visibleCheckboxes = document.querySelectorAll('.item-sekolah[style*="display: block"] .check-sekolah');
            const allChecked = Array.from(visibleCheckboxes).every(cb => cb.checked);
            visibleCheckboxes.forEach(cb => cb.checked = !allChecked);
            this.innerText = allChecked ? 'Select All' : 'Deselect All';
        });

        // 5. Simpan Pemetaan
        const btnSave = document.getElementById('btn-save');
        btnSave.addEventListener('click', function() {
            const selectedSchools = [];
            document.querySelectorAll('.check-sekolah:checked').forEach(cb => {
                selectedSchools.push(cb.value);
            });

            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

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
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        // Update badge di list kiri
                        const activeBadge = document.querySelector('.btn-pengawas.active .count-badge');
                        if(activeBadge) activeBadge.innerText = selectedSchools.length;
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Simpan Pemetaan');
                }
            });
        });
    });
</script>
@endpush
@endsection
