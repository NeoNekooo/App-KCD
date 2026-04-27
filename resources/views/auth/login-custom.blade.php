@php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
@endphp
<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('sneat/assets/') }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Login - Kantor Cabang Dinas</title>
    @php
        // Obfuscator Aset Ala Facebook (Data URIs / Inline)
        function injectViteAsset($resourcePath) {
            $manifestPath = public_path('build/manifest.json');
            if (!file_exists($manifestPath)) {
                // Return default vite for dev mode
                return app(\Illuminate\Foundation\Vite::class)([$resourcePath])->toHtml();
            }

            $manifest = json_decode(file_get_contents($manifestPath), true);
            if (!isset($manifest[$resourcePath])) return '';

            $entry = $manifest[$resourcePath];
            $outputHtml = '';

            // Fungsi Peluluh SourceMap agar DevTools tidak membangun ulang visual folder palsu
            $stripSourceMaps = function($text) {
                return preg_replace('/(\/\/[#@]\s*sourceMappingURL=.*|\/\*[\s\S]*?sourceMappingURL=[\s\S]*?\*\/)/i', '', $text);
            };

            // 1. Tangani CSS (Base64 Data URI agar tampil persis seperti Facebook)
            if (isset($entry['css'])) {
                foreach ($entry['css'] as $cssFile) {
                    $cssPath = public_path('build/' . $cssFile);
                    if (file_exists($cssPath)) {
                        $content = $stripSourceMaps(file_get_contents($cssPath));
                        $b64 = base64_encode($content);
                        $outputHtml .= '<link rel="stylesheet" href="data:text/css;base64,' . $b64 . '">';
                    }
                }
            }
            
            // Atau jika entry itu sendiri CSS
            if (str_ends_with($resourcePath, '.css')) {
                 $cssPath = public_path('build/' . $entry['file']);
                 if (file_exists($cssPath)) {
                        $content = $stripSourceMaps(file_get_contents($cssPath));
                        $b64 = base64_encode($content);
                        $outputHtml .= '<link rel="stylesheet" href="data:text/css;base64,' . $b64 . '">';
                 }
            }

            // 2. Tangani JS (Inline script agar ES6 imports tidak rusak)
            if (str_ends_with($resourcePath, '.js')) {
                $jsPath = public_path('build/' . $entry['file']);
                if (file_exists($jsPath)) {
                    $jsContent = file_get_contents($jsPath);
                    $jsContent = $stripSourceMaps($jsContent);
                    
                    // Kalau ada imports, kita REPLACE dependencies-nya dengan Base64!
                    if (isset($entry['imports'])) {
                        foreach ($entry['imports'] as $imp) {
                            $impEntry = $manifest[$imp];
                            $impPathReal = public_path('build/' . $impEntry['file']);
                            if (file_exists($impPathReal)) {
                                $impContent = file_get_contents($impPathReal);
                                $impContent = $stripSourceMaps($impContent);
                                $impDataUri = "data:application/javascript;charset=utf-8;base64," . base64_encode($impContent);
                                
                                // Ganti "./module.esm-XXX.js" dengan Data URI
                                $basename = basename($impEntry['file']); 
                                $jsContent = str_replace('"./' . $basename . '"', '"' . $impDataUri . '"', $jsContent);
                                $jsContent = str_replace("'" . './' . $basename . "'", "'" . $impDataUri . "'", $jsContent);
                            }
                        }
                    }
                    
                    // Jadikan script utama (beserta import base64-nya) menjadi base64 juga
                    $jsB64 = base64_encode($jsContent);
                    $outputHtml .= '<script type="module" src="data:application/javascript;charset=utf-8;base64,' . $jsB64 . '"></script>';
                }
            }

            return $outputHtml;
        }

        // Boxicons ke Base64 (Full Embed Font WOFF2 to solve CORS/Hash issues)
        $boxiconsB64 = '';
        $boxiconsPath = public_path('vendor/fonts/boxicons.css');
        if (file_exists($boxiconsPath)) {
            $content = file_get_contents($boxiconsPath);
            
            $woff2Path = public_path('vendor/fonts/boxicons/boxicons.woff2');
            if (file_exists($woff2Path)) {
                $woff2B64 = base64_encode(file_get_contents($woff2Path));
                $woff2DataUri = "data:font/woff2;charset=utf-8;base64," . $woff2B64;
                
                $customFontFace = "@font-face { font-family: 'boxicons'; font-weight: normal; font-style: normal; src: url('$woff2DataUri') format('woff2'); }";
                
                // Timpa aturan font-face lama dengan yang full base64
                $content = preg_replace('/@font-face\s*\{[^}]+\}/i', $customFontFace, $content);
            } else {
                // Fallback jika tidak ada woff2
                $content = str_replace('../fonts/boxicons', asset('vendor/fonts/boxicons'), $content);
            }
            
            $boxiconsB64 = 'data:text/css;base64,' . base64_encode($content);
        }
    @endphp

    @if($boxiconsB64)
        <link rel="stylesheet" href="{!! $boxiconsB64 !!}">
    @else
        <link rel="stylesheet" href="{{ asset('vendor/fonts/boxicons.css') }}">
    @endif

    {!! injectViteAsset('resources/css/app.css') !!}
    {!! injectViteAsset('resources/js/app.js') !!}

    <style>
        body {
            /* Background Pattern Bawaan Sneat */
            background-image: url("{{ asset('sneat/assets/img/backgrounds/dapodik-bg-pattern.svg') }}");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-color: #f5f5f9;
        }

        /* Styling Header Ikon */
        .login-header {
            position: absolute;
            top: 2rem; left: 2rem; right: 2rem;
            display: flex; justify-content: space-between; align-items: center;
            pointer-events: none;
        }
        .login-header .header-icon i {
            font-size: 2.5rem; color: #566a7f; opacity: 0.5;
        }

        /* 1. ANIMASI TANGAN MELAMBAI 👋 */
        .wave-hand {
            display: inline-block;
            animation: wave 2.5s infinite; /* Gerak terus */
            transform-origin: 70% 70%; /* Titik poros di bawah kanan */
            font-size: 1.5rem;
        }

        @keyframes wave {
            0% { transform: rotate(0deg); }
            10% { transform: rotate(14deg); }
            20% { transform: rotate(-8deg); }
            30% { transform: rotate(14deg); }
            40% { transform: rotate(-4deg); }
            50% { transform: rotate(10deg); }
            60%, 100% { transform: rotate(0deg); }
        }

        /* 2. FIX TOMBOL SHOW PASSWORD 👁️ */
        #togglePassword {
            cursor: pointer;
            z-index: 10; /* Pastikan dia di atas */
        }
        /* Trik Jitu: Matikan pointer-events di ikon biar klik tembus ke tombol */
        #togglePassword i {
            pointer-events: none;
        }

        /* Tombol Masuk Custom */
        .btn-masuk {
            background-color: #008493 !important;
            border-color: #008493 !important;
            color: #fff;
            transition: all 0.2s;
        }
        .btn-masuk:hover {
            background-color: #006f7b !important;
            border-color: #006f7b !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 132, 147, 0.4);
        }
    </style>
