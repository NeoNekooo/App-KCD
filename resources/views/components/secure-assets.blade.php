@props([
    'css' => [], 
    'js' => [], 
    'boxicons' => false
])

@php
    $outputTags = [];

    // Vite Assets Obfuscation (CSS & JS)
    $manifestPath = public_path('build/manifest.json');
    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        
        // CSS Processing
        foreach((array) $css as $cssItem) {
            if (isset($manifest[$cssItem]['file'])) {
                $path = public_path('build/' . $manifest[$cssItem]['file']);
                if(file_exists($path)) {
                    $rawCss = file_get_contents($path);

                    // FIX MUTLAK BOXICONS: 
                    // Kita tidak bisa menidurkan Base64 di dalam Base64 (Chrome nge-blokir).
                    // Solusi: Kita ubah path url('./sneat...') dan url('/vendor...') bawaan Vite
                    // menjadi URL absolut ke domain aslinya.
                    $rawCss = str_replace('./sneat/vendor/fonts/boxicons', asset('vendor/fonts/boxicons'), $rawCss);
                    $rawCss = str_replace('/vendor/fonts/boxicons/boxicons', asset('vendor/fonts/boxicons'), $rawCss);

                    // Minify & Encode CSS as Data URI
                    $rawCss = preg_replace('/\s+/', ' ', str_replace(["\r\n", "\r", "\n", "\t"], '', $rawCss));
                    $outputTags[] = '<link rel="stylesheet" href="data:text/css;base64,' . base64_encode($rawCss) . '">';
                }
            }
        }

        // JS Processing Moduler Data URI
        foreach((array) $js as $jsItem) {
            if (isset($manifest[$jsItem]['file'])) {
                $path = public_path('build/' . $manifest[$jsItem]['file']);
                if(file_exists($path)) {
                    $rawJs = file_get_contents($path);
                    $outputTags[] = '<script type="module" src="data:text/javascript;base64,' . base64_encode($rawJs) . '"></script>';
                }
            }
        }
    }

    // Output all obfuscated Data URIs
    echo implode("\n", $outputTags);
@endphp
