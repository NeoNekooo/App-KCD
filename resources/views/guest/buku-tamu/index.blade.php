<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu KCD - Pelayanan Publik</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />

    <style>
        :root {
            --kcd-navy: #0D2137;
            --kcd-blue: #1A374D;
            --kcd-accent: #696cff;
            --body-bg: #F4F7F9;
        }

        body {
            font-family: 'Public Sans', sans-serif;
            background-color: var(--body-bg);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
        }

        .main-wrapper {
            width: 100%;
            max-width: 600px; /* Gue lebarin dikit biar gak terlalu sesek */
        }

        .official-card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e1e8ed;
            overflow: hidden;
            width: 100%;
        }

        .official-header {
            background-color: var(--kcd-navy);
            padding: 2.5rem 2rem;
            text-align: center;
            color: #ffffff;
        }

        .logo-box {
            height: 65px;
            margin-bottom: 1.2rem;
            object-fit: contain;
        }

        .official-header h2 {
            font-size: 1.6rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: 1px;
        }

        .official-header p {
            font-size: 0.85rem;
            opacity: 0.85;
            margin-top: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1.4;
        }

        .form-body {
            padding: 2rem; /* Padding utama yang konsisten */
        }

        /* Fix Simetris: Gunakan gutter Bootstrap asli */
        .form-body .row {
            margin-bottom: 0; 
        }

        .section-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--kcd-navy);
            margin-bottom: 0.5rem;
            margin-top: 1rem;
            display: block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .official-input {
            width: 100%;
            padding: 0.85rem 1rem;
            border-radius: 0.6rem;
            border: 1.5px solid #d1d9e0;
            background: #ffffff;
            font-size: 0.95rem;
            color: #333;
            margin-bottom: 1rem;
            transition: all 0.2s;
            display: block;
        }

        .official-input:focus {
            outline: none;
            border-color: var(--kcd-accent);
            box-shadow: 0 0 0 4px rgba(105, 108, 255, 0.1);
            background-color: #fff;
        }

        .btn-official {
            background-color: var(--kcd-navy);
            color: #fff;
            width: 100%;
            padding: 1.1rem;
            border-radius: 0.6rem;
            border: none;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 1rem;
            letter-spacing: 0.5px;
        }

        .btn-official:hover {
            background-color: var(--kcd-blue);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(13, 33, 55, 0.2);
        }

        .btn-official:active {
            transform: translateY(0);
        }

        /* TICKET STYLE */
        .ticket-result {
            padding: 2.5rem 2rem;
            background: #fff;
        }

        .ticket-strip {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 1.2rem;
            padding: 2.5rem;
            text-align: center;
        }

        .ticket-id-number {
            font-size: 4.5rem;
            font-weight: 900;
            color: var(--kcd-navy);
            margin: 0.5rem 0;
            line-height: 1;
        }

        .footer-logo-row {
            margin-top: 2.5rem;
            text-align: center;
            opacity: 0.6;
            border-top: 1px solid #eee;
            padding-top: 1.5rem;
        }
    </style>
</head>

<body>

    <div class="main-wrapper">

        @if (session('tiket_success'))
            <div class="official-card animate__animated animate__fadeIn">
                <div class="official-header" style="background-color: #10b981;">
                    <i class='bx bx-check-circle' style="font-size: 3.5rem; margin-bottom: 1rem;"></i>
                    <h2>Pendaftaran Berhasil</h2>
                    <p>Silakan Tunggu Antrian Anda</p>
                </div>

                <div class="ticket-result">
                    <div class="ticket-strip">
                        <div style="font-size: 0.85rem; font-weight: 700; color: #64748b; letter-spacing: 2px;">NOMOR ANTRIAN</div>
                        <div class="ticket-id-number">{{ session('tiket_nomor') }}</div>

                        <div style="margin-top: 2rem; border-top: 2px dashed #cbd5e1; padding-top: 1.5rem; text-align: left;">
                            <div class="mb-3">
                                <small style="color: #64748b; text-transform: uppercase; font-size: 0.7rem; font-weight: 700;">Nama Lengkap</small>
                                <div style="font-weight: 700; color: #1e293b; font-size: 1.1rem;">{{ session('tiket_nama') }}</div>
                            </div>
                            <div>
                                <small style="color: #64748b; text-transform: uppercase; font-size: 0.7rem; font-weight: 700;">Tujuan Layanan</small>
                                <div style="font-weight: 700; color: var(--kcd-accent); font-size: 1.1rem;">{{ session('tiket_tujuan') ?? 'Umum / Resepsionis' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('guest.buku-tamu') }}" class="btn btn-label-secondary w-100 btn-xl rounded-pill">
                            <i class='bx bx-home-alt me-2'></i> Selesai & Kembali
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="official-card">
                <div class="official-header">
                    @if ($instansi && $instansi->logo)
                        <img src="{{ Storage::url($instansi->logo) }}" class="logo-box" alt="Logo Instansi">
                    @else
                        <img src="{{ asset('logo.png') }}" class="logo-box" alt="Logo Jawa Barat">
                    @endif
                    <h2>BUKU TAMU DIGITAL</h2>
                    <p>{{ $instansi->nama ?? 'Kantor Cabang Dinas Pendidikan Wilayah' }}</p>
                </div>

                <div class="form-body">
                    @if ($errors->any())
                        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                            <i class='bx bx-error-circle me-2'></i>
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('guest.buku-tamu.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <label class="section-label">Nama Lengkap *</label>
                                <input type="text" name="nama" class="official-input" placeholder="Sesuai KTP" required value="{{ old('nama') }}" autofocus>
                            </div>
                            <div class="col-md-6">
                                <label class="section-label">Jabatan *</label>
                                <input type="text" name="jabatan_pengunjung" class="official-input" placeholder="Guru, Orang Tua, dll" required value="{{ old('jabatan_pengunjung') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="section-label">Instansi/Sekolah *</label>
                                <input type="text" name="asal_instansi" class="official-input" placeholder="Nama Sekolah/Instansi" required value="{{ old('asal_instansi') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="section-label">NPSN (Opsional)</label>
                                <input type="text" name="npsn" class="official-input" placeholder="Kode NPSN" value="{{ old('npsn') }}">
                            </div>
                        </div>

                        <label class="section-label">Nomor WhatsApp *</label>
                        <input type="text" name="nomor_hp" class="official-input" placeholder="08xxxxxxxxxx" required value="{{ old('nomor_hp') }}">

                        <label class="section-label">Keperluan Kedatangan *</label>
                        <textarea name="keperluan" rows="3" class="official-input" style="resize: none;" placeholder="Jelaskan secara singkat maksud kedatangan Anda..." required>{{ old('keperluan') }}</textarea>

                        <button type="submit" class="btn-official">
                            SUBMIT & AMBIL ANTRIAN <i class='bx bx-right-arrow-alt'></i>
                        </button>
                    </form>

                    <div class="footer-logo-row">
                        <small class="text-muted">Sistem Pelayanan Terpadu &copy; {{ date('Y') }}</small>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
</body>
</html>