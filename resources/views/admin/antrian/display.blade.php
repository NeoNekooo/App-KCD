<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Antrian KCD</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;800;900&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />

    <style>
        :root {
            --bg-color: #0d2137;
            --card-bg: #162c46;
            --accent-color: #696cff;
            --text-heading: #ffffff;
            --text-sub: #a0aec0;
            --highlight: #fbd38d;
        }

        body {
            font-family: 'Public Sans', sans-serif;
            background-color: var(--bg-color);
            color: #ffffff;
            margin: 0;
            overflow-x: hidden;
            min-height: 100vh;
        }

        .tv-header {
            height: 100px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.05);
            background: rgba(0, 0, 0, 0.2);
        }

        .tv-header .brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .tv-header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .tv-header .clock {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--highlight);
        }

        .layout-container {
            display: grid;
            grid-template-columns: 65% 35%;
            gap: 30px;
            padding: 30px 40px;
            height: calc(100vh - 100px);
        }

        .card-called {
            background-color: var(--card-bg);
            border-radius: 2.5rem;
            border: 2px solid rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
            position: relative;
        }

        .tag-status {
            position: absolute;
            top: 20px;
            left: 20px;
            background: var(--accent-color);
            padding: 6px 18px;
            border-radius: 40px;
            font-weight: 800;
            font-size: 0.85rem;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .called-no {
            font-size: 14rem;
            font-weight: 900;
            line-height: 1;
            margin: 0;
            color: #ffffff;
        }

        .called-name {
            font-size: 4rem;
            font-weight: 800;
            margin: 20px 0 5px;
            color: var(--highlight);
            text-transform: uppercase;
        }

        .called-destination {
            font-size: 2rem;
            color: var(--text-sub);
            font-weight: 500;
        }

        .card-waiting {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 2rem;
            padding: 25px;
            display: flex;
            flex-direction: column;
        }

        .waiting-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-sub);
            margin-bottom: 25px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.05);
            padding-bottom: 15px;
            text-transform: uppercase;
        }

        .waiting-list {
            overflow-y: hidden;
        }

        .waiting-item {
            background: rgba(255, 255, 255, 0.03);
            margin-bottom: 12px;
            padding: 15px 25px;
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .waiting-item .no {
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--accent-color);
            width: 100px;
            text-align: center;
            border-right: 2px solid rgba(255, 255, 255, 0.2);
            line-height: 1;
            margin-right: 20px;
            padding-right: 10px;
            flex-shrink: 0;
        }

        .waiting-item .name-text {
            font-size: 1.8rem;
            color: var(--accent-color);
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 350px;
            line-height: 1;
            text-transform: uppercase;
            flex: 1;
        }

        .btn-print-touch {
            background: rgba(105, 108, 255, 0.2);
            color: #ffffff;
            border: 2px solid var(--accent-color);
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            margin-left: 15px;
        }

        .btn-print-touch:active {
            background: var(--accent-color);
            transform: scale(0.9);
        }

        .btn-print-touch i {
            font-size: 1.6rem;
        }

        .qr-section-top {
            background: #ffffff;
            padding: 12px;
            border-radius: 1.2rem;
            display: inline-block;
            margin: 0 auto 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            width: fit-content;
        }

        .qr-section-top #qrcode {
            background: white;
            padding: 0;
            border-radius: 8px;
        }

        .qr-text h6 {
            font-weight: 900;
            margin: 0;
            font-size: 1.4rem;
            color: #162c46;
        }

        .qr-text p {
            margin: 5px 0 0;
            font-size: 0.9rem;
            color: #666;
            font-weight: 600;
        }

        @media (orientation: portrait),
        (max-width: 900px) {
            .layout-container {
                grid-template-columns: 100%;
                height: auto;
                padding: 15px;
                gap: 20px;
            }

            .tv-header {
                padding: 0 20px;
                height: 80px;
            }

            .tv-header h1 {
                font-size: 1.2rem;
            }

            .tv-header .clock {
                font-size: 1.5rem;
            }

            .card-called {
                padding: 30px 20px;
                border-radius: 1.5rem;
            }

            .called-no {
                font-size: 8rem;
            }

            .called-name {
                font-size: 2.5rem;
            }

            .called-destination {
                font-size: 1.4rem;
            }

            .qr-section-top {
                margin-bottom: 50px;
                padding: 12px;
            }

            .qr-section-top #qrcode canvas,
            .qr-section-top #qrcode img {
                width: 120px !important;
                height: 120px !important;
            }

            .waiting-item .no {
                font-size: 1.8rem;
            }

            .waiting-item .name {
                font-size: 1.2rem;
            }

            .waiting-title {
                font-size: 1.2rem;
                margin-bottom: 15px;
            }
        }

        #btnInitManual {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 10000;
            background: rgba(13, 33, 55, 0.98);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            text-align: center;
            padding: 20px;
        }

        .hidden-important {
            display: none !important;
        }

        .blink {
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
</head>

<body>

    <div id="btnInitManual">
        <i class='bx bx-play-circle mb-4 text-primary' style="font-size: 8rem;"></i>
        <h2 class="text-white fw-bold">KLIK UNTUK MENGAKTIFKAN MONITOR</h2>
        <p class="text-white-50">Mengaktifkan Fitur Suara & Layar Penuh</p>
        <div class="mt-4 p-3 border border-secondary rounded"
            style="background: rgba(255,255,255,0.05); max-width: 500px;">
            <small class="text-info font-monospace">💡 Tips: Gunakan browser <b>Microsoft Edge</b> untuk kualitas suara
                terbaik (Indonesian Natural Voice).</small>
        </div>
    </div>

    <div class="tv-header">
        <div class="brand">
            @if ($instansi && $instansi->logo)
                <img src="{{ Storage::url($instansi->logo) }}" height="60" alt="Logo">
            @else
                <img src="{{ asset('logo.png') }}" height="60" alt="Logo">
            @endif
            <div>
                <h1>MONITOR ANTRIAN</h1>
                <div id="voiceIndicator"
                    style="font-size: 0.7rem; color: var(--accent-color); font-weight: 700; opacity: 0.8;">Mencari
                    Suara...</div>
            </div>
        </div>
        <div class="clock" id="clock">00:00:00</div>
    </div>

    <div class="layout-container">
        <div class="card-called">
            <div class="tag-status blink">SEDANG MELAYANI</div>
            <h2 class="called-no" id="lblCallNumber">--</h2>
            <div class="called-name" id="lblCallName">SIAP MELAYANI</div>
            <div class="called-destination" id="lblCallTujuan">Menunggu Antrian...</div>
        </div>

        <div class="card-waiting">
            <!-- QR Code Section at Top (Centering) -->
            <div style="text-align: center; width: 100%;">
                <div class="qr-section-top">
                    <div id="qrcode"></div>
                </div>
            </div>

            <div class="waiting-title">Daftar Tunggu</div>
            <div id="waitingListContainer" class="waiting-list"></div>
        </div>
    </div>

    <audio id="bellSound" preload="auto">
        <source src="https://www.myinstants.com/media/sounds/elevator-ding.mp3" type="audio/mpeg">
    </audio>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        let isInitialized = false;
        let synth = window.speechSynthesis;
        let voiceIndo = null;
        let lastCalledId = null;
        let lastCallCount = 0;
        let isSpeaking = false; // Grendel Anti-Gema

        // 1. QR Code (DI-GEDE-IN)
        try {
            new QRCode(document.getElementById("qrcode"), {
                text: window.location.origin + "/buku-tamu",
                width: 200,
                height: 200
            });
        } catch (e) {
            console.error("QR Error", e);
        }

        // 2. Voice Loader (Mencari suara paling medok)
        function loadVoices() {
            let v = synth.getVoices();
            if (v.length > 0) {
                voiceIndo = v.find(x => x.lang.includes('id') && x.name.includes('Natural')) ||
                    v.find(x => x.lang.includes('id') && x.name.includes('Online')) ||
                    v.find(x => x.lang.includes('id') && x.name.includes('Google')) ||
                    v.find(x => x.lang.includes('id') && x.name.includes('Microsoft')) ||
                    v.find(x => x.lang.includes('id'));

                if (voiceIndo) {
                    document.getElementById('voiceIndicator').innerText = "MODE SUARA: " + voiceIndo.name;
                    document.getElementById('voiceIndicator').style.color = "#00ff00";
                }
            }
        }
        if (speechSynthesis.onvoiceschanged !== undefined) {
            speechSynthesis.onvoiceschanged = loadVoices;
        }
        loadVoices();

        // 3. Init Click
        document.getElementById('btnInitManual').addEventListener('click', function() {
            let elem = document.documentElement;
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            }
            isInitialized = true;
            this.classList.add('hidden-important');
            loadVoices();
            setTimeout(() => {
                speakText("Monitor antrian telah diaktifkan.");
            }, 500);
        });

        // 4. Helper Ejaan
        function ejaIndonesia(text) {
            const kamus = {
                '0': 'kosong',
                '1': 'satu',
                '2': 'dua',
                '3': 'tiga',
                '4': 'empat',
                '5': 'lima',
                '6': 'enam',
                '7': 'tujuh',
                '8': 'delapan',
                '9': 'sembilan',
                'A': 'A',
                'B': 'B',
                '-': ' '
            };
            return text.toUpperCase().split('').map(char => kamus[char] || char).join(' ');
        }

        // 5. Speak Core (Anti-Gema & Anti-Tidur)
        function speakText(txt) {
            if (!isInitialized) return;

            // Jika masih ngomong, stop dulu yang lama
            if (synth.speaking) {
                synth.cancel();
            }

            isSpeaking = true;
            
            // Safety timeout (kunci bakal lepas sendiri setelah 15 detik kalau error)
            let safetyReset = setTimeout(() => {
                isSpeaking = false;
            }, 15000);

            let utter = new SpeechSynthesisUtterance(txt);
            utter.lang = 'id-ID';
            if (voiceIndo) utter.voice = voiceIndo;
            utter.pitch = 1.0;
            utter.rate = 0.90; // Sedikit lebih cepat agar natural

            utter.onend = function() {
                clearTimeout(safetyReset);
                isSpeaking = false;
            };

            utter.onerror = function(event) {
                console.error("TTS Error:", event);
                clearTimeout(safetyReset);
                isSpeaking = false;
            };

            // Trik Chrome: Resume sebelum speak & panggil 3x biar bangun
            synth.resume();
            synth.speak(utter);
        }

        // Trik Tambahan: Jaga biar TTS gak 'tidur' tiap 10 detik
        setInterval(() => {
            if (isInitialized && !synth.speaking) {
                synth.resume();
            }
        }, 10000);

        // 6. Clock
        setInterval(() => {
            document.getElementById('clock').innerText = new Date().toLocaleTimeString('id-ID');
        }, 1000);

        // 7. Update Fetcher & Remote Printing
        function fetchUpdates() {
            $.ajax({
                url: "/admin/display-antrian/updates",
                type: "GET",
                success: function(res) {
                    // --- A. Handling Panggilan Suara & Visual ---
                    if (res.dipanggil && res.dipanggil.length > 0) {
                        let top = res.dipanggil[0];

                        if (lastCalledId !== top.id || lastCallCount !== top.jumlah_panggilan) {
                            lastCalledId = top.id;
                            lastCallCount = top.jumlah_panggilan;

                            if (isInitialized) {
                                document.getElementById('bellSound').play().catch(e => {});
                                setTimeout(() => {
                                    let ejaanNomor = ejaIndonesia(top.nomor_antrian);
                                    let voiceMsg =
                                        `Nomor antrian. ${ejaanNomor}. Atas nama. ${top.nama}. Silakan menuju ke resepsionis.`;
                                    speakText(voiceMsg);
                                }, 1500);
                            }
                        }

                        $('#lblCallNumber').text(top.nomor_antrian);
                        $('#lblCallName').text(top.nama);
                        $('#lblCallTujuan').text("Keperluan: " + top.keperluan);
                    } else {
                        $('#lblCallNumber').text("--");
                        $('#lblCallName').text("SIAP MELAYANI");
                        $('#lblCallTujuan').text("Menunggu Antrian...");
                    }

                    // --- B. Daftar Tunggu Kanann ---
                    let html = '';
                    if (res.menunggu && res.menunggu.length > 0) {
                        res.menunggu.slice(0, 5).forEach(w => {
                            html +=
                                `<div class="waiting-item d-flex align-items-center justify-content-between" style="padding: 15px 25px;">
                                    <div style="display: flex; align-items: center; min-width: 0; flex: 1;">
                                        <div class="no" style="margin-bottom: 0;">${w.nomor_antrian}</div>
                                        <div class="name-text">${w.nama}</div>
                                    </div>
                                    <div class="btn-print-touch" onclick="printTicketRemotely(${w.id})" title="Cetak Tiket" 
                                         style="display: flex !important; align-items: center !important; justify-content: center !important; background: rgba(105, 108, 255, 0.3) !important; border: 2px solid #696cff;">
                                        <span style="font-size: 1.8rem;">🖨️</span>
                                    </div>
                                </div>`;
                        });
                    } else {
                        html = '<p class="text-center text-white-50 mt-5">Tidak ada antrian selanjutnya.</p>';
                    }
                    $('#waitingListContainer').html(html);

                    // --- C. REMOTE PRINTING LOGIC ---
                    if (res.to_print && res.to_print.length > 0) {
                        res.to_print.forEach(item => {
                            printTicketRemotely(item.id);
                        });
                    }
                }
            });
        }

        // Fungsi buat nembak Iframe ke Printer
        function printTicketRemotely(id) {
            console.log("Mencetak tiket ID:", id);
            
            // Hapus dulu kalau iframe lama masih ada
            const oldFrame = document.getElementById("printFrame_" + id);
            if (oldFrame) oldFrame.remove();

            // 1. Buat Iframe tersembunyi
            const iframe = document.createElement('iframe');
            iframe.id = "printFrame_" + id;
            // Gunakan gaya yang lebih 'silent' agar tidak mengganggu rendering utama
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            if (oldFrame) oldFrame.remove();
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            iframe.src = "/admin/display-antrian/ticket/" + id;
            document.body.appendChild(iframe);

            // 2. Beri tau server kalau sudah diproses cetak
            $.ajax({
                url: "/admin/display-antrian/mark-printed/" + id,
                type: "PUT",
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function() {
                    console.log("Tiket " + id + " ditandai sudah dicetak.");
                    
                    // Jaga agar tetap Full Screen dengan jeda lebih lama (3 detik)
                    // karena printer butuh waktu buat 'melepaskan' window focus
                    setTimeout(() => {
                        if (isInitialized && !document.fullscreenElement) {
                            console.log("Mencoba balik ke Full Screen...");
                            document.documentElement.requestFullscreen().catch(e => {
                                console.log("Gagal auto-fullscreen, butuh sentuhan user.");
                            });
                        }
                    }, 3000);

                    // Hapus iframe setelah 30 detik biar gak menuhin DOM
                    setTimeout(() => { if(iframe) iframe.remove(); }, 30000);
                }
            });
        }

        // Trik Tambahan: Deteksi kapan print selesai untuk balik ke layar penuh
        window.onafterprint = function() {
            if (isInitialized && !document.fullscreenElement) {
                setTimeout(() => {
                    document.documentElement.requestFullscreen().catch(e => {});
                }, 1000);
            }
        };

        setInterval(fetchUpdates, 4000); // 4 detik agar tidak terlalu rapat
        fetchUpdates();
    </script>
</body>

</html>
