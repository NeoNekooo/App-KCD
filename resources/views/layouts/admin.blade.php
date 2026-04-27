@php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    if (!function_exists('injectViteAsset')) {
        function injectViteAsset($resourcePath) {
            $manifestPath = public_path('build/manifest.json');
            if (!file_exists($manifestPath)) return app(\Illuminate\Foundation\Vite::class)([$resourcePath])->toHtml();
            $manifest = json_decode(file_get_contents($manifestPath), true);
            if (!isset($manifest[$resourcePath])) return '';
            $entry = $manifest[$resourcePath];
            $filename = $entry['file'];

            // Pakai Jalur Siluman Global (LEBIH CEPAT & STABIL)
            $secureUrl = url("/system/core/" . $filename);

            if (str_ends_with($resourcePath, '.css')) {
                return '<link rel="stylesheet" href="' . $secureUrl . '">';
            }
            if (str_ends_with($resourcePath, '.js')) {
                return '<script type="module" src="' . $secureUrl . '"></script>';
            }
            return '';
        }
    }

    $boxiconsTag = '';
    $boxiconsPath = public_path('vendor/fonts/boxicons.css');
    if (file_exists($boxiconsPath)) {
        $content = file_get_contents($boxiconsPath);
        $woff2Path = public_path('vendor/fonts/boxicons/boxicons.woff2');
        if (file_exists($woff2Path)) {
            $woff2B64 = base64_encode(file_get_contents($woff2Path));
            $woff2DataUri = "data:font/woff2;charset=utf-8;base64," . $woff2B64;
            $newFontFace = "@font-face { font-family: 'boxicons'; font-weight: normal; font-style: normal; src: url('$woff2DataUri') format('woff2'); }";
            $content = preg_replace('/@font-face\s*\{[^}]+\}/is', $newFontFace, $content);
        }
        $boxiconsTag = '<style id="_bx_gh_admin">' . $content . '</style>';
    }
@endphp
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets/') }}" data-template="vertical-menu-template-free" data-layout="wide">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $appSettings['site_name'] ?? 'MANDALA' }} | @yield('title')</title>

    {!! $boxiconsTag !!}
    {!! injectViteAsset('resources/css/app.css') !!}
    @stack('styles')
</head>
<body style="background-color: #f5f5f9;">
    @php $is2faForced = Auth::check() && !Auth::user()->google2fa_enabled; @endphp
    @include('layouts.partials.toast')

    <div class="layout-wrapper layout-content-navbar {{ $is2faForced ? 'layout-without-menu' : '' }}">
        <div class="layout-container">
            @if(!$is2faForced) @include('layouts.partials.sidebar') @endif
            <div class="layout-page">
                @if(!$is2faForced) @include('layouts.partials.topbar') @endif
                <div class="content-wrapper">
                    <div class="container flex-grow-1 container-p-y {{ $is2faForced ? 'd-flex justify-content-center align-items-center' : '' }}">
                        <div class="{{ $is2faForced ? 'w-100' : '' }}" style="{{ $is2faForced ? 'max-width: 1000px;' : '' }}">
                            @yield('content')
                        </div>
                    </div>
                    @include('layouts.partials.footer')
                </div>
            </div>
        </div>
    </div>

    <script async defer src="https://buttons.github.io/buttons.js"></script>
    {!! injectViteAsset('resources/js/app.js') !!}
    @stack('scripts')
</body>
</html>
