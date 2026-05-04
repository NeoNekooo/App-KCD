@extends('layouts.admin')

@section('title', 'Mapping Pengawas Pembina')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0"><span class="text-muted fw-light">PKKS /</span> Mapping Pengawas</h4>
        <div>
            <button type="button" class="btn btn-primary shadow-sm d-none" id="btn-save">
                <i class="bx bx-save me-1"></i> Simpan Pemetaan
            </button>
        </div>
    </div>

    {{-- Filter Jenjang --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase mb-1">Filter Jenjang</label>
                    <select id="filter-jenjang" class="form-select border-2">
                        <option value="">-- Tampilkan Semua Jenjang --</option>
                        @foreach($jenjangs as $j)
                        <option value="{{ $j->nama }}">{{ $j->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 text-md-end mt-3 mt-md-0">
                    <div class="small text-muted">
                        <i class="bx bx-info-circle me-1"></i> Pilih pengawas terlebih dahulu untuk mengelola pemetaan sekolah.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Tabel Sekolah (Kiri) --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-buildings me-2 text-primary"></i>Daftar Sekolah</h5>
                    <div class="input-group input-group-merge w-50">
                        <span class="input-group-text border-0 bg-light"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control border-0 bg-light" id="search-sekolah" placeholder="Cari NPSN atau Nama...">
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 60vh;">
                    <table class="table table-hover align-middle mb-0" id="table-sekolah">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="120">NPSN</th>
                                <th>Nama Sekolah</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sekolahs as $s)
                            <tr class="row-sekolah" data-jenjang="{{ $s->bentuk_pendidikan_id_str }}" data-search="{{ strtolower($s->npsn . ' ' . $s->nama) }}">
                                <td class="fw-bold">{{ $s->npsn }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $s->nama }}</div>
                                    <div class="d-flex align-items-center gap-2 small">
                                        <span class="badge bg-label-secondary" style="font-size: 9px;">{{ $s->bentuk_pendidikan_id_str }}</span>
                                        <span class="text-muted">{{ $s->kecamatan }}</span>
                                    </div>
                                    <div class="owner-info mt-1 d-none" id="owner-{{ $s->sekolah_id }}">
                                        <span class="badge bg-label-info rounded-pill" style="font-size: 10px;">
                                            <i class="bx bx-user me-1"></i> <span class="owner-name"></span>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input check-sekolah" type="checkbox" 
                                               value="{{ $s->sekolah_id }}" 
                                               id="cb-{{ $s->sekolah_id }}" 
                                               disabled>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Side Panel Pengawas (Kanan) --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-primary py-3">
                    <h6 class="mb-0 text-white fw-bold"><i class="bx bx-user-check me-2"></i>Pilih Pengawas</h6>
                </div>
                <div class="card-body p-0">
                    <div class="p-3 border-bottom">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text border-0 bg-light"><i class="bx bx-search"></i></span>
                            <input type="text" class="form-control border-0 bg-light" id="search-pengawas" placeholder="Cari pengawas...">
                        </div>
                    </div>
                    <div class="list-group list-group-flush" style="max-height: 50vh; overflow-y: auto;">
                        @foreach($pengawas as $p)
                        <a href="javascript:void(0);" 
                           class="list-group-item list-group-item-action btn-pengawas d-flex align-items-center py-3"
                           data-id="{{ $p->id }}"
                           data-name="{{ $p->name }}"
                           data-search="{{ strtolower($p->name) }}">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($p->name, 0, 1) }}</span>
                            </div>
                            <div class="w-100 overflow-hidden">
                                <h6 class="mb-0 text-truncate fw-bold">{{ $p->name }}</h6>
                                <small class="text-muted small">ID: {{ $p->id }}</small>
                            </div>
                            <i class="bx bx-chevron-right ms-auto opacity-25"></i>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-4 p-4 rounded-4 bg-label-primary border border-primary border-opacity-10 d-none" id="mapping-summary">
                <h6 class="fw-bold mb-2">Informasi Pemetaan</h6>
                <p class="small mb-0">Pengawas: <strong id="sum-name">-</strong></p>
                <p class="small mb-0">Sekolah dipilih: <strong id="sum-count">0</strong></p>
            </div>
        </div>
    </div>
</div>

