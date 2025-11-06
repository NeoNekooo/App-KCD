<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('sneat/assets/') }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Login - Sistem Informasi Akademik</title>
    
    @vite([
        'resources/sneat/assets/vendor/fonts/boxicons.css',
        'resources/sneat/assets/vendor/css/core.css',
        'resources/sneat/assets/vendor/css/theme-default.css',
        'resources/sneat/assets/css/demo.css',
        'resources/sneat/assets/vendor/css/pages/page-auth.css',
    ])

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
            justify-content: space-between; /* Membuat jarak maksimal antar elemen */
            align-items: center;
        }

        .login-header .header-icon i {
            font-size: 2.5rem; /* 40px */
            color: #566a7f;
        }
        
        .login-header .app-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #566a7f;
            text-align: center;
        }
        
        .btn-masuk {
            background-color: #008493 !important;
            border-color: #008493 !important;
        }
        .btn-registrasi {
            background-color: #00a9be !important;
            border-color: #00a9be !important;
        }
    </style>
</head>

<body>
    <header class="login-header">
        <div class="header-icon">
            {{-- Ikon Kiri (Sekolah) --}}
            <i class='bx bxs-school'></i>
        </div>
        <div class="app-name">
            {{-- Teks di Tengah --}}
            {{-- Sistem Informasi Akademik --}}
        </div>
        <div class="header-icon">
            {{-- Ikon Kanan (Aplikasi/Widget) --}}
            <i class='bx bxs-widget'></i>
        </div>
    </header>

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">

                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-2 text-center">Masuk</h4>
                        <p class="mb-4 text-center">Selamat datang di SI-Akademik</p>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger py-2">
                                <ul class="mb-0">@foreach ($errors->all() as $error)<li class="small">{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif

                        <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="Masukkan email" value="{{ old('email') }}" required autofocus />
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required />
                                    <span id="togglePassword" class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="tahun_pelajaran" class="form-label">Tahun Pelajaran</label>
                                <select id="tahun_pelajaran" name="tahun_pelajaran" class="form-select" required>
                                    <option value="">Pilih Tahun Pelajaran...</option>
                                    <option value="2025/2026 Ganjil" selected>2025/2026 Ganjil</option>
                                    <option value="2024/2025 Genap">2024/2025 Genap</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
                                    <label class="form-check-label" for="remember-me"> Ingatkan saya di peramban ini </label>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary d-grid w-100 btn-masuk" type="submit">Masuk</button>
                                <a href="{{ route('register') }}" class="btn btn-primary d-grid w-100 btn-registrasi">Registrasi</a>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const icon = togglePassword.querySelector('i');
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle ikon antara hide dan show
                if (type === 'text') {
                    icon.classList.remove('bx-hide');
                    icon.classList.add('bx-show');
                } else {
                    icon.classList.remove('bx-show');
                    icon.classList.add('bx-hide');
                }
            });
        });
    </script>
</body>
</html>