</head>

<body>
    <header class="login-header">
        <div class="header-icon"><i class='bx bxs-school'></i></div>
        <div class="header-icon"><i class='bx bxs-widget'></i></div>
    </header>

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                
                <div class="card">
                    <div class="card-body">
                        
                        <div class="app-brand justify-content-center mb-2">
                            <span class="app-brand-text demo text-body fw-bolder" style="text-transform: uppercase;">Sistem KCD</span>
                        </div>
                        
                        <h4 class="mb-2 text-center">
                            Selamat Datang! <span class="wave-hand">👋</span>
                        </h4>
                        <p class="mb-4 text-center text-muted">Masuk menggunakan NIP atau Username.</p>

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

                        <form id="formAuthentication" class="mb-3" action="#" method="POST">
                            @csrf

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
                                    <span class="input-group-text cursor-pointer" id="togglePassword">
                                        <i class="bx bx-hide"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
                                    <label class="form-check-label" for="remember-me"> Ingat Saya </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100 btn-masuk" type="submit">Masuk Sistem</button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    @php
        $routeLoginHash = base64_encode(route('login'));
        $inlineScript = <<<EOT
        document.addEventListener('DOMContentLoaded', function () {
            // Obfuscator: Route Action disembunyikan dan hanya dipasang sedetik sebelum submit
            const form = document.getElementById('formAuthentication');
            form.addEventListener('submit', function(e) {
                if(this.getAttribute('action') === '#' || this.getAttribute('action') === '') {
                    this.setAttribute('action', atob('$routeLoginHash'));
                }
            });

            // Toggle Password UI
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const icon = togglePassword.querySelector('i');

            togglePassword.addEventListener('click', function (e) {
                e.preventDefault(); 
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
EOT;
        // Minify sedehana
        $inlineScript = preg_replace('/\s+/', ' ', str_replace(["\r\n", "\r", "\n", "\t"], '', $inlineScript));
        $inlineScriptBase64 = base64_encode($inlineScript);
    @endphp
    
    <script src="data:text/javascript;base64,{!! $inlineScriptBase64 !!}"></script>
</body>
</html>