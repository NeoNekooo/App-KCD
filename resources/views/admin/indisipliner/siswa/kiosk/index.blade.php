@extends('layouts.admin') {{-- Asumsi Anda menggunakan layout admin utama --}}

{{-- ====================================================================== --}}
{{-- BAGIAN CONTENT UTAMA                                                 --}}
{{-- ====================================================================== --}}
@section('content')

{{-- ====================================================================== --}}
{{-- PERBAIKAN 1: Menambahkan variabel 'config' palsu                     --}}
{{-- Ini untuk 'menipu' script analytics template agar tidak crash.       --}}
{{-- ====================================================================== --}}
<script>
  let config = { assetsPath: '' }; 
</script>

{{-- CSS Khusus untuk Kios (Chip, dll) --}}
<style>
    /* Style untuk Chip Pelanggaran */
    .btn-chip {
        margin: 4px;
        border-radius: 16px; /* Membuatnya bulat seperti pil */
    }
    
    /* Style untuk Chip yang Aktif */
    .btn-chip.active {
        background-color: #007bff; /* Warna biru primer (sesuaikan) */
        color: white;
        border-color: #007bff;
    }
    
    /* Style untuk Chip yang Disabled */
    .btn-chip:disabled {
        opacity: 0.6;
    }

    /* Sidebar untuk monitoring */
    .kiosk-sidebar {
        height: 80vh; /* Tinggi 80% layar */
        overflow-y: auto; /* Scroll jika log penuh */
    }
    .log-entry {
        border-bottom: 1px solid #eee;
        padding-bottom: 8px;
        margin-bottom: 8px;
    }
    .log-entry small {
        color: #6c757d; /* Warna abu-abu */
    }

    /* Memastikan scanner tidak terlalu besar di mobile */
    #qr-reader {
        max-width: 500px;
        width: 100%;
        margin: 0 auto;
    }
</style>

<div class="container-fluid py-3">
    <div class="row">
        
        {{-- =================================== --}}
        {{-- KOLOM KIRI: SCANNER & JAM           --}}
        {{-- =================================== --}}
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Kios Pencatatan Pelanggaran</h5>
                    {{-- Jam Realtime --}}
                    <span class="badge bg-primary rounded-pill fs-6" id="realtime-clock">{{ date('H:i:s') }}</span>
                </div>
                <div class="card-body text-center">
                    
                    <p class="lead" id="scanner-status">Kamera siap memindai...</p>
                    
                    <!-- 1. Jendela Kamera (Selalu Aktif) -->
                    <div id="qr-reader"></div>

                </div>
            </div>
            
            {{-- (Opsional) Anda bisa tambahkan 'Jadwal Hari Ini' seperti di gambar Anda --}}
            
        </div>

        {{-- =================================== --}}
        {{-- KOLOM KANAN: FORM & MONITORING    --}}
        {{-- =================================== --}}
        <div class="col-md-6">
            
            <!-- 2. FORM INPUT PELANGGARAN (NON-MODAL) -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Form Input Pelanggaran</h5>
                </div>
                <div class="card-body">
                    <form id="form-input-kiosk">
                        <!-- Info Siswa (Diisi JS) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Siswa</label>
                            <input type="text" id="form_nama_siswa" class="form-control" 
                                   placeholder="Scan kartu siswa untuk memulai..." readonly>
                        </div>

                        <!-- Kumpulan Chip Pelanggaran -->
                        {{-- Fieldset ini akan di-enable/disable oleh JS --}}
                        <fieldset id="fieldset-pelanggaran" disabled>
                            <label class="form-label fw-bold">Pilih satu atau lebih pelanggaran:</label>
                            {{-- Container untuk chip --}}
                            <div id="chip-container" style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                                {{-- Loop dari data Controller (kategoriList) --}}
                                @forelse ($kategoriList as $kategori)
                                    <div class="mb-2">
                                        <strong>{{ $kategori->nama }}</strong>
                                        <div class="d-flex flex-wrap">
                                            @foreach ($kategori->pelanggaranPoin as $poin)
                                                <button type="button" 
                                                        class="btn btn-outline-secondary btn-chip"
                                                        data-id="{{ $poin->ID }}">
                                                    {{ $poin->nama }} ({{ $poin->poin }})
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-danger">Belum ada data poin pelanggaran di pengaturan.</p>
                                @endforelse
                            </div>
                        </fieldset>

                        <!-- Tombol Simpan -->
                        <div class="d-grid mt-3">
                            <button type="submit" id="form_simpan_btn" class="btn btn-primary btn-lg" disabled>
                                Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 3. MONITORING HARI INI -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monitoring Hari Ini</h5>
                </div>
                <div class="card-body kiosk-sidebar">
                    <div id="monitoring-log">
                        <p class="text-muted" id="log-placeholder">Belum ada data tercatat...</p>
                        <!-- Data log akan ditambahkan oleh JavaScript di sini -->
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection

