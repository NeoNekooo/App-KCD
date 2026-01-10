<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('sneat/assets/') }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Login - Kantor Cabang Dinas</title>

    <link rel="stylesheet" href="{{ asset('vendor/fonts/boxicons.css') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-image: url("{{ asset('sneat/assets/img/backgrounds/dapodik-bg-pattern.svg') }}");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-color: #f5f5f9;
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
        .btn-masuk {
            background-color: #008493 !important;
            border-color: #008493 !important;
            color: #fff;
        }
        .btn-masuk:hover {
            background-color: #006f7b !important;
            border-color: #006f7b !important;
        }
    </style>
</head>

<body>
    {{-- Header Ikon --}}
    <header class="login-header">
        <div class="header-icon"><i class='bx bxs-school'></i></div>
        <div class="header-icon"><i class='bx bxs-widget'></i></div>
    </header>

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <div class="card">
                    <div class="card-body">
                        {{-- Logo / Judul --}}
                        <div class="app-brand justify-content-center mb-2">
                            <span class="app-brand-text demo text-body fw-bolder">Sistem KCD</span>
                        </div>
                        <h4 class="mb-2 text-center">Selamat Datang! 👋</h4>
                        <p class="mb-4 text-center text-muted">Masuk menggunakan NIP atau Username.</p>

                        {{-- Alert Error (Sinkron dengan ValidationException di Controller) --}}
                        @if ($errors->any())
                            <div class="alert alert-danger py-2 mb-3" role="alert">
                                <div class="d-flex align-items-center mb-1">
                                    <i class='bx bx-error-circle me-2'></i>
                                    <span class="fw-bold">Gagal Masuk:</span>
                                </div>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li class="small">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- FORM LOGIN --}}
                        <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                            @csrf

                            {{-- 1. USERNAME / NIP --}}
                            <div class="mb-3">
                                <label for="username" class="form-label">Username / NIP</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       placeholder="Masukkan NIP" 
                                       value="{{ old('username') }}" 
                                       required 
                                       autofocus />
                            </div>

                            {{-- 2. PASSWORD --}}
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" 
                                           id="password" 
                                           class="form-control" 
                                           name="password" 
                                           placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                                           required />
                                    <span class="input-group-text cursor-pointer" id="togglePassword"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            {{-- 3. REMEMBER ME --}}
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
                                    <label class="form-check-label" for="remember-me"> Ingat Saya </label>
                                </div>
                            </div>

                            {{-- 4. TOMBOL MASUK --}}
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100 btn-masuk" type="submit">Masuk Sistem</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Toggle Password --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const icon = togglePassword.querySelector('i');

            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

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