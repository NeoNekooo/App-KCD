@extends('layouts.admin')

@section('title', 'Mapping Pengawas Pembina')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0"><span class="text-muted fw-light">PKKS /</span> Mapping Pengawas Pembina</h4>
        <div id="action-buttons" class="d-none">
            <button type="button" class="btn btn-outline-primary btn-sm me-2" id="btn-select-all">Pilih Semua</button>
            <button type="button" class="btn btn-primary" id="btn-save">
                <i class="bx bx-save me-1"></i> Simpan Pemetaan
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Daftar Pengawas -->
        <div class="col-md-4">
            <div class="card mb-4 shadow-none border">
                <div class="card-header border-bottom">
                    <div class="input-group input-group-merge border rounded-pill px-2">
                        <span class="input-group-text bg-transparent border-0"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control bg-transparent border-0" id="search-pengawas" placeholder="Cari pengawas...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="list-pengawas">
                        @forelse($pengawas as $p)
                        <a href="javascript:void(0);" 
                           class="list-group-item list-group-item-action btn-pengawas border-0 d-flex align-items-center py-3 px-4"
                           data-id="{{ $p->id }}"
                           data-name="{{ $p->name }}"
                           data-search="{{ strtolower($p->name) }}">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($p->name, 0, 1) }}</span>
                            </div>
                            <div class="w-100 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-truncate" style="max-width: 150px;">{{ $p->name }}</h6>
                                    <span class="badge bg-label-secondary count-badge rounded-pill" data-pid="{{ $p->id }}">0</span>
                                </div>
                                <small class="text-muted text-truncate d-block">{{ $p->role }}</small>
                            </div>
                        </a>
                        @empty
                        <div class="p-4 text-center">
                            <p class="text-muted mb-0 small">Belum ada data pengawas.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Sekolah -->
        <div class="col-md-8">
            <!-- Placeholder -->
            <div class="card shadow-none border text-center py-5" id="placeholder-mapping">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bx bx-pointer text-primary" style="font-size: 5rem; opacity: 0.1;"></i>
                    </div>
                    <h5>Pilih pengawas di kolom kiri</h5>
                    <p class="text-muted">Kelola pemetaan sekolah binaan untuk pengawas yang dipilih.</p>
                </div>
            </div>

            <!-- Card Sekolah -->
            <div class="card shadow-none border d-none" id="card-sekolah">
                <div class="card-header border-bottom py-3 d-flex align-items-center">
                    <div class="avatar avatar-sm bg-label-primary me-3">
                        <span class="avatar-initial rounded" id="initial-name">?</span>
                    </div>
                    <h5 class="mb-0 fw-bold" id="selected-pengawas-name">Nama Pengawas</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="input-group input-group-merge mb-4 rounded-pill border px-2">
                        <span class="input-group-text bg-transparent border-0"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control bg-transparent border-0" id="search-sekolah" placeholder="Cari nama sekolah atau NPSN...">
                    </div>

                    <div class="row g-3" id="container-sekolah">
                        @foreach($sekolahs as $s)
                        <div class="col-md-6 item-sekolah" data-sid="{{ $s->sekolah_id }}" data-search="{{ strtolower($s->nama . ' ' . $s->npsn) }}">
                            <div class="form-check custom-option custom-option-basic">
                                <label class="form-check-label custom-option-content" for="sekolah-{{ $s->sekolah_id }}">
                                    <input class="form-check-input check-sekolah" type="checkbox" value="{{ $s->sekolah_id }}" id="sekolah-{{ $s->sekolah_id }}">
                                    <span class="custom-option-header pb-0">
                                        <span class="h6 mb-1 text-truncate" style="max-width: 220px;">{{ $s->nama }}</span>
                                        <small class="text-muted">{{ $s->npsn }}</small>
                                    </span>
                                    <span class="custom-option-body">
                                        <small class="text-muted d-block text-truncate">
                                            {{ $s->status_sekolah_str }} • {{ $s->kecamatan }}
                                        </small>
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