{{-- ====================================================================== --}}
{{-- BAGIAN JAVASCRIPT                                                    --}}
{{-- ====================================================================== --}}
@push('scripts')
{{-- Load SweetAlert (Penting untuk notifikasi) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- Load Library Scanner QR --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
// Pastikan semua HTML selesai di-load
// document.addEventListener('DOMContentLoaded', function () { // <-- BARIS INI DIHAPUS

    // --- 1. SETUP AWAL (Definisi Variabel) ---
    const form = document.getElementById('form-input-kiosk');
    const fieldset = document.getElementById('fieldset-pelanggaran');
    const chipContainer = document.getElementById('chip-container');
    const siswaNamaInput = document.getElementById('form_nama_siswa');
    const saveButton = document.getElementById('form_simpan_btn');
    const scannerStatus = document.getElementById('scanner-status');
    
    const logContainer = document.getElementById('monitoring-log');
    const logPlaceholder = document.getElementById('log-placeholder');
    
    let currentQrToken = null; // Menyimpan QR Token siswa yang sedang aktif
    let html5QrcodeScanner; // Deklarasi di scope atas
    
    // ======================================================================
    // PERBAIKAN 2: Hapus variabel 'isScannerPaused'
    // Kita akan gunakan 'getState()' dari library scanner-nya langsung
    // ======================================================================
    // let isScannerPaused = false; // <-- BARIS INI DIHAPUS

    
    // Setup Jam Realtime
    const clockElement = document.getElementById('realtime-clock');
    if (clockElement) {
        // Update jam setiap detik
        setInterval(() => {
            clockElement.innerText = new Date().toLocaleTimeString('id-ID');
        }, 1000);
    }

    // --- 2. LOGIKA TOMBOL CHIP (Toggle Aktif/Non-Aktif) ---
    chipContainer.addEventListener('click', function(e) {
        // Hanya jika yang diklik adalah tombol chip
        if (e.target.classList.contains('btn-chip')) {
            // Toggle (aktif/nonaktif)
            e.target.classList.toggle('active');
            e.target.classList.toggle('btn-outline-secondary');
            e.target.classList.toggle('btn-primary');
        }
    });

    // --- 3. LOGIKA TOMBOL SIMPAN (AJAX POST) ---
    form.addEventListener('submit', async function(e) {
        e.preventDefault(); // Hentikan submit form biasa
        
        // Ambil semua chip yang aktif
        const activeChips = chipContainer.querySelectorAll('.btn-chip.active');
        if (activeChips.length === 0) {
            Swal.fire('Error', 'Pilih minimal satu pelanggaran.', 'error');
            return;
        }

        // Kumpulkan ID pelanggaran dari chip yang aktif
        const pelanggaran_ids = Array.from(activeChips).map(chip => chip.dataset.id);

        // Tampilkan loading di tombol
        saveButton.disabled = true;
        saveButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
        
        try {
            // Panggil API 'kiosk.store' yang ada di web.php
            const response = await fetch("{{ route('admin.indisipliner.siswa.kiosk.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Penting untuk keamanan Laravel
                },
                body: JSON.stringify({
                    qr_token: currentQrToken,
                    pelanggaran_ids: pelanggaran_ids
                })
            });

            const data = await response.json();
            if (!response.ok) throw new Error(data.error || 'Terjadi kesalahan');

            // --- SUKSES MENYIMPAN ---
            // Tampilkan notifikasi "Toast" kecil
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Disimpan!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            
            addLogEntry(data); // Tambahkan ke log monitoring
            resetForm(); // Reset form dan aktifkan scan lagi

        } catch (error) {
            // ======================================================================
            // PERBAIKAN 3: Tambahkan console.error untuk debug
            // ======================================================================
            console.error('GAGAL MENYIMPAN:', error);
            Swal.fire('Gagal', error.message, 'error');
            
            // Jika gagal, jangan reset, biarkan OSIS perbaiki
            saveButton.disabled = false;
            saveButton.innerHTML = 'Simpan Data';
        }
    });
    
    // --- 4. FUNGSI UNTUK RESET FORM & LANJUTKAN SCAN ---
    function resetForm() {
        currentQrToken = null;
        siswaNamaInput.value = '';
        siswaNamaInput.placeholder = 'Scan kartu siswa untuk memulai...';
        
        // Reset semua chip
        chipContainer.querySelectorAll('.btn-chip.active').forEach(chip => {
            chip.classList.remove('active', 'btn-primary');
            chip.classList.add('btn-outline-secondary');
        });
        
        // Disable form
        fieldset.disabled = true;
        saveButton.disabled = true;
        saveButton.innerHTML = 'Simpan Data';
        
        // ======================================================================
        // PERBAIKAN 4: Ganti logika 'isScannerPaused'
        // Kita cek status scanner SEKARANG.
        // Status 3 = PAUSED
        // ======================================================================
        try {
            if (html5QrcodeScanner && html5QrcodeScanner.getState() === 3) {
                html5QrcodeScanner.resume(); // Lanjutkan kamera
                scannerStatus.innerText = 'Kamera siap memindai...';
            }
        } catch (e) {
            console.error("Gagal me-resume scanner:", e);
            scannerStatus.innerText = 'Gagal restart kamera. Refresh halaman.';
        }
    }

    // --- 5. FUNGSI UNTUK MENAMBAHKAN LOG ke Monitoring ---
    function addLogEntry(data) {
        // Hapus placeholder 'Belum ada data'
        if (logPlaceholder) {
            logPlaceholder.style.display = 'none';
        }
        
        const entry = document.createElement('div');
        entry.className = 'log-entry';
        // Buat HTML untuk log entry
        entry.innerHTML = `
            <strong>${data.siswa_nama}</strong> (${data.siswa_rombel})
            <br>
            <span class="badge bg-danger rounded-pill">${data.jumlah_pelanggaran} Pelanggaran</span>
            <small class="float-end">${data.waktu}</small>
        `;
        // Tambahkan ke paling atas log
        logContainer.prepend(entry);
    }

    // --- 6. LOGIKA SCANNER (INTI) ---
    async function onScanSuccess(decodedText, decodedResult) {
        // 'decodedText' adalah isi qr_token
        
        // Jangan scan siswa yang sama 2x (jika form masih aktif)
        // Kita cek dari 'currentQrToken', bukan 'isScannerPaused'
        if (currentQrToken === decodedText) return; 
        
        // ======================================================================
        // PERBAIKAN 5: Ganti logika 'isScannerPaused'
        // Kita cek status scanner SEKARANG.
        // Status 2 = SCANNING
        // ======================================================================
        try {
            if (html5QrcodeScanner && html5QrcodeScanner.getState() === 2) {
                html5QrcodeScanner.pause();
            }
        } catch (e) {
            console.error("Gagal mem-pause scanner:", e);
            // Lanjut saja, mungkin library sudah auto-pause
        }

        scannerStatus.innerText = 'Siswa Terdeteksi. Mengambil data...';
        
        // Simpan token
        currentQrToken = decodedText; 
        
        try {
            // Panggil API 'kiosk.findSiswa' yang ada di web.php
            const response = await fetch(`/admin/indisipliner/siswa/api/kiosk-find-siswa/${currentQrToken}`);
            const data = await response.json();
            if (!response.ok) throw new Error(data.error || 'Siswa tidak ditemukan');

            // --- SUKSES DAPAT NAMA ---
            // Isi nama di form
            siswaNamaInput.value = `${data.nama_siswa} (${data.nama_rombel})`;
            
            // Aktifkan form
            fieldset.disabled = false;
            saveButton.disabled = false;
            scannerStatus.innerText = 'Silakan pilih pelanggaran dan klik Simpan.';
            
            // Auto-focus ke tombol simpan (opsional, tapi bagus)
            saveButton.focus();

        } catch (error) {
            // Jika error (siswa tdk ditemukan), reset & scan lagi
            Swal.fire('Error', error.message, 'error');
            resetForm(); // Auto-reset jika scan gagal
        }
    }
    
    // Fungsi gagal scan (bisa diabaikan)
    function onScanFailure(error) { 
        // console.warn(`Scan error: ${error}`);
    }

    // --- 7. MULAI SCANNER SAAT HALAMAN DIBUKA ---
    // Pastikan #qr-reader ada sebelum membuat scanner
    if (document.getElementById("qr-reader")) {
        html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", // ID elemen
            { 
                fps: 10, // 10 frame per detik
                qrbox: { width: 300, height: 150 } // Ukuran kotak scan
            }
        );
        // Render scanner
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    } else {
        console.error("Elemen #qr-reader tidak ditemukan!");
    }

// }); // <-- BARIS INI JUGA DIHAPUS
</script>
@endpush