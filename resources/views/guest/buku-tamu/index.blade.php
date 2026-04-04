<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu KCD - Pelayanan Publik</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

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
            width: 100%;
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
            padding: 2.5rem 2.2rem;
        }

        /* FIX SIMETRIS: Hapus margin 0 agar gutter Bootstrap jalan normal */
        .form-body .row {
            /* margin-left: 0; */
            /* margin-right: 0; */
        }

        .form-body .row>[class*="col-"] {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .section-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--kcd-navy);
            margin-bottom: 0.6rem;
            margin-top: 1.2rem;
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
            margin-bottom: 1rem;
            transition: border-color 0.2s;
            box-sizing: border-box; /* Pastikan padding tidak nambah lebar */
        }

        .official-input:focus {
            outline: none;
            border-color: var(--kcd-accent);
            box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.1);
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

        @if (session('tiket_success'))
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
                                <span
                                    class="fw-bold text-primary">{{ session('tiket_tujuan') ?? 'Umum/Resepsionis' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <button type="button" id="btnPrintTicket" 
                            class="btn btn-success w-100 rounded-pill mb-2 py-2 fw-bold shadow-sm"
                            onclick="requestPrint({{ session('tiket_id') }})">
                            <i class='bx bx-printer me-2'></i>CETAK TIKET FISIK
                        </button>
                        <a href="{{ route('guest.buku-tamu') }}"
                            class="btn btn-outline-secondary w-100 rounded-pill">Kembali ke Beranda</a>
                    </div>
                </div>
            </div>
            
            <script>
                function requestPrint(id) {
                    const btn = document.getElementById('btnPrintTicket');
                    const originalHtml = btn.innerHTML;
                    
                    // Ganti status tombol
                    btn.disabled = true;
                    btn.innerHTML = "<i class='bx bx-loader-alt bx-spin me-2'></i>Mencetak...";

                    fetch(`/buku-tamu/${id}/print`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.status === 'success') {
                            alert('Tiket sedang dicetak di mesin printer depan. Silakan ambil tiket fisik Anda.');
                            btn.innerHTML = "<i class='bx bx-check me-2'></i>Sudah Dicetak";
                            btn.classList.replace('btn-success', 'btn-light');
                        } else {
                            alert('Gagal mengirim perintah cetak. Silakan coba lagi.');
                            btn.disabled = false;
                            btn.innerHTML = originalHtml;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan koneksi.');
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    });
                }
            </script>
        @else
            <div class="official-card">
                <div class="official-header">
                    @if ($instansi && $instansi->logo)
                        <img src="{{ Storage::url($instansi->logo) }}" class="logo-box" height="60"
                            alt="Logo Instansi">
                    @else
                        <img src="{{ asset('logo.png') }}" class="logo-box" height="60" alt="Logo Jawa Barat">
                    @endif
                    <h2>BUKU TAMU DIGITAL</h2>
                    <p>{{ $instansi->nama_instansi ?? 'Kantor Cabang Dinas Pendidikan Wilayah' }}</p>
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

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="section-label">Nama Lengkap *</label>
                                <input type="text" name="nama" class="official-input" placeholder="Sesuai KTP"
                                    required value="{{ old('nama') }}" autofocus>
                            </div>
                            <div class="col-md-6">
                                <label class="section-label">Jabatan *</label>
                                <input type="text" name="jabatan_pengunjung" class="official-input"
                                    placeholder="Contoh: Siswa, Guru, Orang Tua" required
                                    value="{{ old('jabatan_pengunjung') }}">
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="section-label">Unit/Instansi/Sekolah *</label>
                                <input type="text" name="asal_instansi" class="official-input"
                                    placeholder="Nama Sekolah/Lembaga" required value="{{ old('asal_instansi') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="section-label">NPSN (Opsional)</label>
                                <input type="text" name="npsn" class="official-input"
                                    placeholder="Kode NPSN Sekolah" value="{{ old('npsn') }}">
                            </div>
                        </div>

                        <label class="section-label">Nomor HP *</label>
                        <input type="text" name="nomor_hp" class="official-input" placeholder="08xxxxxxxxxx" required
                            value="{{ old('nomor_hp') }}">

                        <label class="section-label">Keperluan *</label>
                        <select name="keperluan" class="official-input" required>
                            <option value="" disabled selected>Pilih Keperluan Anda...</option>
                            @forelse($categories as $cat)
                                <option value="{{ $cat->name }}" {{ old('keperluan') == $cat->name ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @empty
                                <option value="Umum">Umum / Resepsionis</option>
                            @endforelse
                        </select>

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