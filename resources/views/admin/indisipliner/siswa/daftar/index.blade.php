@extends('layouts.admin')

{{-- 
======================================================================
 BAGIAN STYLE BARU (Untuk Avatar di Tabel)
======================================================================
--}}
@push('styles')
<style>
    /* Style ini untuk membuat avatar inisial nama di dalam tabel.
      Ukurannya lebih kecil (sm = small) dari avatar di rekapitulasi.
    */
    .avatar-initials-sm {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;  /* Ukuran lebih kecil */
        height: 40px; /* Ukuran lebih kecil */
        border-radius: 50%;
        background-color: #f0f0f8; /* Warna background (sesuaikan) */
        color: #696cff; /* Warna teks (sesuaikan) */
        font-size: 1.25rem; /* Ukuran font lebih kecil */
        font-weight: 600;
        flex-shrink: 0; /* Mencegah avatar penyet */
    }
</style>
@endpush


{{-- 
======================================================================
 BAGIAN CONTENT (Sudah Dirapikan)
======================================================================
--}}
@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Indisipliner / Siswa /</span> Daftar Indisipliner
</h4>

{{-- Box untuk error validasi modal --}}
@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>ERROR! Data Gagal Disimpan:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- 
  Notifikasi 'success' dan 'error' lain
  diproses di dalam @push('scripts') di bawah.
--}}


