<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian KCD - Monitor Utama</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet" />

    <!-- Icons -->
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
            overflow: hidden;
            height: 100vh;
        }

        .layout-grid {
            display: grid;
            grid-template-columns: 65% 35%;
            height: calc(100vh - 120px);
            gap: 20px;
            padding: 0 40px 40px 40px;
        }

        /* HEADER */
        .tv-header {
            height: 120px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 50px;
            border-bottom: 2px solid rgba(255,255,255,0.05);
            margin-bottom: 20px;
        }
        .tv-header .brand {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .tv-header h1 {
            font-size: 2.8rem;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .tv-header .clock {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--highlight);
        }

        /* MAIN CALLED CARD */
        .card-called {
            background-color: var(--card-bg);
            border-radius: 2rem;
            border: 2px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            position: relative;
        }
        .tag-status {
            position: absolute;
            top: 40px;
            left: 40px;
            background: var(--accent-color);
            padding: 10px 30px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 1.2rem;
            text-transform: uppercase;
        }

        .called-no-wrapper {
            margin-top: 20px;
        }
        .called-no {
            font-size: 15rem;
            font-weight: 900;
            line-height: 1;
            margin: 0;
            color: #ffffff;
            text-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .called-name {
            font-size: 4.5rem;
            font-weight: 800;
            margin: 20px 0 10px;
            color: var(--highlight);
            text-transform: capitalize;
        }
        .called-destination {
            font-size: 2.5rem;
            color: var(--text-sub);
            font-weight: 400;
        }

        /* WAITING SIDEBAR */
        .card-waiting {
            background-color: rgba(255,255,255,0.02);
            border-left: 2px solid rgba(255,255,255,0.05);
            padding: 0 20px;
            display: flex;
            flex-direction: column;
        }
        .waiting-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-sub);
            margin-bottom: 30px;
            border-bottom: 2px solid rgba(255,255,255,0.1);
            padding-bottom: 15px;
            text-transform: uppercase;
        }
        .waiting-item {
            background: rgba(255,255,255,0.05);
            margin-bottom: 15px;
            padding: 20px 30px;
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .waiting-item .no {
            font-size: 2.8rem;
            font-weight: 900;
            color: var(--accent-color);
        }
        .waiting-item .name {
            font-size: 1.8rem;
            font-weight: 700;
            text-align: right;
            flex: 1;
            margin-left: 20px;
        }

        /* FOOTER INFO / QR */
        .qr-strip {
            position: fixed;
            bottom: 40px;
            right: 40px;
            width: 320px;
            background: #ffffff;
            color: #333;
            padding: 20px;
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }
        .qr-strip h6 { font-weight: 800; margin: 0; font-size: 1.1rem; }
        .qr-strip p { margin: 0; font-size: 0.8rem; color: #666; }

        /* MANUAL INIT BUTTON */
        #btnInitManual {
            position: fixed;
            top: 0; bottom: 0; left:0; right:0;
            z-index: 10000;
            background: rgba(13, 33, 55, 0.95);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
        .hidden-important { display: none !important; }
    </style>
</head>
<body>

<!-- OVERLAY BUTTON: WAJIB UNTUK IZIN VOICE BROWSER -->
<div id="btnInitManual">
    <i class='bx bx-play-circle mb-4 text-primary' style="font-size: 8rem;"></i>
    <h2 class="text-white fw-bold">KLIK UNTUK MENGAKTIFKAN MONITOR</h2>
    <p class="text-white-50">Mengaktifkan Fitur Suara Otomatis & Layar Penuh</p>
</div>

<div class="tv-header">
    <div class="brand">
        <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Logo_Provinsi_Jawa_Barat.png" height="70" alt="Logo">
        <h1>MONITOR ANTRIAN TAMU </h1>
    </div>
    <div class="clock" id="clock">00:00:00</div>
</div>

<div class="layout-grid">
    <!-- PANGGILAN UTAMA -->
    <div class="card-called">
        <div class="tag-status">SEDANG MELAYANI</div>
        <div class="called-no-wrapper">
            <h2 class="called-no" id="lblCallNumber">--</h2>
        </div>
        <div class="called-name" id="lblCallName">SIAP MELAYANI</div>
        <div class="called-destination" id="lblCallTujuan">Menunggu Antrian...</div>
    </div>

    <!-- DAFTAR TUNGGU -->
    <div class="card-waiting">
        <div class="waiting-title">Daftar Antrian Selanjutnya</div>
        <div id="waitingListContainer">
            <!-- Dynamic items -->
        </div>
    </div>
</div>

<div class="qr-strip">
    <div id="qrcode"></div>
    <div>
        <h6>DAFTAR MANDIRI</h6>
        <p>Scan untuk mengambil nomor antrian Anda.</p>
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

    // QR Logic
    try {
        let guestBookUrl = "{{ route('guest.buku-tamu') }}";
        new QRCode(document.getElementById("qrcode"), {
            text: guestBookUrl,
            width: 100,
            height: 100,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    } catch(e) { console.error(e); }

    // Init Manual
    document.getElementById('btnInitManual').addEventListener('click', function() {
        let elem = document.documentElement;
        if (elem.requestFullscreen) { elem.requestFullscreen(); }
        else if (elem.webkitRequestFullscreen) { elem.webkitRequestFullscreen(); }
        
        isInitialized = true;
        this.classList.add('hidden-important');
        speakText("Monitor antrian telah diaktifkan.");
    });

    // Voices
    function loadVoices() {
        let v = synth.getVoices();
        voiceIndo = v.find(x => x.lang.includes('id'));
        if(!voiceIndo && v.length > 0) voiceIndo = v[0];
    }
    if (speechSynthesis.onvoiceschanged !== undefined) {
        speechSynthesis.onvoiceschanged = loadVoices;
    }

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
                // Now Calling Update
                if(res.dipanggil && res.dipanggil.length > 0) {
                    let top = res.dipanggil[0];
                    let key = top.id + "_" + top.jumlah_panggilan;

                    if (!lastCalledIds.includes(key)) {
                        lastCalledIds.push(key);
                        if(lastCalledIds.length > 20) lastCalledIds.shift();
                        
                        if(isInitialized) {
                            document.getElementById('bellSound').play();
                            setTimeout(() => {
                                let msg = `Nomor Antrian. ${top.nomor_antrian.replace('-', ' ')}. ${top.nama}. Silahkan menuju ke ${top.tujuan}.`;
                                speakText(msg);
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

                // Waiting List Update
                let html = '';
                if(res.menunggu && res.menunggu.length > 0) {
                    let limit = Math.min(res.menunggu.length, 6);
                    for(let i=0; i<limit; i++) {
                        let w = res.menunggu[i];
                        html += `
                        <div class="waiting-item">
                            <div class="no">${w.nomor_antrian}</div>
                            <div class="name">${w.nama}</div>
                        </div>`;
                    }
                } else {
                    html = '<p class="text-center text-white-50 mt-5">Tidak ada antrian selanjutnya.</p>';
                }
                $('#waitingListContainer').html(html);
            }
        });
    }

    setInterval(fetchUpdates, 2000);
    fetchUpdates();
</script>
</body>
</html>
