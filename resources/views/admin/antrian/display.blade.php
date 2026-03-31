<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian KCD</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400&display=swap" rel="stylesheet" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    
    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <style>
        body {
            background: url("https://images.unsplash.com/photo-1557682250-33bd709cbe85?q=80&w=2029&auto=format&fit=crop") no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            margin: 0;
            overflow: hidden !important; 
            font-family: 'Public Sans', sans-serif;
            color: #fff;
        }
        .overlay-bg {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 0;
        }
        .container-tv {
            position: relative;
            z-index: 1;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 2.5rem;
        }
        
        /* HEADER */
        .tv-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 2rem;
        }
        .tv-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #fff;
            margin: 0;
        }
        .tv-time {
            font-size: 2.2rem;
            font-weight: 700;
        }

        /* MAIN LAYOUT */
        .tv-content {
            display: flex;
            gap: 2.5rem;
            flex-grow: 1;
        }
        .column-left {
            flex: 6.5;
            display: flex;
            flex-direction: column;
        }
        .column-right {
            flex: 3.5;
            display: flex;
            flex-direction: column;
        }

        /* CARDS */
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 2rem;
            padding: 2.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        /* NOW CALLING */
        .calling-title {
            font-size: 2.2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #4fd1c5;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .calling-number {
            font-size: 14rem;
            font-weight: 900;
            text-align: center;
            line-height: 1;
            margin: 1.5rem 0;
            text-shadow: 0 0 30px rgba(79, 209, 197, 0.6);
            color: #fff;
        }
        .calling-name {
            font-size: 3.5rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 0.8rem;
        }
        .calling-destination {
            font-size: 2.2rem;
            font-weight: 400;
            text-align: center;
            color: #cbd5e0;
        }

        /* WAITING LIST */
        .waiting-title {
            font-size: 1.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #fbd38d;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .waiting-list {
            overflow: hidden;
            flex-grow: 1;
        }
        .waiting-item {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 1.5rem;
            padding: 1.8rem 2rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .waiting-no {
            font-size: 3rem;
            font-weight: 800;
            color: #fbd38d;
            width: 160px;
        }
        .waiting-info {
            flex-grow: 1;
        }
        .waiting-name {
            font-size: 2rem;
            font-weight: 700;
        }
        
        /* QR CODE SECTION */
        .qr-section {
            margin-top: auto;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        #qrcode {
            background: #fff;
            padding: 0.8rem;
            border-radius: 1rem;
        }
        .qr-text h5 {
            font-size: 1.4rem;
            font-weight: 800;
            margin: 0;
            color: #4fd1c5;
        }
        .qr-text p {
            font-size: 1rem;
            margin: 0.4rem 0 0;
            color: #cbd5e0;
        }

        /* START BUTTON FLOAT */
        #btnInitManual {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            padding: 10px 20px;
            background: rgba(79, 209, 197, 0.8);
            border: none;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        #btnInitManual:hover {
            background: #4fd1c5;
            transform: scale(1.05);
        }
        .hidden-important {
            display: none !important;
        }
    </style>
</head>
<body>

<!-- Tombol Kecil Manual agar tidak menghalangi layar -->
<button id="btnInitManual">
    <i class='bx bx-play-circle me-1'></i> AKTIFKAN SUARA & FULLSCREEN
</button>

<div class="overlay-bg"></div>

<div class="container-tv">
    <div class="tv-header">
        <div class="d-flex align-items-center">
            <i class='bx bxs-institution text-primary me-3' style="font-size: 3.5rem;"></i>
            <h1>Layanan Terpadu KCD</h1>
        </div>
        <div class="tv-time" id="clock">--:--:--</div>
    </div>

    <div class="tv-content">
        <!-- Call Screen -->
        <div class="column-left">
            <div class="glass-card h-100 d-flex flex-column justify-content-center">
                <div class="calling-title"><i class='bx bx-broadcast me-2'></i>Sedang Memanggil</div>
                
                <div id="callingContainer">
                    <div class="calling-number" id="lblCallNumber">--</div>
                    <div class="calling-name" id="lblCallName">Antrian Belum Dimulai</div>
                    <div class="calling-destination" id="lblCallTujuan"></div>
                </div>
            </div>
        </div>

        <!-- Waiting List -->
        <div class="column-right">
            <div class="glass-card h-100 d-flex flex-column">
                <div class="waiting-title">Daftar Tunggu</div>
                <div class="waiting-list mb-4" id="waitingListContainer">
                    <!-- Dynamic -->
                </div>

                <!-- QR Code Info -->
                <div class="qr-section">
                    <div id="qrcode"></div>
                    <div class="qr-text">
                        <h5>Scan QR Code</h5>
                        <p>Silakan scan untuk mengambil nomor antrian secara mandiri.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audio -->
