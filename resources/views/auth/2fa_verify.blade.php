<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('sneat/assets/') }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Verifikasi 2FA - Kantor Cabang Dinas</title>

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
        .btn-verifikasi {
            background-color: #008493 !important;
            border-color: #008493 !important;
            color: #fff;
            transition: all 0.2s;
        }
        .btn-verifikasi:hover {
            background-color: #006f7b !important;
            border-color: #006f7b !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 132, 147, 0.4);
        }
        .digit-group input {
            width: 45px;
            height: 55px;
            background-color: #f5f5f9;
            border: 2px solid #d9dee3;
            line-height: 50px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #566a7f;
            margin: 0 4px;
            border-radius: 8px;
        }
        .digit-group input:focus {
            border-color: #008493;
            box-shadow: 0 0 0 0.25rem rgba(0, 132, 147, 0.25);
            outline: none;
        }
    </style>
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <div class="card">
                    <div class="card-body">
                        <div class="app-brand justify-content-center mb-2">
                            <span class="app-brand-text demo text-body fw-bolder" style="text-transform: uppercase;">Keamanan Ganda</span>
                        </div>
                        
                        <h4 class="mb-2 text-center text-primary">Otentikasi 2FA <i class='bx bxs-shield-alt-2'></i></h4>
                        <p class="text-center mb-4">Buka aplikasi <b>Google Authenticator</b> di HP Anda dan masukkan kode 6-digit yang muncul.</p>

                        @if ($errors->any())
                            <div class="alert alert-danger py-2 mb-3" role="alert">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li class="small">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="otpForm" action="{{ route('2fa.verify') }}" method="POST">
                            @csrf
                            <input type="hidden" name="one_time_password" id="fullOtp">
                            
                            <div class="digit-group d-flex justify-content-center mb-4" data-group-name="digits" data-autosubmit="false" autocomplete="off">
                                <input type="text" id="digit-1" name="digit-1" data-next="digit-2" maxlength="1" />
                                <input type="text" id="digit-2" name="digit-2" data-next="digit-3" data-previous="digit-1" maxlength="1" />
                                <input type="text" id="digit-3" name="digit-3" data-next="digit-4" data-previous="digit-2" maxlength="1" />
                                <input type="text" id="digit-4" name="digit-4" data-next="digit-5" data-previous="digit-3" maxlength="1" />
                                <input type="text" id="digit-5" name="digit-5" data-next="digit-6" data-previous="digit-4" maxlength="1" />
                                <input type="text" id="digit-6" name="digit-6" data-previous="digit-5" maxlength="1" />
                            </div>

                            <button type="submit" class="btn btn-primary d-grid w-100 btn-verifikasi">Verifikasi Sekarang</button>
                            
                            <p class="text-center mt-3">
                                <a href="{{ route('login') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="small">
                                    <i class='bx bx-chevron-left'></i> Kembali ke Login
                                </a>
                            </p>
                        </form>
                        
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.digit-group input');
            const form = document.getElementById('otpForm');
            const fullOtpHidden = document.getElementById('fullOtp');

            inputs.forEach((input, index) => {
                input.addEventListener('keyup', function(e) {
                    if (e.keyCode === 8 || e.keyCode === 37) { // Backspace or Left Arrow
                        const prev = document.getElementById(this.dataset.previous);
                        if (prev) prev.focus();
                    } else if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105)) { // Numbers
                        const next = document.getElementById(this.dataset.next);
                        if (next) next.focus();
                        
                        // Check if all filled
                        checkAllFilled();
                    }
                });

                // Paste support
                input.addEventListener('paste', function(e) {
                    const data = e.clipboardData.getData('text').split('');
                    if (data.length === 6) {
                        inputs.forEach((inp, i) => inp.value = data[i]);
                        checkAllFilled();
                        inputs[5].focus();
                    }
                });
            });

            function checkAllFilled() {
                let otp = '';
                inputs.forEach(input => otp += input.value);
                fullOtpHidden.value = otp;
                if (otp.length === 6) {
                    // Option: auto submit here if you want
                }
            }

            form.addEventListener('submit', function(e) {
                let otp = '';
                inputs.forEach(input => otp += input.value);
                fullOtpHidden.value = otp;
                if (otp.length < 6) {
                    e.preventDefault();
                    alert('Mohon masukkan 6 digit kode lengkap.');
                }
            });
        });
    </script>
</body>
</html>
