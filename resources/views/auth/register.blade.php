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
    </style>
</head>

<body>

    <!-- 🔥 HEADER – SAMA PERSIS LOGIN -->
    <header class="login-header">
        <div class="header-icon"><i class='bx bxs-school'></i></div>
        <div class="app-name"></div>
        <div class="header-icon"><i class='bx bxs-widget'></i></div>
    </header>

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">

                <!-- 🔥 CARD – SAMA STRUKTURNYA DENGAN LOGIN -->
                <div class="card">
                    <div class="card-body">

                        <h4 class="mb-2 text-center">Registrasi</h4>
                        <p class="mb-4 text-center">Buat akun wali murid untuk akses SI-Akademik</p>

                        @if ($errors->any())
                            <div class="alert alert-danger py-2">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li class="small">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('register') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label" for="nik">NIK</label>
                                <input type="text" class="form-control" id="nik" name="nik"
                                    value="{{ old('nik') }}" placeholder="Masukkan nik Siswa" required autofocus>
                            </div>

                            <div id="info-siswa" class="alert d-none mt-3"></div>

                            <div id="loading-nik" class="text-center d-none mt-2">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <span class="ms-2">Mencari data siswa...</span>
                            </div>

                            <div id="form-wali" class="d-none">
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email') }}" placeholder="Masukkan email Anda">
                                </div>

                                <div class="mb-3 form-password-toggle">
                                    <label class="form-label" for="password">Password</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" class="form-control" name="password"
                                            placeholder="••••••">
                                        <span class="input-group-text cursor-pointer" id="togglePassword">
                                            <i class="bx bx-hide"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-3 form-password-toggle">
                                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password_confirmation" class="form-control"
                                            name="password_confirmation" placeholder="••••••">
                                        <span class="input-group-text cursor-pointer" id="togglePassword2">
                                            <i class="bx bx-hide"></i>
                                        </span>
                                    </div>
                                </div>

                                <button class="btn btn-primary d-grid w-100 btn-registrasi" type="submit">
                                    Daftar
                                </button>
                            </div>

                            <p class="text-center mt-3">
                                Sudah punya akun?
                                <a href="{{ route('login') }}">Masuk</a>
                            </p>
                        </form>

                    </div>
                </div>
                <!-- END CARD -->

            </div>
        </div>
    </div>

    <script>
        const nikInput = document.getElementById('nik');
        const infoSiswa = document.getElementById('info-siswa');
        const formWali = document.getElementById('form-wali');
        const loadingNik = document.getElementById('loading-nik');

        let debounceTimer = null;

        nikInput.addEventListener('input', () => {
            const nik = nikInput.value.replace(/\D/g, '');

            nikInput.value = nik;

            infoSiswa.className = 'alert d-none';
            formWali.classList.add('d-none');

            if (nik.length !== 16) {
                loadingNik.classList.add('d-none');
                return;
            }

            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(() => {
                cekNik(nik);
            }, 400);
        });

        async function cekNik(nik) {
            loadingNik.classList.remove('d-none');

            try {
                const res = await fetch("{{ route('cek.nik') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        nik
                    })
                });

                const data = await res.json();

                loadingNik.classList.add('d-none');

                if (!data.status) {
                    infoSiswa.textContent = data.message;
                    infoSiswa.classList.remove('d-none');
                    infoSiswa.classList.add('alert-danger');
                    return;
                }

                infoSiswa.innerHTML = `
            <strong>Nama :</strong> ${data.data.nama}<br>
            <strong>Kelas :</strong> ${data.data.kelas}<br>
            <strong>Tanggal Lahir :</strong> ${data.data.tanggal_lahir}
        `;
                infoSiswa.classList.remove('d-none');
                infoSiswa.classList.add('alert-info');

                formWali.classList.remove('d-none');

            } catch (e) {
                loadingNik.classList.add('d-none');
                infoSiswa.textContent = 'Terjadi kesalahan sistem';
                infoSiswa.classList.remove('d-none');
                infoSiswa.classList.add('alert-danger');
            }
        }

        document.getElementById('togglePassword').onclick = function() {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('bx-hide');
            icon.classList.toggle('bx-show');
        };
        document.getElementById('togglePassword2').onclick = function() {
            const input = document.getElementById('password_confirmation');
            const icon = this.querySelector('i');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('bx-hide');
            icon.classList.toggle('bx-show');
        };
    </script>

</body>

</html>
