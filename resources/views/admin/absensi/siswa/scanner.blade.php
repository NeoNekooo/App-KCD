<!DOCTYPE html>
<html
  lang="id"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('sneat/assets/') }}"
  data-template="vertical-menu-template-free"
>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kios Absensi QR</title>

    @vite(['resources/css/app.css'])
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto+Mono:wght@700&display=swap" rel="stylesheet">

    <style>
        /* --- CSS CORE KIOSK --- */
        :root {
            --kiosk-bg-light: #f5f5f9; --kiosk-border-color: #d9dee3;
            --kiosk-text-primary: #566a7f; --kiosk-text-secondary: #697a8d;
            --kiosk-primary: #696cff; --kiosk-success: #71dd37;
            --kiosk-warning: #ffab00; --kiosk-info: #03c3ec;
            --kiosk-danger: #ff3e1d;
        }

        #fullscreen-prompt {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(21, 22, 37, 0.95);
            display: flex; justify-content: center; align-items: center;
            z-index: 9999; backdrop-filter: blur(5px);
            flex-direction: column; color: white; text-align: center;
        }
        #main-content { display: none; opacity: 0; transition: opacity 0.5s ease-in-out; }
        body.fullscreen-active #main-content { display: block; opacity: 1; }
        body.fullscreen-active #fullscreen-prompt { display: none; }

        body { background-color: var(--kiosk-bg-light); font-family: 'Poppins', sans-serif; overflow-x: hidden; }

        .card.kiosk-card { box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.15); border: none; border-radius: 0.8rem; height: 100%; }
        .card-header { background: transparent; border-bottom: 1px solid var(--kiosk-border-color); padding: 1.5rem; }

        /* Jam Digital */
        .clock-container {
            background: linear-gradient(135deg, #696cff 0%, #4f52e6 100%);
            color: white; padding: 1.5rem; border-radius: 0.8rem;
            text-align: center; margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(105, 108, 255, 0.4);
        }
        .clock-time { font-family: 'Roboto Mono', monospace; font-size: 3rem; font-weight: 700; letter-spacing: 1px; line-height: 1; margin-bottom: 0.5rem; }
        .clock-date { font-size: 1rem; opacity: 0.9; }

        /* Scanner Area */
        .scanner-viewport {
            width: 100%; max-width: 320px; margin: 0 auto;
            position: relative; aspect-ratio: 1 / 1;
            border-radius: 1rem; overflow: hidden;
            border: 4px solid var(--kiosk-primary);
            background: black; box-shadow: 0 0 20px rgba(105, 108, 255, 0.2);
        }
        #qr-reader {
            width: 100%; height: 100%; position: relative; overflow: hidden; background: #000;
        }

        /* FIX VIDEO CSS: Agar tidak hijau/gepeng */
        #qr-reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important; /* Kunci agar full screen kotak */
            border-radius: 0.8rem;
        }

        .scan-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='100%25' height='100%25' fill='none' rx='20' ry='20' stroke='%23ffffff' stroke-width='4' stroke-dasharray='50%2c 50' stroke-dashoffset='0' stroke-linecap='square'/%3E%3C/svg%3E");
            opacity: 0.6; pointer-events: none; z-index: 10;
        }
        .scan-laser {
            position: absolute; width: 100%; height: 2px; background: #ef3e3e;
            box-shadow: 0 0 4px #ef3e3e; top: 50%; z-index: 11; animation: scanMove 2s infinite alternate;
        }
        @keyframes scanMove { 0% { top: 10%; } 100% { top: 90%; } }

        /* List & Badge */
        .recent-scans-list { list-style: none; padding: 0; margin: 0; max-height: 500px; overflow-y: auto; }
        .recent-scans-list li { display: flex; align-items: center; padding: 1rem; border-bottom: 1px solid var(--kiosk-border-color); background: white; transition: background 0.3s; }
        .recent-scans-list li:last-child { border-bottom: none; }
        .scan-photo { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; margin-right: 1rem; border: 2px solid var(--kiosk-border-color); }
        .scan-details { flex-grow: 1; }
        .scan-name { font-weight: 600; color: var(--kiosk-text-primary); margin-bottom: 0.2rem; }

        /* Modal Styles */
        .modal-content { border: none; border-radius: 1rem; overflow: hidden; }
        .status-success .modal-header { background-color: var(--kiosk-success); color: white; }
        .status-warning .modal-header { background-color: var(--kiosk-warning); color: white; }
        .status-danger .modal-header { background-color: var(--kiosk-danger); color: white; }
        .status-info .modal-header { background-color: var(--kiosk-info); color: white; }
        #modal-student-photo { width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 5px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.2); margin-top: -70px; background: white; }

        .loader { width: 48px; height: 48px; border: 5px solid #FFF; border-bottom-color: var(--kiosk-primary); border-radius: 50%; display: inline-block; animation: rotation 1s linear infinite; }
        @keyframes rotation { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* Camera Select */
        #camera-select-container { margin-bottom: 1rem; width: 100%; max-width: 320px; }
        .form-select-camera { background-color: #f0f2f4; border: 1px solid transparent; font-size: 0.85rem; font-weight: 600; text-align: center; }
        .form-select-camera:focus { box-shadow: none; border-color: var(--kiosk-primary); }
    </style>
</head>

<body>
    <div id="fullscreen-prompt">
        <div class="mb-4"><i class='bx bx-qr-scan' style="font-size: 5rem; color: #696cff;"></i></div>
        <h2 class="mb-4">Mode Kios Absensi</h2>
        <button id="enter-fullscreen-btn" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg">
            <i class='bx bx-fullscreen me-2'></i> Mulai Aplikasi
        </button>
        <p class="mt-3 text-white-50 small">Tekan tombol di atas untuk masuk ke mode layar penuh.</p>
    </div>

    @include('layouts.partials.toast')

    <div class="container-fluid p-3 h-100" id="main-content">
        <div class="row h-100 g-3">

            <div class="col-lg-7 d-flex flex-column">
                <div class="card kiosk-card flex-fill">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">

                        <div class="clock-container w-100 mb-3">
                            <div id="clock-time" class="clock-time">--:--:--</div>
                            <div id="clock-date" class="clock-date">Memuat Tanggal...</div>
                        </div>

                        @if(isset($infoLibur) && $infoLibur)
                            <div class="alert alert-warning w-100 text-center mb-4 p-4 shadow-sm border-2 border-warning">
                                <i class='bx bx-calendar-event bx-lg mb-2 text-warning'></i>
                                <h3 class="fw-bold text-dark mb-1">HARI INI LIBUR</h3>
                                <p class="fs-5 mb-0 text-muted">{{ $infoLibur->keterangan }}</p>
                            </div>
                        @elseif ($jadwalHariIni && $jadwalHariIni->is_active)
                            <div class="d-flex justify-content-center gap-3 text-center mb-4 w-100">
                                <div class="p-3 rounded bg-label-primary flex-fill">
                                    <div class="small text-uppercase fw-bold opacity-75"><i class='bx bx-log-in-circle'></i> Masuk</div>
                                    <div class="fs-4 fw-bold text-primary">{{ date('H:i', strtotime($jadwalHariIni->jam_masuk_sekolah)) }}</div>
                                </div>
                                <div class="p-3 rounded bg-label-info flex-fill">
                                    <div class="small text-uppercase fw-bold opacity-75"><i class='bx bx-log-out-circle'></i> Pulang</div>
                                    <div class="fs-4 fw-bold text-info">{{ date('H:i', strtotime($jadwalHariIni->jam_pulang_sekolah)) }}</div>
                                </div>
                                <div class="p-3 rounded bg-label-warning flex-fill">
                                    <div class="small text-uppercase fw-bold opacity-75"><i class='bx bx-timer'></i> Toleransi</div>
                                    <div class="fs-4 fw-bold text-warning">{{ $jadwalHariIni->batas_toleransi_terlambat }} <span class="fs-6 text-muted">mnt</span></div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info w-100 text-center mb-4">
                                <i class='bx bx-calendar-x me-2'></i> Hari ini tidak ada jadwal absensi aktif.
                            </div>
                        @endif

                        <div id="camera-select-container" style="display: none;">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-0"><i class='bx bx-camera'></i></span>
                                <select id="camera-select" class="form-select form-select-camera rounded-pill">
                                    <option value="" disabled selected>Pilih Kamera...</option>
                                </select>
                            </div>
                        </div>

                        <div class="scanner-viewport">
                            <div id="qr-reader"></div>
                            <div class="scan-overlay"></div>
                            <div class="scan-laser"></div>
                        </div>
                        <div class="mt-3 text-muted small"><i class='bx bx-scan'></i> Arahkan QR Code ke kamera</div>

                    </div>
                </div>
            </div>

            <div class="col-lg-5 d-flex flex-column">
                <div class="card kiosk-card flex-fill overflow-hidden">
                    <div class="card-header bg-white pb-0 border-0">
                        <ul class="nav nav-pills nav-fill" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active rounded-pill" data-bs-toggle="tab" data-bs-target="#tab-activity">
                                    <i class='bx bx-history me-1'></i> Aktivitas
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link rounded-pill" data-bs-toggle="tab" data-bs-target="#tab-unscanned">
                                    <i class='bx bx-user-x me-1'></i> Belum Absen
                                    <span id="unscanned-badge" class="badge bg-danger rounded-circle ms-1" style="display:none">0</span>
                                </button>
                            </li>
                        </ul>
                        <div class="mt-3 mb-2">
                             <input type="text" id="search-input" class="form-control rounded-pill bg-light border-0" placeholder="Cari nama siswa...">
                        </div>
                    </div>

                    <div class="card-body p-0 overflow-hidden">
                        <div class="tab-content h-100">
                            <div class="tab-pane fade show active h-100" id="tab-activity">
                                <ul id="recent-list" class="recent-scans-list h-100">
                                    <li class="justify-content-center text-muted py-5">
                                        <div class="spinner-border text-primary" role="status"></div>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-pane fade h-100" id="tab-unscanned">
                                <div class="px-3 pb-2">
                                    <select id="rombel-select" class="form-select form-select-sm rounded-pill">
                                        <option value="all">Semua Kelas</option>
                                    </select>
                                </div>
                                <ul id="unscanned-list" class="recent-scans-list h-100">
                                    <li class="justify-content-center text-muted">Memuat data...</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="feedbackModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header justify-content-center pb-5 border-0">
                    <h5 class="modal-title text-white fw-bold" id="modal-title">Memproses...</h5>
                </div>
                <div class="modal-body text-center pt-0 relative">
                    <img id="modal-student-photo" src="" alt="" style="display: none;">
                    <div id="modal-loader" class="py-4"><div class="loader"></div></div>
                    <div id="modal-result" style="display: none;" class="mt-4">
                        <h4 id="modal-student-name" class="fw-bold mb-1">Nama Siswa</h4>
                        <p id="modal-message" class="mb-0 text-muted">Pesan Status</p>
                        <div id="modal-detail" class="mt-2 badge bg-label-secondary fs-6"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js'])
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- VARIABLES ---
        const enterBtn = document.getElementById('enter-fullscreen-btn');
        const feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
        const recentList = document.getElementById('recent-list');
        const unscannedList = document.getElementById('unscanned-list');
        const searchInput = document.getElementById('search-input');
        const rombelSelect = document.getElementById('rombel-select');
        const unscannedBadge = document.getElementById('unscanned-badge');

        const cameraSelect = document.getElementById('camera-select');
        const cameraSelectContainer = document.getElementById('camera-select-container');

        let isScanning = false;
        let isAudioReady = false;
        let scanTimeout;
        let allUnscannedData = [];
        let html5QrCode = null;
        let currentCameraId = localStorage.getItem('selectedCameraId') || null;

        // --- FULLSCREEN & START ---
        enterBtn.addEventListener('click', () => {
            document.documentElement.requestFullscreen().catch(e => console.log(e));
            document.body.classList.add('fullscreen-active');
            if(!isAudioReady) {
                window.speechSynthesis.speak(new SpeechSynthesisUtterance(''));
                isAudioReady = true;
            }

            initCameraSelection();
            startClock();
            fetchInitialData();
            setInterval(fetchRecentScans, 5000);
        });

        function startClock() {
            function update() {
                const now = new Date();
                document.getElementById('clock-time').innerText = now.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'}).replace(/\./g, ':');
                document.getElementById('clock-date').innerText = now.toLocaleDateString('id-ID', {weekday:'long', day:'numeric', month:'long', year:'numeric'});
            }
            setInterval(update, 1000);
            update();
        }

        // --- CAMERA LOGIC ---

        function initCameraSelection() {
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    cameraSelect.innerHTML = '';
                    devices.forEach(device => {
                        const option = document.createElement('option');
                        option.value = device.id;
                        option.text = device.label || `Kamera ${cameraSelect.length + 1}`;
                        cameraSelect.appendChild(option);
                    });

                    if(devices.length > 1) {
                        cameraSelectContainer.style.display = 'block';
                    }

                    const savedIdExists = devices.some(d => d.id === currentCameraId);
                    if (!savedIdExists) currentCameraId = devices[0].id;
                    cameraSelect.value = currentCameraId;

                    startScanner(currentCameraId);
                } else {
                    alert('Tidak ada kamera ditemukan.');
                }
            }).catch(err => {
                console.error("Gagal mendeteksi kamera", err);
                document.getElementById('qr-reader').innerHTML = '<div class="text-white text-center p-4">Gagal akses kamera.</div>';
            });
        }

        function startScanner(cameraId) {
            if(html5QrCode) {
                html5QrCode.stop().then(() => startInternal(cameraId)).catch(() => startInternal(cameraId));
            } else {
                startInternal(cameraId);
            }
        }

        function startInternal(cameraId) {
            html5QrCode = new Html5Qrcode("qr-reader");

            // CONFIG: HAPUS aspectRatio agar tidak hijau
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            html5QrCode.start(cameraId, config, onScanSuccess).then(() => {
                // Atur Mirroring CSS
                const label = cameraSelect.options[cameraSelect.selectedIndex].text.toLowerCase();
                const videoElement = document.querySelector('#qr-reader video');
                if(videoElement) {
                    // Jika kamera belakang/environment -> Normal. Jika depan -> Mirror.
                    if(label.includes('back') || label.includes('environment') || label.includes('belakang')) {
                        videoElement.style.transform = 'scaleX(1)';
                    } else {
                        videoElement.style.transform = 'scaleX(-1)';
                    }
                }
            }).catch(err => console.error(err));
        }

        cameraSelect.addEventListener('change', function() {
            const newId = this.value;
            currentCameraId = newId;
            localStorage.setItem('selectedCameraId', newId);
            startScanner(newId);
        });

        // --- SCAN HANDLER ---

        function onScanSuccess(decodedText) {
            if (isScanning) return;
            isScanning = true;
            showModal('loading');

            fetch("{{ route('admin.absensi.siswa.handle_scan') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ token: decodedText })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw { status: res.status, ...data };
                return data;
            })
            .then(data => handleScanResult('success', data))
            .catch(err => handleScanResult('error', err))
            .finally(() => setTimeout(() => { isScanning = false; }, 3000));
        }

        function handleScanResult(type, data) {
            let title = 'Berhasil', message = data.message, theme = 'success', detail = '', audioText = '';
            let photo = data.siswa?.foto ? `{{ asset('storage') }}/${data.siswa.foto}` : null;
            let name = data.siswa?.nama || 'Siswa';

            if (type === 'success') {
                const status = data.status;
                if (status === 'Terlambat') {
                    theme = 'warning'; title = 'Terlambat';
                    let latemsg = [];
                    if(data.menit_terlambat > 0) latemsg.push(data.menit_terlambat + ' menit');
                    if(data.detik_terlambat > 0) latemsg.push(data.detik_terlambat + ' detik');
                    detail = latemsg.join(' ');
                    audioText = `Maaf ${name}, Anda terlambat ${detail}`;
                } else if (status.includes('Pulang')) {
                    theme = 'info'; title = 'Hati-hati';
                    audioText = `Sampai jumpa ${name}, hati-hati di jalan`;
                } else if (status === 'Izin') {
                    theme = 'info'; title = 'Izin Keluar';
                    audioText = `Izin keluar tercatat untuk ${name}`;
                } else {
                    theme = 'success'; title = 'Berhasil';
                    audioText = `Selamat datang ${name}`;
                }
                fetchRecentScans();
                fetchUnscannedData();
            } else {
                theme = 'danger'; title = 'Gagal';
                if (data.message.includes('Libur')) {
                    theme = 'warning'; title = 'Hari Libur';
                    audioText = `Maaf, hari ini libur.`;
                } else if (data.message.includes('sudah absen')) {
                    theme = 'warning'; title = 'Duplikat';
                    audioText = `Anda sudah absen sebelumnya.`;
                } else {
                    audioText = `Gagal. ${data.message}`;
                }
            }
            showModal('result', { title, message, theme, photo, name, detail });
            speak(audioText);
        }

        function speak(text) {
            if (!isAudioReady) return;
            window.speechSynthesis.cancel();
            const utter = new SpeechSynthesisUtterance(text);
            utter.lang = 'id-ID';
            window.speechSynthesis.speak(utter);
        }

        function showModal(state, data = {}) {
            const modalContent = document.querySelector('.modal-content');
            const loader = document.getElementById('modal-loader');
            const result = document.getElementById('modal-result');
            const titleEl = document.getElementById('modal-title');
            const photoEl = document.getElementById('modal-student-photo');

            modalContent.classList.remove('status-success', 'status-warning', 'status-danger', 'status-info');

            if (state === 'loading') {
                modalContent.classList.add('status-info');
                titleEl.innerText = 'Memproses...';
                loader.style.display = 'block';
                result.style.display = 'none';
                photoEl.style.display = 'none';
                feedbackModal.show();
            } else {
                modalContent.classList.add(`status-${data.theme}`);
                titleEl.innerText = data.title;
                loader.style.display = 'none';
                result.style.display = 'block';

                document.getElementById('modal-student-name').innerText = data.name;
                document.getElementById('modal-message').innerText = data.message;
                document.getElementById('modal-detail').innerText = data.detail || '';
                document.getElementById('modal-detail').style.display = data.detail ? 'inline-block' : 'none';

                photoEl.src = data.photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(data.name)}&background=random`;
                photoEl.style.display = 'block';

                clearTimeout(scanTimeout);
                scanTimeout = setTimeout(() => feedbackModal.hide(), 3500);
            }
        }

        function fetchInitialData() { fetchRecentScans(); fetchUnscannedData(true); }

        function fetchRecentScans() {
            fetch("{{ route('admin.absensi.siswa.get_recent_scans') }}").then(r=>r.json()).then(data => {
                if(data.length === 0) { recentList.innerHTML = '<li class="text-center text-muted border-0 mt-3">Belum ada aktivitas.</li>'; return; }
                recentList.innerHTML = data.map(scan => {
                    const time = new Date(scan.updated_at).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
                    const photo = scan.siswa.foto ? `{{ asset('storage') }}/${scan.siswa.foto}` : `https://ui-avatars.com/api/?name=${scan.siswa.nama}&background=e8e8e8&color=333`;
                    let badge = 'bg-label-secondary';
                    if(scan.status_kehadiran === 'Tepat Waktu') badge = 'bg-label-success';
                    if(scan.status_kehadiran === 'Terlambat') badge = 'bg-label-warning';
                    if(scan.status === 'Pulang') badge = 'bg-label-info';
                    if(scan.status === 'Izin') badge = 'bg-label-primary';
                    return `<li><img src="${photo}" class="scan-photo"><div class="scan-details"><div class="scan-name">${scan.siswa.nama}</div><div class="scan-time">${time}</div></div><span class="badge ${badge}">${scan.status === 'Pulang' ? 'Pulang' : scan.status_kehadiran}</span></li>`;
                }).join('');
            });
        }

        function fetchUnscannedData(init = false) {
            const rid = rombelSelect.value;
            fetch(`{{ route('admin.absensi.siswa.get_unscanned_data') }}?rombel_id=${rid}`).then(r=>r.json()).then(data => {
                if(init && data.rombels) {
                    data.rombels.forEach(r => { const o = document.createElement('option'); o.value=r.id; o.text=r.nama; rombelSelect.appendChild(o); });
                }
                allUnscannedData = data.unscanned_students;
                renderUnscanned();
                unscannedBadge.innerText = allUnscannedData.length;
                unscannedBadge.style.display = allUnscannedData.length > 0 ? 'inline-block' : 'none';
            });
        }

        function renderUnscanned() {
            const term = searchInput.value.toLowerCase();
            const filtered = allUnscannedData.filter(s => s.nama.toLowerCase().includes(term));
            if(filtered.length === 0) { unscannedList.innerHTML = '<li class="text-center text-muted border-0 mt-3">Tidak ditemukan.</li>'; return; }
            unscannedList.innerHTML = filtered.map(s => {
                const photo = s.foto ? `{{ asset('storage') }}/${s.foto}` : `https://ui-avatars.com/api/?name=${s.nama}&background=e8e8e8&color=333`;
                return `<li><img src="${photo}" class="scan-photo"><div class="scan-details"><div class="scan-name">${s.nama}</div><small class="text-danger">Belum Hadir</small></div></li>`;
            }).join('');
        }

        searchInput.addEventListener('input', () => {
            renderUnscanned();
            const term = searchInput.value.toLowerCase();
            recentList.querySelectorAll('li').forEach(row => {
                const name = row.querySelector('.scan-name')?.innerText.toLowerCase();
                if(name) row.style.display = name.includes(term) ? 'flex' : 'none';
            });
        });
        rombelSelect.addEventListener('change', () => fetchUnscannedData(false));
    });
    </script>
</body>
</html>
