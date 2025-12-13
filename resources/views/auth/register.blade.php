<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr"
    data-theme="theme-default"
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
                        <p class="mb-4 text-center">Buat akun untuk akses SI-Akademik</p>

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
                                <label class="form-label" for="name">Nama</label>
                                <input type="text" class="form-control" id="name"
                                    name="name" value="{{ old('name') }}"
                                    placeholder="Masukkan nama Anda" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email"
                                    name="email" value="{{ old('email') }}"
                                    placeholder="Masukkan email Anda" required>
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control"
                                        name="password" placeholder="••••••" required >
                                    <span class="input-group-text cursor-pointer" id="togglePassword">
                                        <i class="bx bx-hide"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password_confirmation" class="form-control"
                                        name="password_confirmation" placeholder="••••••" required >
                                    <span class="input-group-text cursor-pointer" id="togglePassword2">
                                        <i class="bx bx-hide"></i>
                                    </span>
                                </div>
                            </div>

                            <button class="btn btn-primary d-grid w-100 btn-registrasi" type="submit">
                                Daftar
                            </button>

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
        document.getElementById('togglePassword').onclick = function () {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('bx-hide');
            icon.classList.toggle('bx-show');
        };
        document.getElementById('togglePassword2').onclick = function () {
            const input = document.getElementById('password_confirmation');
            const icon = this.querySelector('i');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('bx-hide');
            icon.classList.toggle('bx-show');
        };
    </script>

</body>
</html>
