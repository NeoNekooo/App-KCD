<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu KCD - Digital Queue</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />

    <style>
        :root {
            --primary-color: #696cff;
            --secondary-color: #71dd37;
            --accent-color: #03c3ec;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.4);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #696cff 0%, #3f4191 100%);
            background-attachment: fixed;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Abstract blobs for background eye-candy */
        .blob {
            position: fixed;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            filter: blur(80px);
            border-radius: 50%;
            z-index: -1;
            animation: move 20s infinite alternate;
        }
        .blob-1 { top: -100px; left: -100px; background: rgba(3, 195, 236, 0.2); }
        .blob-2 { bottom: -100px; right: -100px; background: rgba(113, 221, 55, 0.2); }

        @keyframes move {
            from { transform: translate(0, 0); }
            to { transform: translate(100px, 100px); }
        }

        .guestbook-container {
            width: 100%;
            max-width: 550px;
            perspective: 1000px;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: fadeInCard 0.8s ease-out;
        }

        @keyframes fadeInCard {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-header-gradient {
            background: linear-gradient(to bottom right, #696cff, #4345eb);
            padding: 3rem 2rem;
            text-align: center;
            color: white;
            position: relative;
        }

        .brand-icon-wrapper {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-size: 2.5rem;
        }

        /* FORM STYLING */
        .form-label {
            font-weight: 600;
            color: #444;
            font-size: 0.9rem;
            margin-bottom: 0.6rem;
            display: block;
        }

        .input-group-modern {
            position: relative;
            margin-bottom: 1.8rem;
        }

        .form-control-modern {
            width: 100%;
            padding: 1rem 1.2rem;
            border-radius: 1rem;
            border: 2px solid #edeff2;
            background: #fcfcfd;
            font-size: 1rem;
            transition: all 0.3s;
            color: #333;
            outline: none;
        }

        .form-control-modern:focus {
            border-color: var(--primary-color);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(105, 108, 255, 0.1);
        }

        /* TICKET DESIGN */
        .ticket-box {
            background: #fff;
            border-radius: 1.5rem;
            padding: 2.5rem;
            position: relative;
            border: 2px solid #f1f1f1;
        }
        .ticket-box::before, .ticket-box::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30px;
            height: 30px;
            background: #e9ecef; /* match wrapper bg */
            border-radius: 50%;
            transform: translateY(-50%);
        }
        .ticket-box::before { left: -17px; }
        .ticket-box::after { right: -17px; }

        .ticket-divider {
            border-top: 2px dashed #eee;
            margin: 1.5rem 0;
            position: relative;
        }

        .ticket-no-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #888;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .ticket-number-display {
            font-size: 4.5rem;
            font-weight: 900;
            color: var(--primary-color);
            margin: 0.5rem 0;
            line-height: 1;
        }

        .btn-submit {
            background: linear-gradient(135deg, #696cff 0%, #4345eb 100%);
            color: white;
            padding: 1.2rem;
            border-radius: 1.2rem;
            font-weight: 700;
            font-size: 1.1rem;
            border: none;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(105, 108, 255, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(105, 108, 255, 0.4);
        }

        .btn-submit:active {
            transform: translateY(-1px);
        }

    </style>
</head>
<body>

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

<div class="guestbook-container">

    @if(session('tiket_success'))
        {{-- SUCCESS TICKET VIEW --}}
        <div class="glass-card">
            <div class="card-header-gradient" style="background: linear-gradient(135deg, #71dd37, #26af48);">
                <div class="brand-icon-wrapper">
                    <i class='bx bx-check-double'></i>
                </div>
                <h2 class="fw-bold text-white m-0">Berhasil Terdaftar!</h2>
                <p class="opacity-75 mb-0">Silakan simpan nomor antrian Anda.</p>
            </div>

            <div class="p-4 p-md-5">
                <div class="ticket-box shadow-sm text-center">
                    <div class="ticket-no-label">Nomor Antrian</div>
                    <div class="ticket-number-display">{{ session('tiket_nomor') }}</div>
                    
                    <div class="ticket-divider"></div>
                    
                    <div class="row text-start g-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Nama Tamu</small>
                            <span class="fw-bold">{{ session('tiket_nama') }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Waktu</small>
                            <span class="fw-bold">{{ session('tiket_waktu') }}</span>
                        </div>
                        <div class="col-12 border-top pt-2">
                            <small class="text-muted d-block">Tujuan Bertemu</small>
                            <span class="fw-bold text-primary">{{ session('tiket_tujuan') ?? 'Layanan Umum' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <a href="{{ route('guest.buku-tamu') }}" class="btn btn-outline-primary rounded-pill px-5 fw-bold">
                        Daftar Baru
                    </a>
                </div>
            </div>
        </div>

    @else
        {{-- FORM INPUT VIEW --}}
        <div class="glass-card">
            <div class="card-header-gradient">
                <div class="brand-icon-wrapper">
                    <i class='bx bxs-user-pin'></i>
                </div>
                <h2 class="fw-bold text-white m-0">Buku Tamu KCD</h2>
                <p class="opacity-75 mt-2 mb-0">Selamat Datang di Kantor Cabang Dinas Pendidikan</p>
            </div>

            <div class="p-4 p-md-5">
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('guest.buku-tamu.store') }}" method="POST">
                    @csrf
                    
                    <div class="input-group-modern">
                        <label class="form-label">NAMA LENGKAP</label>
                        <input type="text" name="nama" class="form-control-modern" placeholder="Masukkan nama sesuai KTP" required value="{{ old('nama') }}" autofocus>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group-modern">
                                <label class="form-label">NIK (OPSIONAL)</label>
                                <input type="text" name="nik" class="form-control-modern" placeholder="No. Induk Kependudukan" value="{{ old('nik') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group-modern">
                                <label class="form-label">INSTANSI / SEKOLAH</label>
                                <input type="text" name="asal_instansi" class="form-control-modern" placeholder="Contoh: SMAN 1 Bandung" required value="{{ old('asal_instansi') }}">
                            </div>
                        </div>
                    </div>

                    <div class="input-group-modern">
                        <label class="form-label">TUJUAN PEJABAT / UNIT</label>
                        <select name="tujuan_pegawai_id" class="form-control-modern" required>
                            <option value="" selected disabled>-- Pilih yang Ingin Ditemui --</option>
                            @foreach($pegawais as $pegawai)
                                <option value="{{ $pegawai->id }}" {{ old('tujuan_pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                                    {{ $pegawai->nama }} ({{ $pegawai->jabatanKcd->nama ?? $pegawai->jabatan }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="input-group-modern">
                        <label class="form-label">MAKSUD & KEPERLUAN</label>
                        <textarea name="keperluan" rows="3" class="form-control-modern" placeholder="Contoh: Koordinasi Dana BOS atau Konsultasi Ijazah" required>{{ old('keperluan') }}</textarea>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn-submit">
                            DAPATKAN NOMOR ANTRIAN <i class='bx bx-right-arrow-alt ms-2'></i>
                        </button>
                    </div>
                    
                    <div class="text-center mt-4">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Logo_Provinsi_Jawa_Barat.png" style="height: 40px; opacity: 0.6; filter: grayscale(1);">
                        <p class="text-muted small mt-2 m-0">&copy; 2024 Dinas Pendidikan KCD</p>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>

<!-- Scripts -->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
</body>
</html>