<audio id="bellSound" preload="auto">
    <source src="https://www.myinstants.com/media/sounds/elevator-ding.mp3" type="audio/mpeg">
</audio>

<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    let isInitialized = false;
    let synth = window.speechSynthesis;
    let voiceIndo = null;
    let lastCalledIds = [];

    // Initialize QR Code Safely
    try {
        let guestBookUrl = "{{ route('guest.buku-tamu') }}";
        new QRCode(document.getElementById("qrcode"), {
            text: guestBookUrl,
            width: 120,
            height: 120,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    } catch(e) { console.error("QR Error", e); }

    // Init Voices
    function loadVoices() {
        let v = synth.getVoices();
        voiceIndo = v.find(x => x.lang.includes('id'));
        if(!voiceIndo && v.length > 0) voiceIndo = v[0];
    }
    if (speechSynthesis.onvoiceschanged !== undefined) {
        speechSynthesis.onvoiceschanged = loadVoices;
    }

    // Manual Activation Click
    document.getElementById('btnInitManual').addEventListener('click', function() {
        // 1. Fullscreen
        let elem = document.documentElement;
        if (elem.requestFullscreen) { elem.requestFullscreen(); }
        else if (elem.webkitRequestFullscreen) { elem.webkitRequestFullscreen(); }
        
        // 2. Set State
        isInitialized = true;
        
        // 3. Hide Button
        this.classList.add('hidden-important');
        
        // 4. Test Voice
        speakText("Sistem antrian diaktifkan.");
    });

    // Clock
    setInterval(() => {
        document.getElementById('clock').innerText = new Date().toLocaleTimeString('id-ID');
    }, 1000);

    function speakText(txt) {
        if (!isInitialized) return;
        synth.cancel();
        let utter = new SpeechSynthesisUtterance(txt);
        if(voiceIndo) utter.voice = voiceIndo;
        utter.rate = 0.85;
        synth.speak(utter);
    }

    function fetchUpdates() {
        $.ajax({
            url: "{{ route('admin.display.antrian.updates') }}",
            type: "GET",
            success: function(res) {
                // Update Calling
                if(res.dipanggil && res.dipanggil.length > 0) {
                    let top = res.dipanggil[0];
                    let key = top.id + "_" + top.jumlah_panggilan;

                    if (!lastCalledIds.includes(key)) {
                        lastCalledIds.push(key);
                        if(lastCalledIds.length > 20) lastCalledIds.shift();
                        
                        if(isInitialized) {
                            document.getElementById('bellSound').play();
                            setTimeout(() => {
                                let voiceMsg = `Panggilan Antrian. Nomor. ${top.nomor_antrian.replace('-', ' ')}... Atas nama. ${top.nama}... Silahkan menuju ke ${top.tujuan}.`;
                                speakText(voiceMsg);
                            }, 1500);
                        }
                    }
                    $('#lblCallNumber').text(top.nomor_antrian);
                    $('#lblCallName').text(top.nama);
                    $('#lblCallTujuan').text("Tujuan: " + top.tujuan);
                }

                // Update Waiting List
                let html = '';
                if(res.menunggu && res.menunggu.length > 0) {
                    let limit = Math.min(res.menunggu.length, 5);
                    for(let i=0; i<limit; i++) {
                        let w = res.menunggu[i];
                        html += `
                        <div class="waiting-item">
                            <div class="waiting-no">${w.nomor_antrian}</div>
                            <div class="waiting-info">
                                <div class="waiting-name">${w.nama}</div>
                                <div class="text-white-50 small mt-1">Tujuan: ${w.tujuan}</div>
                            </div>
                        </div>`;
                    }
                } else {
                    html = '<p class="text-center text-white-50 mt-5">Tidak ada antrian saat ini.</p>';
                }
                $('#waitingListContainer').html(html);
            }
        });
    }

    setInterval(fetchUpdates, 3000);
    fetchUpdates();
</script>
</body>
</html>