<style>
    .btn-pengawas.active {
        background-color: #696cff !important;
        color: #fff !important;
    }
    .btn-pengawas.active .text-muted, .btn-pengawas.active h6, .btn-pengawas.active .count-badge {
        color: #fff !important;
    }
    .btn-pengawas.active .count-badge {
        background-color: rgba(255,255,255,0.2) !important;
    }
    .custom-option-content {
        padding: 0.75rem !important;
    }
    .custom-option-header {
        margin-bottom: 0 !important;
    }
    .item-sekolah {
        transition: all 0.2s ease;
    }
    .btn-pengawas {
        cursor: pointer !important;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentPengawasId = null;

        // 1. Search Pengawas
        const searchPengawas = document.getElementById('search-pengawas');
        if(searchPengawas) {
            searchPengawas.addEventListener('keyup', function() {
                const val = this.value.toLowerCase();
                document.querySelectorAll('.btn-pengawas').forEach(item => {
                    const text = item.getAttribute('data-search');
                    item.style.display = text.includes(val) ? 'flex' : 'none';
                });
            });
        }

        // 2. Klik Pengawas
        document.querySelectorAll('.btn-pengawas').forEach(button => {
            button.addEventListener('click', function() {
                // TOGGLE LOGIC: Kalau diklik lagi yang sudah aktif, batalkan pilihan
                if (this.classList.contains('active')) {
                    this.classList.remove('active');
                    currentPengawasId = null;
                    document.getElementById('placeholder-mapping').classList.remove('d-none');
                    document.getElementById('card-sekolah').classList.add('d-none');
                    document.getElementById('action-buttons').classList.add('d-none');
                    return;
                }

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
                        data.other_schools.forEach(sekolahId => {
                            const item = document.querySelector(`.item-sekolah[data-sid="${sekolahId}"]`);
                            if(item) item.style.display = 'none';
                        });

                        data.my_schools.forEach(sekolahId => {
                            const item = document.querySelector(`.item-sekolah[data-sid="${sekolahId}"]`);
                            if(item) {
                                item.style.display = 'block';
                                item.querySelector('.check-sekolah').checked = true;
                            }
                        });
                        
                        const badge = this.querySelector('.count-badge');
                        if(badge) badge.innerText = data.my_schools.length;
                    });
            });
        });

        // 3. Search Sekolah
        const searchSekolahInput = document.getElementById('search-sekolah');
        if(searchSekolahInput) {
            searchSekolahInput.addEventListener('keyup', function() {
                const val = this.value.toLowerCase();
                document.querySelectorAll('.item-sekolah').forEach(item => {
                    const text = item.getAttribute('data-search');
                    // Hanya filter yang aslinya memang terlihat
                    if (item.style.display !== 'none' || val === '') {
                        item.style.display = text.includes(val) ? 'block' : 'none';
                    }
                });
            });
        }

        // 4. Select All
        const btnSelectAll = document.getElementById('btn-select-all');
        if(btnSelectAll) {
            btnSelectAll.addEventListener('click', function() {
                const visibleCheckboxes = document.querySelectorAll('.item-sekolah[style*="display: block"] .check-sekolah');
                const allChecked = Array.from(visibleCheckboxes).every(cb => cb.checked);
                visibleCheckboxes.forEach(cb => cb.checked = !allChecked);
                this.innerText = allChecked ? 'Pilih Semua' : 'Batal Pilih Semua';
            });
        }

        // 5. Simpan Pemetaan (Pake Fetch API biar modern dan gak gampang nyangkut)
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

                // Set Loading
                const originalHtml = btnSave.innerHTML;
                btnSave.disabled = true;
                btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

                fetch('{{ route("admin.pkks.mapping-pengawas.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        pengawas_id: currentPengawasId,
                        sekolah_ids: selectedSchools
                    })
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Server Error');
                    return data;
                })
                .then(res => {
                    if (res.success) {
                        // Toast Gaya "Sneat" Minimalis
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });

                        Toast.fire({
                            icon: 'success',
                            title: res.message
                        });

                        // Update angka di list kiri
                        const activeBadge = document.querySelector('.btn-pengawas.active .count-badge');
                        if(activeBadge) activeBadge.innerText = selectedSchools.length;
                    } else {
                        throw new Error(res.message);
                    }
                })
                .catch(error => {
                    console.error('Save Error:', error);
                    Swal.fire('Error!', error.message || 'Terjadi kesalahan pada server.', 'error');
                })
                .finally(() => {
                    // Berhentiin Spinner
                    btnSave.disabled = false;
                    btnSave.innerHTML = originalHtml;
                });
            });
        }
    });
</script>
@endpush
@endsection