<style>
    .row-sekolah { transition: all 0.2s; }
    .row-sekolah.assigned-to-other { background-color: rgba(0,0,0,0.02); opacity: 0.8; }
    .btn-pengawas { border: none !important; margin-bottom: 2px; }
    .btn-pengawas:hover { background-color: #f8f9ff; }
    .btn-pengawas.active { 
        background-color: #696cff !important; 
        color: #fff !important; 
        box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3);
    }
    .btn-pengawas.active h6, .btn-pengawas.active small { color: #fff !important; }
    .check-sekolah { width: 1.5rem; height: 1.5rem; cursor: pointer; }
    .check-sekolah:disabled { cursor: not-allowed; opacity: 0.5; }
    .table-responsive::-webkit-scrollbar { width: 5px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #eee; border-radius: 10px; }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentPengawasId = null;
        let globalMapping = @json($mapping); // sekolah_id => pengawas_id
        let pengawasData = @json($pengawas->keyBy('id'));

        // 1. Filter Jenjang
        const filterJenjang = document.getElementById('filter-jenjang');
        filterJenjang.addEventListener('change', function() {
            const val = this.value;
            document.querySelectorAll('.row-sekolah').forEach(row => {
                const j = row.getAttribute('data-jenjang');
                row.style.display = (!val || j === val) ? 'table-row' : 'none';
            });
        });

        // 2. Search Sekolah
        const searchSekolah = document.getElementById('search-sekolah');
        searchSekolah.addEventListener('keyup', function() {
            const val = this.value.toLowerCase();
            const currentJenjang = filterJenjang.value;
            document.querySelectorAll('.row-sekolah').forEach(row => {
                const j = row.getAttribute('data-jenjang');
                const s = row.getAttribute('data-search');
                const matchesJenjang = !currentJenjang || j === currentJenjang;
                const matchesSearch = s.includes(val);
                row.style.display = (matchesJenjang && matchesSearch) ? 'table-row' : 'none';
            });
        });

        // 3. Search Pengawas
        const searchPengawas = document.getElementById('search-pengawas');
        searchPengawas.addEventListener('keyup', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.btn-pengawas').forEach(btn => {
                btn.style.display = btn.getAttribute('data-search').includes(val) ? 'flex' : 'none';
            });
        });

        // 4. Klik Pengawas
        document.querySelectorAll('.btn-pengawas').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                // Toggle Active
                document.querySelectorAll('.btn-pengawas').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                currentPengawasId = id;
                document.getElementById('sum-name').innerText = name;
                document.getElementById('btn-save').classList.remove('d-none');
                document.getElementById('mapping-summary').classList.remove('d-none');

                refreshTable();
            });
        });

        function refreshTable() {
            if (!currentPengawasId) return;

            document.querySelectorAll('.row-sekolah').forEach(row => {
                const sid = row.querySelector('.check-sekolah').value;
                const checkbox = row.querySelector('.check-sekolah');
                const ownerBox = row.querySelector('.owner-info');
                const ownerName = row.querySelector('.owner-name');
                
                const currentOwnerId = globalMapping[sid];

                checkbox.disabled = false;
                checkbox.checked = false;
                row.classList.remove('assigned-to-other');
                ownerBox.classList.add('d-none');

                if (currentOwnerId) {
                    if (currentOwnerId == currentPengawasId) {
                        checkbox.checked = true;
                    } else {
                        // Dipegang orang lain
                        checkbox.disabled = true;
                        row.classList.add('assigned-to-other');
                        ownerBox.classList.remove('d-none');
                        ownerName.innerText = pengawasData[currentOwnerId] ? pengawasData[currentOwnerId].name : 'Lainnya';
                    }
                }
            });
            updateSummary();
        }

        function updateSummary() {
            const count = document.querySelectorAll('.check-sekolah:checked').length;
            document.getElementById('sum-count').innerText = count;
        }

        // Checkbox change listener
        document.querySelectorAll('.check-sekolah').forEach(cb => {
            cb.addEventListener('change', updateSummary);
        });

        // 5. Simpan (AJAX)
        document.getElementById('btn-save').addEventListener('click', function() {
            if (!currentPengawasId) return;
            
            const selectedIds = Array.from(document.querySelectorAll('.check-sekolah:checked')).map(cb => cb.value);
            const btn = this;
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            fetch('{{ route("admin.pkks.mapping-pengawas.update") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ 
                    pengawas_id: currentPengawasId, 
                    sekolah_ids: selectedIds,
                    jenjang: document.getElementById('filter-jenjang').value
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    // Update global mapping local
                    // 1. Hapus semua sekolah yang sebelumnya dimiliki pengawas ini
                    for (let sid in globalMapping) {
                        if (globalMapping[sid] == currentPengawasId) delete globalMapping[sid];
                    }
                    // 2. Tambah yang baru dipilih
                    selectedIds.forEach(sid => globalMapping[sid] = currentPengawasId);

                    Swal.fire({ icon: 'success', title: 'Sukses', text: res.message, timer: 2000, showConfirmButton: false });
                    refreshTable();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            })
            .catch(err => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' }))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        });
    });
</script>
@endpush
@endsection
