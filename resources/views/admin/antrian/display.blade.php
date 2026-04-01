<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Antrian KCD</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet" />

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
            border-bottom: 2px solid rgba(255,255,255,0.05);
            background: rgba(0,0,0,0.2);
        }
        .tv-header .brand { display: flex; align-items: center; gap: 15px; }
        .tv-header h1 { font-size: 2rem; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .tv-header .clock { font-size: 2.2rem; font-weight: 700; color: var(--highlight); }

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
            border: 2px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
            box-shadow: 0 30px 60px rgba(0,0,0,0.4);
            position: relative;
        }
        .tag-status {
            position: absolute;
            top: 30px;
            left: 30px;
            background: var(--accent-color);
            padding: 8px 25px;
            border-radius: 40px;
            font-weight: 800;
            font-size: 1.1rem;
            text-transform: uppercase;
        }

        .called-no { font-size: 14rem; font-weight: 900; line-height: 1; margin: 0; color: #ffffff; }
        .called-name { font-size: 4rem; font-weight: 800; margin: 20px 0 5px; color: var(--highlight); text-transform: uppercase; }
        .called-destination { font-size: 2rem; color: var(--text-sub); font-weight: 500; }

        .card-waiting {
            background: rgba(0,0,0,0.1);
            border-radius: 2rem;
            padding: 25px;
            display: flex;
            flex-direction: column;
        }
        .waiting-title { font-size: 1.5rem; font-weight: 800; color: var(--text-sub); margin-bottom: 25px; border-bottom: 2px solid rgba(255,255,255,0.05); padding-bottom: 15px; text-transform: uppercase; }
        .waiting-list { overflow-y: hidden; }
        .waiting-item { 
            background: rgba(255,255,255,0.03); 
            margin-bottom: 12px; 
            padding: 15px 25px; 
            border-radius: 1.5rem; 
            display: flex; 
            align-items: center; 
            justify-content: space-between;
        }
        .waiting-item .no { font-size: 2.5rem; font-weight: 900; color: var(--accent-color); }
        .waiting-item .name { font-size: 1.6rem; font-weight: 700; text-align: right; flex: 1; margin-left: 20px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .qr-section-top { 
            background: #ffffff; 
            padding: 20px; 
            border-radius: 1.5rem; 
            display: flex; 
            flex-direction: column; 
            align-items: center;
            text-align: center;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }
        .qr-section-top #qrcode { 
            background: white; 
            padding: 5px;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .qr-text h6 { font-weight: 900; margin: 0; font-size: 1.4rem; color: #162c46; }
        .qr-text p { margin: 5px 0 0; font-size: 0.9rem; color: #666; font-weight: 600; }

        @media (orientation: portrait), (max-width: 900px) {
            .layout-container { grid-template-columns: 100%; height: auto; }
            .called-no { font-size: 10rem; }
            .tv-header h1 { font-size: 1.4rem; }
            .qr-section-top { margin-bottom: 20px; }
        }

        #btnInitManual {
            position: fixed; top: 0; bottom: 0; left:0; right:0;
            z-index: 10000; background: rgba(13, 33, 55, 0.98);
            display: flex; flex-direction: column; justify-content: center; align-items: center; cursor: pointer;
            text-align: center; padding: 20px;
        }
        .hidden-important { display: none !important; }
        .blink { animation: blink 1s infinite; }
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
    </style>
</head>
<body>

<div id="btnInitManual">
    <i class='bx bx-play-circle mb-4 text-primary' style="font-size: 8rem;"></i>
    <h2 class="text-white fw-bold">KLIK UNTUK MENGAKTIFKAN MONITOR</h2>
    <p class="text-white-50">Mengaktifkan Fitur Suara & Layar Penuh</p>
    <div class="mt-4 p-3 border border-secondary rounded" style="background: rgba(255,255,255,0.05); max-width: 500px;">
        <small class="text-info font-monospace">💡 Tips: Gunakan browser <b>Microsoft Edge</b> untuk kualitas suara terbaik (Indonesian Natural Voice).</small>
    </div>
</div>

<div class="tv-header">
    <div class="brand">
        @if($instansi && $instansi->logo)
            <img src="{{ Storage::url($instansi->logo) }}" height="60" alt="Logo">
        @else
            <img src="{{ asset('logo.png') }}" height="60" alt="Logo">
        @endif
        <div>
            <h1>MONITOR ANTRIAN</h1>
            <div id="voiceIndicator" style="font-size: 0.7rem; color: var(--accent-color); font-weight: 700; opacity: 0.8;">Mencari Suara...</div>
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
        <!-- QR Code Section at Top -->
        <div class="qr-section-top">
            <div id="qrcode"></div>
            <div class="qr-text">
                <h6>DAFTAR MANDIRI</h6>
                <p>Scan untuk pendaftaran.</p>
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

    // 1. QR Code
    try {
        new QRCode(document.getElementById("qrcode"), {
            text: window.location.origin + "/buku-tamu",
            width: 140, height: 140
        });
    } catch(e) { console.error("QR Error", e); }

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
    if (speechSynthesis.onvoiceschanged !== undefined) { speechSynthesis.onvoiceschanged = loadVoices; }
    loadVoices();

    // 3. Init Click
    document.getElementById('btnInitManual').addEventListener('click', function() {
        let elem = document.documentElement;
        if (elem.requestFullscreen) { elem.requestFullscreen(); }
        isInitialized = true;
        this.classList.add('hidden-important');
        loadVoices();
        setTimeout(() => { speakText("Monitor antrian telah diaktifkan."); }, 500);
    });

    // 4. Helper Ejaan
    function ejaIndonesia(text) {
        const kamus = {
            '0': 'kosong', '1': 'satu', '2': 'dua', '3': 'tiga', '4': 'empat',
            '5': 'lima', '6': 'enam', '7': 'tujuh', '8': 'delapan', '9': 'sembilan',
            'A': 'A', 'B': 'B', '-' : ' '
        };
        return text.toUpperCase().split('').map(char => kamus[char] || char).join(' ');
    }

    // 5. Speak Core (Anti-Gema)
    function speakText(txt) {
        if (!isInitialized || isSpeaking) return;
        
        isSpeaking = true;
        synth.cancel();
        
        let utter = new SpeechSynthesisUtterance(txt);
        utter.lang = 'id-ID';
        if(voiceIndo) utter.voice = voiceIndo;
        utter.pitch = 1.0;
        utter.rate = 0.88; 
        
        // Lepas grendel jika suara selesai
        utter.onend = function() { isSpeaking = false; };
        utter.onerror = function() { isSpeaking = false; };
        
        synth.speak(utter);
    }

    // 6. Clock
    setInterval(() => { document.getElementById('clock').innerText = new Date().toLocaleTimeString('id-ID'); }, 1000);

    // 7. Update Fetcher
    function fetchUpdates() {
        $.ajax({
            url: "/admin/display-antrian/updates",
            type: "GET",
            success: function(res) {
                if(res.dipanggil && res.dipanggil.length > 0) {
                    let top = res.dipanggil[0];
                    
                    if (lastCalledId !== top.id || lastCallCount !== top.jumlah_panggilan) {
                        lastCalledId = top.id;
                        lastCallCount = top.jumlah_panggilan;
                        
                        if(isInitialized) {
                            document.getElementById('bellSound').play().catch(e => {});
                            setTimeout(() => {
                                let ejaanNomor = ejaIndonesia(top.nomor_antrian);
                                let voiceMsg = `Nomor antrian. ${ejaanNomor}. Atas nama. ${top.nama}. Silakan menuju ke. ${top.tujuan}.`;
                                speakText(voiceMsg);
                            }, 1500);
                        }
                    }

                    $('#lblCallNumber').text(top.nomor_antrian);
                    $('#lblCallName').text(top.nama);
                    $('#lblCallTujuan').text("Tujuan: " + top.tujuan);
                } else {
                    $('#lblCallNumber').text("--");
                    $('#lblCallName').text("SIAP MELAYANI");
                    $('#lblCallTujuan').text("Menunggu Antrian...");
                }

                let html = '';
                if(res.menunggu && res.menunggu.length > 0) {
                    res.menunggu.slice(0, 5).forEach(w => {
                        html += `<div class="waiting-item"><div class="no">${w.nomor_antrian}</div><div class="name">${w.nama}</div></div>`;
                    });
                } else {
                    html = '<p class="text-center text-white-50 mt-5">Tidak ada antrian selanjutnya.</p>';
                }
                $('#waitingListContainer').html(html);
            }
        });
    }

    setInterval(fetchUpdates, 4000); // 4 detik agar tidak terlalu rapat
    fetchUpdates();
</script>
</body>
</html>