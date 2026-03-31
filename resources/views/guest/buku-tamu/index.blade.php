<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu KCD - Pelayanan Publik</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
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
            padding: 1rem;
        }

        .main-wrapper {
            width: 100%;
            max-width: 520px;
        }

        .official-card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e1e8ed;
            overflow: hidden;
        }

        .official-header {
            background-color: var(--kcd-navy);
            padding: 2rem;
            text-align: center;
            color: #ffffff;
        }

        .logo-box {
            height: 60px;
            margin-bottom: 1rem;
        }

        .official-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .official-header p {
            font-size: 0.85rem;
            opacity: 0.8;
            margin-top: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-body {
            padding: 2.5rem 2rem;
        }

        .section-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--kcd-navy);
            margin-bottom: 0.5rem;
            display: block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .official-input {
            width: 100%;
            padding: 0.8rem 1rem;
            border-radius: 0.5rem;
            border: 1.5px solid #d1d9e0;
            background: #ffffff;
            font-size: 1rem;
            color: #333;
            margin-bottom: 1.5rem;
            transition: border-color 0.2s;
        }

        .official-input:focus {
            outline: none;
            border-color: var(--kcd-accent);
            box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.1);
        }

        .official-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23444' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }

        .btn-official {
            background-color: var(--kcd-navy);
            color: #fff;
            width: 100%;
            padding: 1rem;
            border-radius: 0.5rem;
            border: none;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-official:hover {
            background-color: var(--kcd-blue);
        }

        /* TICKET STYLE */
        .ticket-result {
            padding: 2rem;
            background: #fff;
        }

        .ticket-strip {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .ticket-id-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .ticket-id-number {
            font-size: 4rem;
            font-weight: 800;
            color: var(--kcd-navy);
            margin: 0;
            line-height: 1;
        }

        .ticket-footer {
            margin-top: 1.5rem;
            border-top: 1px dashed #cbd5e1;
            padding-top: 1.5rem;
            text-align: left;
        }

        .footer-logo-row {
            margin-top: 2rem;
            text-align: center;
            opacity: 0.7;
        }
    </style>
</head>
<body>

<div class="main-wrapper">

    @if(session('tiket_success'))
        <div class="official-card">
            <div class="official-header" style="background-color: #10b981;">
                <i class='bx bx-check-circle fs-1'></i>
                <h2>Pendaftaran Berhasil</h2>
                <p>Silakan Tunggu Antrian Anda</p>
            </div>
            
            <div class="ticket-result">
                <div class="ticket-strip">
                    <div class="ticket-id-label">ANTRIAN ANDA</div>
                    <div class="ticket-id-number">{{ session('tiket_nomor') }}</div>
                    
                    <div class="ticket-footer">
                        <div class="mb-2">
                            <small class="text-muted d-block">Nama Lengkap</small>
                            <span class="fw-bold text-dark">{{ session('tiket_nama') }}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Tujuan Pejabat/Layanan</small>
                            <span class="fw-bold text-primary">{{ session('tiket_tujuan') ?? 'Umum/Resepsionis' }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('guest.buku-tamu') }}" class="btn btn-outline-secondary w-100 rounded-pill">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    @else
        <div class="official-card">
            <div class="official-header">
                <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Logo_Provinsi_Jawa_Barat.png" class="logo-box" alt="Logo Jawa Barat">
                <h2>BUKU TAMU DIGITAL</h2>
                <p>Kantor Cabang Dinas Pendidikan Wilayah</p>
            </div>

            <div class="form-body">
                @if ($errors->any())
                    <div class="alert alert-danger p-2 mb-4 small rounded-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('guest.buku-tamu.store') }}" method="POST">
                    @csrf
                    
                    <label class="section-label">Nama Lengkap Tamu *</label>
                    <input type="text" name="nama" class="official-input" placeholder="Sesuai kartu identitas" required value="{{ old('nama') }}" autofocus>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="section-label">NIK (Opsional)</label>
                            <input type="text" name="nik" class="official-input" placeholder="16 Digit NIK" value="{{ old('nik') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="section-label">Unit/Instansi/Sekolah *</label>
                            <input type="text" name="asal_instansi" class="official-input" placeholder="Nama Sekolah/Lembaga" required value="{{ old('asal_instansi') }}">
                        </div>
                    </div>

                    <label class="section-label">Tujuan Bidang / Pejabat *</label>
                    <select name="tujuan_pegawai_id" class="official-input official-select" required>
                        <option value="" selected disabled>-- Pilih Tujuan --</option>
                        @foreach($pegawais as $pegawai)
                            <option value="{{ $pegawai->id }}" {{ old('tujuan_pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                                {{ $pegawai->nama }} ({{ $pegawai->jabatanKcd->nama ?? $pegawai->jabatan }})
                            </option>
                        @endforeach
                    </select>

                    <label class="section-label">Keperluan / Maksud Kunjungan *</label>
                    <textarea name="keperluan" rows="3" class="official-input" style="resize: none;" placeholder="Contoh: Konsultasi masalah dapodik sekolah" required>{{ old('keperluan') }}</textarea>

                    <button type="submit" class="btn-official shadow-sm mt-3">
                        SUBMIT & AMBIL ANTRIAN <i class='bx bx-paper-plane'></i>
                    </button>
                </form>

                <div class="footer-logo-row">
                    <small class="text-muted">Layanan Terpadu Satu Pintu &copy; {{ date('Y') }}</small>
                </div>
            </div>
        </div>
    @endif

</div>

<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
</body>
</html>
