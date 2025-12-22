<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('sneat/assets/') }}">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Registrasi - Sistem Informasi Akademik</title>

    <link rel="stylesheet" href="{{ asset('vendor/fonts/boxicons.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-image: url("{{ asset('sneat/assets/img/backgrounds/dapodik-bg-pattern.svg') }}");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        .login-header {
            position: absolute;
            top: 2rem;
            left: 2rem;
            right: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .login-header .header-icon i {
            font-size: 2.5rem;
            color: #566a7f;
        }

        .btn-registrasi {
            background-color: #00a9be !important;
            border-color: #00a9be !important;
        }

        /* ANIMASI STEP */
        .step-animate {
            overflow: hidden;
            opacity: 0;
            transform: translateY(-10px);
            max-height: 0;
            transition: all 0.4s ease;
        }

        .step-animate.show {
            opacity: 1;
            transform: translateY(0);
            max-height: 500px;
        }
    </style>
</head>

<body>

<header class="login-header">
    <div class="header-icon"><i class='bx bxs-school'></i></div>
    <div class="app-name"></div>
    <div class="header-icon"><i class='bx bxs-widget'></i></div>
</header>

<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <div class="card">
                <div class="card-body">

                    <h4 class="mb-2 text-center">Registrasi</h4>
                    <p class="mb-4 text-center">Buat akun wali murid untuk akses SI-Akademik</p>

                    <form action="{{ route('register') }}" method="POST" id="form-register">
                        @csrf

                        <!-- STEP 1 -->
                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" class="form-control" id="nik" name="nik"
                                   placeholder="Masukkan NIK Siswa" required>
                        </div>

                        <div id="loading-nik" class="text-center d-none mt-2">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            <span class="ms-2">Mencari data siswa...</span>
                        </div>

                        <div id="info-siswa" class="alert d-none mt-3"></div>

                        <!-- STEP 2 -->
                        <div id="step-akun" class="step-animate d-none">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="password" name="password" readonly>
                                    <button type="button" class="btn btn-outline-secondary" id="copyPassword">
                                        Salin
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- BUTTON DINAMIS -->
                        <button type="button" id="actionButton"
                                class="btn btn-primary w-100 btn-registrasi mt-2">
                            Konfirmasi
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="toastCopy" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                Password berhasil disalin 📋
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
    const nikInput = document.getElementById('nik');
    const infoSiswa = document.getElementById('info-siswa');
    const loadingNik = document.getElementById('loading-nik');
    const stepAkun = document.getElementById('step-akun');
    const actionButton = document.getElementById('actionButton');
    const form = document.getElementById('form-register');

    let siswaData = null;
    let step = 1;

    function showStepAkun() {
        stepAkun.classList.remove('d-none');
        requestAnimationFrame(() => stepAkun.classList.add('show'));
    }

    function hideStepAkun() {
        stepAkun.classList.remove('show');
        setTimeout(() => stepAkun.classList.add('d-none'), 400);
    }

    nikInput.addEventListener('input', () => {
        nikInput.value = nikInput.value.replace(/\D/g, '');
        infoSiswa.className = 'alert d-none';
        hideStepAkun();
        step = 1;
        actionButton.textContent = 'Konfirmasi';

        if (nikInput.value.length === 16) cekNik(nikInput.value);
    });

    async function cekNik(nik) {
        loadingNik.classList.remove('d-none');

        const res = await fetch("{{ route('cek.nik') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ nik })
        });

        const data = await res.json();
        loadingNik.classList.add('d-none');

        if (!data.status) {
            infoSiswa.textContent = data.message;
            infoSiswa.classList.remove('d-none');
            infoSiswa.classList.add('alert-danger');
            return;
        }

        siswaData = data.data;

        infoSiswa.innerHTML = `
            <strong>Nama :</strong> ${data.data.nama}<br>
            <strong>Kelas :</strong> ${data.data.kelas}<br>
            <strong>Tanggal Lahir :</strong> ${data.data.tanggal_lahir}<br>
            <strong>Nama Ayah :</strong> ${data.data.ayah}<br>
            <strong>Nama Ibu :</strong> ${data.data.ibu}
        `;
        infoSiswa.classList.remove('d-none');
        infoSiswa.classList.add('alert-info');
    }

    actionButton.addEventListener('click', () => {
        if (step === 1) {
            document.getElementById('username').value = siswaData.ibu ?? siswaData.nama;
            showStepAkun();
            actionButton.textContent = 'Generate Password';
            step = 2;

        } else if (step === 2) {
            document.getElementById('password').value =
                Math.random().toString(36).slice(-8);
            actionButton.textContent = 'Registrasi';
            step = 3;

        } else {
            form.submit();
        }
    });

    document.getElementById('copyPassword').onclick = async () => {
        const pwd = document.getElementById('password').value;
        if (!pwd) return;

        await navigator.clipboard.writeText(pwd);
        new bootstrap.Toast(document.getElementById('toastCopy'), { delay: 2000 }).show();
    };
</script>

</body>
</html>