{{-- KARTU 1: FILTER PENCARIAN (Tidak berubah) --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bx bx-filter-alt me-1"></i> Filter Pencarian</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.indisipliner.siswa.daftar.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                
                {{-- Filter Semester --}}
                <div class="col-md-3">
                    <label class="form-label" for="semester_id">Tahun Pelajaran - Semester</label>
                    <select name="semester_id" id="semester_id" class="form-select">
                        <option value="">- Semua Semester -</option>
                        @foreach ($semesterList as $item)
                            <option value="{{ $item['semester_id'] }}" 
                                {{ request('semester_id') == $item['semester_id'] ? 'selected' : '' }}>
                                {{ $item['tahun_pelajaran'] }} - {{ $item['semester'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Kelas --}}
                <div class="col-md-3">
                    <label class="form-label" for="kelas_id">Kelas</label>
                    <select name="tingkat_kelas" id="kelas_id" class="form-select">
                        <option value="">- Semua Kelas -</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->nama }}" 
                                {{ request('tingkat_kelas') == $kelas->nama ? 'selected' : '' }}>
                                {{ $kelas->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Rombel --}}
                <div class="col-md-3">
                    <label class="form-label" for="rombel_id">Rombongan Belajar</label>
                    <select name="rombel_id" id="rombel_id" class="form-select">
                        <option value="">- Semua Rombel -</option>
                        {{-- Opsi rombel di sini akan diisi oleh JavaScript saat Kelas dipilih --}}
                        @foreach ($rombelList as $rombel)
                            <option value="{{ $rombel->id }}" 
                                {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>
                                {{ $rombel->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Aksi Filter --}}
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bx bx-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.indisipliner.siswa.daftar.index') }}" 
                       class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bx bx-refresh"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>


{{-- 
======================================================================
 KARTU 2: HASIL DATA & AKSI (Tampilan Tabel Diperbarui)
======================================================================
--}}
<div class="card">
    {{-- Header: Judul dan Tombol Input --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Riwayat Pelanggaran Siswa</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalInputPelanggaran">
            <i class="bx bx-plus me-1"></i> Input Pelanggaran
        </button>
    </div>

    {{-- Tabel Data (DIRAPIKAN) --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Siswa</th> {{-- <- (Gabungan Nama, NISN, Kelas) --}}
                    <th>Pelanggaran</th>
                    <th>Waktu Kejadian</th> {{-- <- (Gabungan Tanggal, Jam) --}}
                    <th>Poin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pelanggaranList as $key => $pelanggaran)
                    <tr>
                        <td>{{ $pelanggaranList->firstItem() + $key }}</td>
                        
                        {{-- (BARU) Kolom Info Siswa dengan Avatar --}}
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-initials-sm me-3">
                                    {{-- Ambil huruf pertama dari nama siswa --}}
                                    {{ substr($pelanggaran->siswa->nama ?? '?', 0, 1) }}
                                </div>
                                <div class="d-flex flex-column">
                                    <strong class="text-nowrap">{{ $pelanggaran->siswa->nama ?? 'Siswa Dihapus' }}</strong>
                                    <small class="text-muted">
                                        {{ $pelanggaran->rombel->nama ?? 'Rombel Dihapus' }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        
                        {{-- Kolom Pelanggaran (dibuat wrap) --}}
                        <td style="white-space: normal;">
                            {{ $pelanggaran->detailPoinSiswa->nama ?? 'Poin Dihapus' }}
                        </td>
                        
                        {{-- (BARU) Kolom Waktu Kejadian --}}
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-nowrap">{{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d M Y') }}</span>
                                <small class="text-muted">Jam: {{ $pelanggaran->jam }}</small>
                            </div>
                        </td>
                        
                        {{-- Kolom Poin --}}
                        <td><span class="badge bg-danger rounded-pill">{{ $pelanggaran->poin }}</span></td>
                        
                        {{-- Kolom Aksi --}}
                        <td>
                            <form action="{{ route('admin.indisipliner.siswa.daftar.destroy', $pelanggaran->ID) }}" 
                                  method="POST" class="d-inline form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-sm btn-outline-danger"
                                        data-bs-toggle="tooltip" title="Hapus Data">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    {{-- (BARU) Tampilan Data Kosong --}}
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bx bx-search-alt-2 bx-lg d-block mb-3 text-muted"></i>
                            <h5 class="mb-1">Data Tidak Ditemukan</h5>
                            <p class="text-muted">Tidak ada data pelanggaran siswa yang sesuai dengan filter Anda.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer Pagination --}}
    <div class="card-footer d-flex justify-content-between align-items-center">
        @if($pelanggaranList->total() > 0)
            <small class="text-muted">
                Menampilkan {{ $pelanggaranList->firstItem() }} sampai {{ $pelanggaranList->lastItem() }} 
                dari {{ $pelanggaranList->total() }} data
            </small>
        @endif
        {{ $pelanggaranList->links() }}
    </div>
</div>

{{-- 
  Modal Input Pelanggaran (Tidak berubah)
--}}
@include('admin.indisipliner.siswa.daftar._modal-form')

@endsection

{{-- 
======================================================================
 BAGIAN SCRIPT (Tidak berubah dari versi sebelumnya)
======================================================================
--}}
@push('scripts')
{{-- 1. Load Libraries --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

{{-- 2. Script Utama Halaman Ini --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // =================================================================
    // BAGIAN A: Notifikasi (Dipindah ke sini agar Rapi & Aman)
    // =================================================================
    
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif
    
    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: `{!! implode('<br>', $errors->all()) !!}`,
        });
    @endif


    // =================================================================
    // BAGIAN B: HELPER FUNCTION
    // =================================================================
    
    function clearSelect(select, message) {
        if (!select) return; 
        select.innerHTML = ''; 
        select.add(new Option(message, ''));
    }


    // =================================================================
    // BAGIAN C: SCRIPT HALAMAN INDEX (Filter & Delete)
    // =================================================================

    // --- C.1: Filter 'Kelas' -> 'Rombel' ---
    const filterKelas = document.getElementById('kelas_id');
    const filterRombel = document.getElementById('rombel_id');

    if (filterKelas && filterRombel) {
        
        filterKelas.addEventListener('change', async function() {
            const tingkat = this.value; 
            clearSelect(filterRombel, 'Memuat rombel...');
            filterRombel.disabled = true;

            if (!tingkat) {
                clearSelect(filterRombel, '- Semua Rombel -');
                filterRombel.disabled = false;
                const currentRombelId = '{{ request('rombel_id') }}';
                if (currentRombelId) filterRombel.value = currentRombelId;
                return; 
            }

            try {
                const url = `{{ route('admin.indisipliner.siswa.getRombelsByTingkat') }}?tingkat=${tingkat}`;
                const response = await fetch(url);
                if (!response.ok) throw new Error('Respon server tidak baik.');
                
                const rombels = await response.json(); 
                clearSelect(filterRombel, '- Pilih Rombel -');
                filterRombel.disabled = false;
                filterRombel.add(new Option('- Semua Rombel di ' + tingkat + ' -', '')); 
                
                rombels.forEach(function(rombel) {
                    filterRombel.add(new Option(rombel.nama, rombel.id));
                });
                
                const currentRombelId = '{{ request('rombel_id') }}';
                if (currentRombelId) filterRombel.value = currentRombelId;

            } catch (error) {
                console.error('Gagal mengambil data rombel:', error);
                clearSelect(filterRombel, 'Gagal memuat');
                filterRombel.disabled = false;
            }
        });

        // Picu event 'change' saat halaman dimuat
        if (filterKelas.value) { 
            filterKelas.dispatchEvent(new Event('change'));
        } else {
             const currentRombelId = '{{ request('rombel_id') }}';
             if (currentRombelId) filterRombel.value = currentRombelId;
        }
    }

    // --- C.2: Inisialisasi Tooltip ---
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // --- C.3: SweetAlert konfirmasi hapus ---
    document.querySelectorAll('.form-delete').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Data?',
                text: 'Data pelanggaran ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    form.submit();
                }
            });
        });
    });


    // =================================================================
    // BAGIAN D: SCRIPT MODAL INPUT PELANGGARAN
    // =================================================================
    const modalElement = document.getElementById('modalInputPelanggaran');
    const rombelSelect = document.getElementById('modal_rombel_id');
    const semesterText = document.getElementById('modal_semester_text');
    const semesterHidden = document.getElementById('modal_semester_id');
    const siswaSelect = document.getElementById('modal_nipd');
    const mapelSelect = document.getElementById('modal_mapel_id');
    const pelanggaranSelect = document.getElementById('modal_pelanggaran_id');
    const poinInput = document.getElementById('modal_poin');

    // --- [AJAX] Saat Rombel diganti ---
    if (rombelSelect) { 
        rombelSelect.addEventListener('change', async function() {
            const rombel_id_internal = this.value;

            semesterText.value = 'Memuat...';
            semesterHidden.value = '';
            clearSelect(siswaSelect, 'Memuat...');
            siswaSelect.disabled = true;
            clearSelect(mapelSelect, 'Memuat...');
            mapelSelect.disabled = true;

            if (!rombel_id_internal) {
                semesterText.value = '- Pilih Rombel Dahulu -';
                clearSelect(siswaSelect, '- Pilih Rombel Dahulu -');
                siswaSelect.disabled = true;
                clearSelect(mapelSelect, '- Di Luar Jam Pelajaran -');
                mapelSelect.disabled = false;
                return;
            }
            
            const url = `/admin/indisipliner/siswa/get-rombel-details/${rombel_id_internal}`; 

            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                
                const data = await response.json();

                if (data.semester && data.semester.id) {
                    semesterText.value = data.semester.text;
                    semesterHidden.value = data.semester.id;
                } else {
                    semesterText.value = 'Semester Gagal Dimuat';
                }

                clearSelect(siswaSelect, '- Pilih Siswa -');
                siswaSelect.disabled = false;
                data.siswa.forEach(siswa => {
                    siswaSelect.add(new Option(`${siswa.nisn} - ${siswa.nama}`, siswa.nipd)); 
                });

                clearSelect(mapelSelect, '- Di Luar Jam Pelajaran -');
                mapelSelect.disabled = false;
                data.mapel.forEach(mapel => {
                    mapelSelect.add(new Option(mapel.nama, mapel.id));
                });

            } catch (error) {
                console.error("Gagal mengambil detail rombel:", error);
                semesterText.value = 'Gagal Muat (Error)';
                clearSelect(siswaSelect, 'Gagal Muat (Error)');
                clearSelect(mapelSelect, 'Gagal Muat (Error)');
            }
        });
    }

    // --- Saat Jenis Pelanggaran diganti ---
    if (pelanggaranSelect) {
        pelanggaranSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const poin = selectedOption.dataset.poin;
            if(poinInput) poinInput.value = poin || '';
        });
    }

    // --- Saat Modal Ditampilkan ---
    if (modalElement) {
        modalElement.addEventListener('shown.bs.modal', function () {
            const rombelFilterValue = document.getElementById('rombel_id').value;
            
            document.getElementById('formInputPelanggaran').reset();
            if (pelanggaranSelect) pelanggaranSelect.value = '';

            if (rombelFilterValue && rombelSelect) {
                rombelSelect.value = rombelFilterValue;
                rombelSelect.dispatchEvent(new Event('change'));
            } else {
                if (rombelSelect) rombelSelect.value = '';
                if (semesterText) semesterText.value = '- Pilih Rombel Dahulu -';
                if (semesterHidden) semesterHidden.value = '';
                clearSelect(siswaSelect, '- Pilih Rombel Dahulu -');
                if (siswaSelect) siswaSelect.disabled = true;
                clearSelect(mapelSelect, '- Di Luar Jam Pelajaran -');
                if (mapelSelect) mapelSelect.disabled = true;
            }
        });
    }
    

    // =================================================================
    // BAGIAN E: SCRIPT SCANNER QR
    // =================================================================
    
    const readerElement = document.getElementById('qr-reader');
    const startScanBtn = document.getElementById('btn-start-scan');
    const stopScanBtn = document.getElementById('btn-stop-scan');
    const stopScanContainer = document.getElementById('stop-scan-container');
    let html5QrcodeScanner = null;

    if (startScanBtn) {
        startScanBtn.addEventListener('click', () => {
            readerElement.style.display = 'block';
            stopScanContainer.style.display = 'block';
            startScanBtn.style.display = 'none';

            html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader",
                { fps: 10, qrbox: { width: 250, height: 250 } }
            );
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        });
    }

    if (stopScanBtn) {
        stopScanBtn.addEventListener('click', async () => await stopScanner());
    }

    async function onScanSuccess(decodedText, decodedResult) {
        console.log(`Hasil Scan: ${decodedText}`);
        await stopScanner();
        Swal.fire({
            title: 'Mencari Data Siswa...',
            text: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        fetchDataSiswa(decodedText);
    }
    
    function onScanFailure(error) { /* console.warn(`Scan error: ${error}`); */ }

    async function fetchDataSiswa(qrToken) {
        try {
            const url = `/admin/indisipliner/siswa/get-siswa-by-qr/${qrToken}`;
            const response = await fetch(url);
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Siswa tidak ditemukan');
            }
            
            const data = await response.json();
            
            // Isi form otomatis
            semesterText.value = data.semester.text;
            semesterHidden.value = data.semester.id;
            rombelSelect.value = data.selected_rombel_id;
            
            clearSelect(siswaSelect, '- Pilih Siswa -');
            siswaSelect.disabled = false;
            data.siswa.forEach(siswa => {
                siswaSelect.add(new Option(`${siswa.nisn} - ${siswa.nama}`, siswa.nipd));
            });
            siswaSelect.value = data.selected_siswa_nipd;

            clearSelect(mapelSelect, '- Di Luar Jam Pelajaran -');
            mapelSelect.disabled = false;
            data.mapel.forEach(mapel => {
                mapelSelect.add(new Option(mapel.nama, mapel.id));
            });
            
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Siswa Ditemukan!',
                text: `${data.siswa.find(s => s.nipd === data.selected_siswa_nipd).nama}`,
                timer: 2000,
                showConfirmButton: false
            });

        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: error.message
            });
        }
    }

    async function stopScanner() {
        if (html5QrcodeScanner) {
            try { await html5QrcodeScanner.clear(); } 
            catch(e) { console.warn('Gagal mematikan scanner:', e); }
            html5QrcodeScanner = null;
        }
        if (readerElement) readerElement.style.display = 'none';
        if (stopScanContainer) stopScanContainer.style.display = 'none';
        if (startScanBtn) startScanBtn.style.display = 'block';
    }

    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', async () => await stopScanner());
    }

}); // <-- AKHIR DARI 'DOMContentLoaded'
</script>
@endpush