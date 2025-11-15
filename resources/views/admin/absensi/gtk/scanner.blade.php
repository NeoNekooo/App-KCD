<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
>
<head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    {{-- CSRF Token untuk AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kios Absensi QR - Guru & GTK</title>

    {{-- ASET DARI CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto+Mono:wght@700&display=swap" rel="stylesheet">

    {{-- Ini adalah CSS dari file siswa Anda, disalin langsung --}}
    <style>
        /* CSS UNTUK PROMPT FULLSCREEN */
        #fullscreen-prompt {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(21, 22, 37, 0.85);
            display: flex; justify-content: center; align-items: center;
            z-index: 9999; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
            flex-direction: column; color: white; text-align: center;
        }
        #enter-fullscreen-btn {
            font-size: 1.5rem; padding: 1rem 2rem;
            box-shadow: 0 8px 25px rgba(105, 108, 255, 0.5);
        }
        #fullscreen-prompt p {
            margin-top: 1.5rem; font-size: 1.1rem; max-width: 400px;
        }
        #main-content { display: none; opacity: 0; transition: opacity 0.3s ease-in-out; }
        body.fullscreen-active #main-content { display: block; opacity: 1; }
        body.fullscreen-active #fullscreen-prompt { display: none; }

        /* DEFINISI WARNA DAN GAYA UMUM */
        :root {
            --kiosk-bg-light: #f5f5f9; --kiosk-border-color: #d9dee3;
            --kiosk-text-primary: #566a7f; --kiosk-text-secondary: #697a8d;
            --kiosk-primary: #696cff; --kiosk-success: #71dd37;
            --kiosk-warning: #ffab00; --kiosk-info: #03c3ec;
            --kiosk-danger: #ff3e1d;
        }
        body { background-color: var(--kiosk-bg-light); }
        .card.kiosk-card { font-family: 'Poppins', sans-serif; box-shadow: none; border: 1px solid var(--kiosk-border-color); }
        .clock-container { background-color: #ffffff; color: var(--kiosk-text-primary); padding: 1.5rem; border-radius: 0.5rem; text-align: center; margin-bottom: 2rem; border: 1px solid var(--kiosk-border-color); }
        .clock-time { font-family: 'Roboto Mono', monospace; font-size: 3.5rem; font-weight: 700; letter-spacing: 2px; color: var(--kiosk-primary); }
        .clock-date { font-size: 1.1rem; color: var(--kiosk-text-secondary); }
        .scanner-viewport { width: 100%; max-width: 400px; margin: 0 auto; position: relative; aspect-ratio: 1 / 1; border-radius: 0.5rem; overflow: hidden; border: 2px solid var(--kiosk-border-color); }
        #qr-reader { width: 100%; height: 100%; }
        #qr-reader video { width: 100% !important; height: 100% !important; object-fit: cover; transform: scaleX(-1);}
        #qr-reader > div:first-of-type { border: none !important; }
        .scanner-viewport::after { content: ''; position: absolute; left: 0; top: 0; width: 100%; height: 4px; background: linear-gradient(90deg, transparent, var(--kiosk-primary), transparent); box-shadow: 0 0 10px var(--kiosk-primary); animation: scanline 3s linear infinite; z-index: 10; }
        @keyframes scanline { 0% { top: 0; } 100% { top: 100%; } }
        
        .recent-scans-list {
            list-style: none; padding: 0; margin: 0;
            overflow-y: auto;
        }
        .tab-content .recent-scans-list {
             max-height: 420px;
        }
        .recent-scans-list li {
            display: flex; align-items: center; padding: 0.8rem 0.5rem;
            border-bottom: 1px solid var(--kiosk-border-color);
            opacity: 0; transform: translateX(20px);
            animation: slideIn 0.4s forwards ease-out;
        }
        .recent-scans-list li:last-child { border-bottom: none; }
        .recent-scans-list li img.scan-photo {
            width: 45px !important; height: 45px !important; border-radius: 50%;
            object-fit: cover; margin-right: 1rem; flex-shrink: 0;
        }
        .scan-details { flex-grow: 1; }
        .scan-name { font-weight: 600; color: var(--kiosk-text-primary); }
        .scan-time { font-size: 0.85rem; color: var(--kiosk-text-secondary); }
        .status-badge { font-size: 0.8rem; }
        @keyframes slideIn { to { opacity: 1; transform: translateX(0); } }

        /* CSS UNTUK MODAL */
        #scanResultModal .modal-content { border: none; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); overflow: hidden; }
        #modal-loading-state .modal-loader { width: 80px; height: 80px; border: 8px solid #f3f3f3; border-top: 8px solid var(--kiosk-primary); border-radius: 50%; margin: 0 auto; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        #modal-result-state .result-header-new { display: flex; flex-direction: column; align-items: center; margin-bottom: 1.5rem; }
        #modal-result-state .student-photo-modal { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--kiosk-border-color); box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: border-color 0.3s ease; }
        #modal-result-state #modal-status-icon { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: white; border: 3px solid white; margin-bottom: 1rem; }
        #modal-student-name { color: var(--kiosk-text-primary); }
        #modal-status-info { color: var(--kiosk-text-secondary); font-size: 1.1rem; }
        .status-success #modal-status-icon { background-color: var(--kiosk-success); }
        .status-success .student-photo-modal { border-color: var(--kiosk-success); }
        .status-warning #modal-status-icon { background-color: var(--kiosk-warning); }
        .status-warning .student-photo-modal { border-color: var(--kiosk-warning); }
        .status-info #modal-status-icon { background-color: var(--kiosk-info); }
        .status-info .student-photo-modal { border-color: var(--kiosk-info); }
        .status-danger #modal-status-icon { background-color: var(--kiosk-danger); }
        .status-danger .student-photo-modal { border-color: var(--kiosk-danger); }

        #unscanned-count-badge {
            font-size: 0.7rem;
            line-height: 1;
        }
    </style>
</head>

<body>
    {{-- Prompt Fullscreen --}}
    <div id="fullscreen-prompt">
        <button id="enter-fullscreen-btn" class="btn btn-primary btn-lg">
            <i class='bx bx-fullscreen me-2'></i>Masuk Mode Kios
        </button>
        <p>Aplikasi ini dirancang untuk berjalan dalam mode layar penuh. Tekan tombol 'ESC' untuk keluar.</p>
    </div>

    {{-- Konten Utama (awalnya disembunyikan) --}}
    <div class="container-fluid p-4" id="main-content">
        <div class="row">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="card kiosk-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Kios Absensi QR (Guru & GTK)</h5> {{-- Teks Diubah --}}
                        <div class="text-secondary fw-light">Tekan 'ESC' untuk keluar</div>
                    </div>
                    <div class="card-body">
                        <div class="clock-container">
                            <div id="clock-time" class="clock-time">00:00:00</div>
                            <div id="clock-date" class="clock-date">Memuat...</div>
                        </div>

                        {{-- 
                          Box ini menggunakan variabel $jadwalHariIni dari Controller.
                          Ini adalah jadwal absensi SISWA, hanya untuk TAMPILAN
                          agar sama seperti scanner siswa Anda.
                        --}}
                        @if ($jadwalHariIni && $jadwalHariIni->is_active)
                            <div class="text-center p-3 mb-4" style="background-color: #f7f7f9; border-radius: 0.5rem; border: 1px solid #e1e3e8;">
                                <h5 class="mb-3">Jadwal Hari Ini ({{ $jadwalHariIni->hari }})</h5>
                                <div class="d-flex justify-content-around align-items-center">
                                    <div class="px-2">
                                        <i class='bx bx-log-in-circle bx-md text-primary'></i>
                                        <div class="small text-muted mt-1">Masuk</div>
                                        <div class="fs-4 fw-bold" style="color: #566a7f;">{{ date('H:i', strtotime($jadwalHariIni->jam_masuk_sekolah)) }}</div>
                                    </div>
                                    <div class="px-2">
                                        <i class='bx bx-log-out-circle bx-md text-info'></i>
                                        <div class="small text-muted mt-1">Pulang</div>
                                        <div class="fs-4 fw-bold" style="color: #566a7f;">{{ date('H:i', strtotime($jadwalHariIni->jam_pulang_sekolah)) }}</div>
                                    </div>
                                    <div class="px-2">
                                        <i class='bx bx-time bx-md text-warning'></i>
                                        <div class="small text-muted mt-1">Toleransi</div>
                                        <div class="fs-4 fw-bold" style="color: #566a7f;">{{ $jadwalHariIni->batas_toleransi_terlambat }} <span class="fs-6 fw-normal">menit</span></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-success text-center" role="alert">
                                <i class='bx bx-calendar-star me-2'></i>
                                <strong>Hari ini tidak ada jadwal absensi. Selamat beristirahat!</strong>
                            </div>
                        @endif

                        <div class="scanner-viewport">
                            <div id="qr-reader"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card kiosk-card">
                    <div class="card-header">
                        <h5 class="mb-0">Monitoring Kehadiran (Guru & GTK)</h5> {{-- Teks Diubah --}}
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-fill" id="activityTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="scanned-tab" data-bs-toggle="tab" data-bs-target="#scanned-tab-pane" type="button" role="tab" aria-controls="scanned-tab-pane" aria-selected="true">
                                    <i class='bx bx-list-check me-1'></i> Aktivitas Hari Ini
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="unscanned-tab" data-bs-toggle="tab" data-bs-target="#unscanned-tab-pane" type="button" role="tab" aria-controls="unscanned-tab-pane" aria-selected="false">
                                    <i class='bx bx-user-x me-1'></i> Belum Absen <span id="unscanned-count-badge" class="badge rounded-pill badge-center h-px-20 w-px-20 bg-danger ms-1" style="display: none;">0</span>
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content pt-3" id="activityTabContent">
                            <div class="tab-pane fade show active" id="scanned-tab-pane" role="tabpanel" aria-labelledby="scanned-tab" tabindex="0">
                                <div class="input-group input-group-merge mb-3">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input type="text" id="activity-search-input" class="form-control" placeholder="Cari nama guru..."/> {{-- Teks Diubah --}}
                                </div>
                                <ul id="recent-scans-list" class="recent-scans-list">
                                    <li class="text-center border-0" style="animation: none;">Memuat data...</li>
                                </ul>
                                <div id="no-results-message" class="text-center text-muted mt-3" style="display: none;">
                                    Tidak ada aktivitas yang cocok dengan pencarian.
                                </div>
                            </div>

                            <div class="tab-pane fade" id="unscanned-tab-pane" role="tabpanel" aria-labelledby="unscanned-tab" tabindex="0">
                                {{-- Filter Rombel disembunyikan --}}
                                <div class="row g-2 mb-3" style="display: none;">
                                    <div class="col-md-7">
                                        <select id="rombel-filter" class="form-select">
                                            <option value="all">Semua Kelas</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" id="unscanned-search-input-siswa" class="form-control" placeholder="Cari nama..."/>
                                    </div>
                                </div>
                                {{-- Search bar baru untuk guru --}}
                                <div class="input-group input-group-merge mb-3">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input type="text" id="unscanned-search-input" class="form-control" placeholder="Cari nama guru..."/>
                                </div>

                                <ul id="unscanned-list" class="recent-scans-list">
                                       <li id="unscanned-loading" class="text-center border-0" style="animation: none;">Memuat data guru...</li> {{-- Teks Diubah --}}
                                </ul>
                                <div id="no-unscanned-results-message" class="text-center text-muted mt-3" style="display: none;">
                                    Tidak ditemukan guru yang cocok. {{-- Teks Diubah --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="scanResultModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body p-4">
                    <div id="modal-loading-state">
                        <div class="modal-loader"></div>
                        <h4 class="card-title fs-3 mt-3">Memproses...</h4>
                        <p class="card-text fs-5 mb-0">Mohon tunggu sebentar</p>
                    </div>
                    <div id="modal-result-state" style="display: none;">
                        <div class="result-header-new">
                            <div id="modal-status-icon"></div>
                            <img id="modal-student-photo" src="" alt="Foto Guru" class="student-photo-modal"> {{-- Teks Diubah --}}
                        </div>
                        <h4 id="modal-student-name" class="card-title fs-3 mt-3">Nama Guru</h4> {{-- Teks Diubah --}}
                        <p id="modal-status-info" class="card-text fs-5 mb-0">Info Status</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ASET JS DARI CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Inisialisasi variabel dan elemen ---
    const enterFullscreenBtn = document.getElementById('enter-fullscreen-btn');
    const timeEl = document.getElementById('clock-time');
    const dateEl = document.getElementById('clock-date');
    const scanResultModal = new bootstrap.Modal(document.getElementById('scanResultModal'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Ambil CSRF Token
    
    // Elemen untuk Tab 1: Aktivitas Hari Ini
    const recentScansList = document.getElementById('recent-scans-list');
    const searchInput = document.getElementById('activity-search-input');
    const noResultsMessage = document.getElementById('no-results-message');

    // Elemen untuk Tab 2: Belum Absen
    const unscannedList = document.getElementById('unscanned-list');
    const rombelFilter = document.getElementById('rombel-filter'); // Tetap ada, tapi disembunyikan
    const unscannedSearchInput = document.getElementById('unscanned-search-input'); // Pakai yg ini
    const noUnscannedResultsMessage = document.getElementById('no-unscanned-results-message');
    const unscannedCountBadge = document.getElementById('unscanned-count-badge');
    
    let isScanning = false;
    let isAudioReady = false;
    let modalHideTimeout;
    let allUnscannedGurus = []; // Ganti nama variabel

    // --- Fungsi Jam --- (TIDAK BERUBAH)
    function updateClock() {
        const now = new Date();
        timeEl.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':');
        dateEl.textContent = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }
    setInterval(updateClock, 1000);
    updateClock();

    // --- FUNGSI UNTUK TAB 1: AKTIVITAS HARI INI ---

    // Fungsi filterActivityList (TIDAK BERUBAH)
    function filterActivityList() {
        const searchText = searchInput.value.toLowerCase();
        const allActivities = recentScansList.querySelectorAll('li');
        let visibleCount = 0;
        allActivities.forEach(activity => {
            const scanNameElement = activity.querySelector('.scan-name');
            if (!scanNameElement) return;
            const personName = scanNameElement.textContent.toLowerCase();
            if (personName.includes(searchText)) {
                activity.style.display = 'flex';
                visibleCount++;
            } else {
                activity.style.display = 'none';
            }
        });
        if (noResultsMessage) {
            noResultsMessage.style.display = (visibleCount === 0 && allActivities.length > 0 && searchText !== '') ? 'block' : 'none';
        }
    }

    // [DIUBAH] Fungsi createScanListItem
    function createScanListItem(scanData, eventType) {
        const li = document.createElement('li');
        li.id = `scan-${scanData.id}-${eventType}`;
        let displayTime, statusBadge, rawTime;

        const person = scanData.gtk; // GANTI DARI 'siswa'
        if (!person) return null; 

        if (eventType === 'pulang') {
            rawTime = scanData.jam_pulang;
            displayTime = new Date(`1970-01-01T${rawTime}`).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            statusBadge = `<span class="badge rounded-pill bg-label-info status-badge">Pulang</span>`;
        } else {
            rawTime = scanData.jam_masuk;
            displayTime = new Date(`1970-01-01T${rawTime}`).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            const statusKehadiran = scanData.status_kehadiran;
            if (statusKehadiran === 'Tepat Waktu') statusBadge = `<span class="badge rounded-pill bg-label-success status-badge">Masuk</span>`;
            else if (statusKehadiran === 'Terlambat') statusBadge = `<span class="badge rounded-pill bg-label-warning status-badge">Terlambat</span>`;
            else statusBadge = `<span class="badge rounded-pill bg-label-secondary status-badge">${statusKehadiran}</span>`;
        }
        li.setAttribute('data-time', rawTime);

        // Ganti 'person.nama' dan 'person.foto'
        const photoUrl = person.foto ? `{{ asset('storage') }}/${person.foto}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(person.nama)}&background=696cff&color=fff&size=45`;
        li.innerHTML = `<img src="${photoUrl}" class="scan-photo" alt="Foto ${person.nama}"><div class="scan-details"><div class="scan-name">${person.nama}</div><div class="scan-time">Jam: ${displayTime}</div></div>${statusBadge}`;
        return li;
    }

    // Fungsi insertIntoSortedList (TIDAK BERUBAH)
    function insertIntoSortedList(newItem) {
        if (!newItem) return;
        const listContainer = recentScansList;
        const existingItems = listContainer.querySelectorAll('li[data-time]');
        const newItemTime = newItem.getAttribute('data-time');
        let inserted = false;
        for (const existingItem of existingItems) {
            if (newItemTime.localeCompare(existingItem.getAttribute('data-time')) > 0) {
                listContainer.insertBefore(newItem, existingItem);
                inserted = true;
                break;
            }
        }
        if (!inserted) listContainer.appendChild(newItem);
        newItem.style.backgroundColor = '#e7f5ff';
        newItem.style.transition = 'background-color 1.5s ease-out';
        setTimeout(() => { newItem.style.backgroundColor = ''; }, 2000);
        filterActivityList();
    }

    // Fungsi addOrUpdateActivityList (TIDAK BERUBAH)
    function addOrUpdateActivityList(scanData) {
        const placeholder = recentScansList.querySelector('.text-muted, .border-0');
        if (placeholder) placeholder.remove();
        if (scanData.jam_masuk && !document.getElementById(`scan-${scanData.id}-masuk`)) {
            insertIntoSortedList(createScanListItem(scanData, 'masuk'));
        }
        if (scanData.jam_pulang && !document.getElementById(`scan-${scanData.id}-pulang`)) {
            insertIntoSortedList(createScanListItem(scanData, 'pulang'));
        }
    }

    // [DIUBAH] Fungsi fetchInitialScans
    function fetchInitialScans() {
        // GANTI ROUTE
        fetch("{{ route('admin.absensi.gtk.get_todays_scans') }}")
            .then(response => response.json()).then(data => {
                recentScansList.innerHTML = '';
                if (data.length === 0) recentScansList.innerHTML = '<li class="text-center text-muted border-0" style="animation: none;">Belum ada aktivitas.</li>';
                else data.forEach(scan => addOrUpdateActivityList(scan));
            }).catch(error => console.error("Gagal mengambil data absensi awal:", error));
    }

    // [DIUBAH] Fungsi startPolling
    function startPolling() {
        setInterval(() => {
            // GANTI ROUTE
            fetch("{{ route('admin.absensi.gtk.get_recent_scans') }}")
                .then(response => response.json()).then(newScans => {
                    if (newScans.length > 0) {
                        newScans.forEach(scan => addOrUpdateActivityList(scan));
                        fetchAndDisplayUnscanned(false);
                    }
                }).catch(error => console.error("Polling error:", error));
        }, 5000);
    }
    
    // --- FUNGSI UNTUK TAB 2: GURU BELUM ABSEN ---

    // [DIUBAH] Fungsi renderUnscannedList
    function renderUnscannedList() {
        const searchText = unscannedSearchInput.value.toLowerCase();
        unscannedList.innerHTML = ''; 
        
        // Ganti nama variabel
        const filteredGurus = allUnscannedGurus.filter(guru => 
            guru.nama.toLowerCase().includes(searchText)
        );

        if (filteredGurus.length === 0) {
            if(noUnscannedResultsMessage) noUnscannedResultsMessage.style.display = 'block';
        } else {
            if(noUnscannedResultsMessage) noUnscannedResultsMessage.style.display = 'none';
            // Ganti nama variabel
            filteredGurus.forEach(guru => {
                const li = document.createElement('li');
                const photoUrl = guru.foto ? `{{ asset('storage') }}/${guru.foto}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(guru.nama)}&background=696cff&color=fff&size=45`;
                li.innerHTML = `<img src="${photoUrl}" class="scan-photo" alt="Foto ${guru.nama}"><div class="scan-details"><div class="scan-name">${guru.nama}</div></div>`;
                unscannedList.appendChild(li);
            });
        }
    }

    // [DIUBAH] Fungsi fetchAndDisplayUnscanned
    async function fetchAndDisplayUnscanned(isInitialLoad = false) {
        // GANTI ROUTE
        const url = new URL("{{ route('admin.absensi.gtk.get_unscanned_data') }}");
        
        // Logika filter rombel dihapus
        // const selectedRombel = rombelFilter.value;
        // url.searchParams.append('rombel_id', selectedRombel);

        try {
            const response = await fetch(url);
            if (!response.ok) { 
                throw new Error(`Server error: ${response.status}`);
            }
            const data = await response.json();

            // Ganti nama variabel
            allUnscannedGurus = data.unscanned_students; // Controller mengirim key 'unscanned_students'

            if (unscannedCountBadge) {
                if (allUnscannedGurus.length > 0) {
                    unscannedCountBadge.textContent = allUnscannedGurus.length;
                    unscannedCountBadge.style.display = 'inline-block';
                } else {
                    unscannedCountBadge.style.display = 'none';
                }
            }
            
            // Logika populate rombel dihapus
            
            const loadingLi = document.getElementById('unscanned-loading');
            if (loadingLi) loadingLi.remove();

            renderUnscannedList();

        } catch (error) {
            console.error("Gagal mengambil data guru belum absen:", error);
            unscannedList.innerHTML = '<li class="text-center text-danger border-0">Gagal memuat data.</li>';
        }
    }

    // --- FUNGSI INTI & PEMINDAI ---
    
    // Fungsi speak (TIDAK BERUBAH)
    function speak(text) {
        try { if (!isAudioReady) return; window.speechSynthesis.cancel(); const ucapan = new SpeechSynthesisUtterance(text); ucapan.lang = 'id-ID'; ucapan.rate = 1.1; window.speechSynthesis.speak(ucapan); } catch (e) { console.error("Gagal memutar suara:", e); }
    }

    // [DIUBAH] Fungsi onScanSuccess
    function onScanSuccess(decodedText, decodedResult) {
        if (isScanning) return;
        isScanning = true;
        showFeedback('loading');
        // GANTI ROUTE
        fetch("{{ route('admin.absensi.gtk.scan') }}", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': csrfToken // Gunakan variabel csrfToken
            },
            body: JSON.stringify({ token: decodedText })
        })
        .then(response => {
            if (!response.ok) return response.json().then(err => Promise.reject(err));
            return response.json();
        })
        .then(data => {
            if (data.success) {
                let statusType = 'success';
                let subMessage = 'Kehadiran Tercatat.';
                let speechMessage = "berhasil absen";
                const status = data.status;
                const person = data.gtk; // GANTI DARI 'siswa'

                if (status === 'Terlambat') {
                    statusType = 'warning';
                    let keterlambatanParts = [];
                    const menit = parseInt(data.menit_terlambat) || 0;
                    const detik = parseInt(data.detik_terlambat) || 0;
                    if (menit > 0) keterlambatanParts.push(`${menit} menit`);
                    if (detik > 0) keterlambatanParts.push(`${detik} detik`);

                    if (keterlambatanParts.length > 0) {
                        subMessage = `Status: Terlambat ${keterlambatanParts.join(' ')}.`;
                        speechMessage = `terlambat ${keterlambatanParts.join(' ')}`;
                    } else {
                        subMessage = 'Status: Terlambat.';
                        speechMessage = 'terlambat';
                    }
                } else if (status.includes('Pulang')) {
                    statusType = 'info';
                    subMessage = 'Status: Absen Pulang Tercatat.';
                    speechMessage = "berhasil absen pulang";
                } else if (status === 'Tepat Waktu'){
                    speechMessage = "berhasil absen masuk";
                }
                
                // Ganti 'person'
                const fotoUrl = person?.foto ? `{{ asset('storage') }}/${person.foto}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(person.nama)}&background=696cff&color=fff&size=120`;
                showFeedback(statusType, person.nama, subMessage, fotoUrl);
                
                const namaGuru = person?.nama || 'Guru';
                speak(`${namaGuru}, ${speechMessage}`);
            }
        })
        .catch(error => {
            // Ganti 'error.gtk'
            const person = error.gtk; 
            const personName = person ? person.nama : 'Gagal';
            const photoUrl = person?.foto ? `{{ asset('storage') }}/${person.foto}` : `https://ui-avatars.com/api/?name=X&background=ffab00&color=fff&size=120`;
            const errorMessage = error.message || 'Tidak dapat memproses permintaan.';
            showFeedback('warning', personName, errorMessage, photoUrl);
            const nameForSpeech = person ? person.nama : '';
            speak(nameForSpeech ? `${nameForSpeech}, ${errorMessage}` : errorMessage);
        })
        .finally(() => {
            setTimeout(() => { isScanning = false; }, 4500);
        });
    }

    // Fungsi showFeedback (TIDAK BERUBAH)
    function showFeedback(type, studentName, statusInfo, photoUrl) {
        clearTimeout(modalHideTimeout); 
        const modalContent = document.querySelector('#scanResultModal .modal-content'); 
        const loadingState = document.getElementById('modal-loading-state'); 
        const resultState = document.getElementById('modal-result-state'); 
        modalContent.className = `modal-content text-center status-${type}`; 
        if (type === 'loading') { 
            loadingState.style.display = 'block'; 
            resultState.style.display = 'none'; 
        } else { 
            loadingState.style.display = 'none'; 
            resultState.style.display = 'block'; 
            const modalIcon = document.getElementById('modal-status-icon'); 
            const modalPhoto = document.getElementById('modal-student-photo'); 
            const modalName = document.getElementById('modal-student-name'); 
            const modalStatus = document.getElementById('modal-status-info'); 
            let iconClass = 'bx bx-x'; 
            if (type === 'success') iconClass = 'bx bx-check'; 
            else if (type === 'warning') iconClass = 'bx bx-error-circle'; 
            else if (type === 'info') iconClass = 'bx bx-info-circle'; 
            modalIcon.innerHTML = `<i class='${iconClass}'></i>`; 
            modalPhoto.src = photoUrl; 
            modalName.textContent = studentName; 
            modalStatus.textContent = statusInfo; 
        } 
        scanResultModal.show(); 
        if (type !== 'loading') { 
            modalHideTimeout = setTimeout(() => scanResultModal.hide(), 4000); 
        }
    }
    
    // Fungsi startScanner (TIDAK BERUBAH)
    function startScanner() {
        try { 
            const html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: { width: 250, height: 250 } }, false); 
            html5QrcodeScanner.render(onScanSuccess, (error) => {}); 
        } catch (e) { 
            console.error("Gagal memulai scanner:", e); 
            document.getElementById('qr-reader').innerHTML = '<div class="alert alert-danger">Gagal memulai kamera. Pastikan izin telah diberikan.</div>'; 
        }
    }

    // Fungsi handleFullscreenChange (TIDAK BERUBAH)
    function handleFullscreenChange() {
        if (!document.fullscreenElement) {
            document.body.classList.remove('fullscreen-active');
        }
    }
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    
    // Fungsi enterFullscreenBtn (TIDAK BERUBAH)
    enterFullscreenBtn.addEventListener('click', () => {
        document.documentElement.requestFullscreen().catch(err => alert(`Gagal masuk mode layar penuh. Error: ${err.message}`));
        
        document.body.classList.add('fullscreen-active');

        if (!isAudioReady) {
            window.speechSynthesis.speak(new SpeechSynthesisUtterance(''));
            console.log('API Suara siap!');
            isAudioReady = true;
        }
        
        startScanner();
        fetchInitialScans();
        fetchAndDisplayUnscanned(true); 
        startPolling();
    });

    // --- EVENT LISTENERS (DIUBAH) ---
    searchInput.addEventListener('keyup', filterActivityList);
    // Hapus event listener untuk rombel
    // rombelFilter.addEventListener('change', () => fetchAndDisplayUnscanned(false)); 
    unscannedSearchInput.addEventListener('keyup', renderUnscannedList); // Pastikan ini pakai ID yg benar
});
</script>
</body>
</html>