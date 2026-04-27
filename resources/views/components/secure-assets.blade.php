@props([
    'css' => [], 
    'js' => [], 
    'boxicons' => false
])

@php
    $outputTags = [];
    $combinedCss = '';

    // 1. Boxicons Font Obfuscation
    if ($boxicons) {
        if(file_exists(public_path('vendor/fonts/boxicons.css'))) {
            $cssBoxicons = file_get_contents(public_path('vendor/fonts/boxicons.css'));
            $woff2Path = public_path('vendor/fonts/boxicons.woff2');
            
            if(file_exists($woff2Path)) {
                $base64Woff2 = base64_encode(file_get_contents($woff2Path));
                
                // MENEBAS 100% BLOK @font-face LAMA
                $cssBoxicons = preg_replace('/@font-face\s*\{[^}]+\}/', '', $cssBoxicons);

                // MEMBUAT @font-face BARU (Tanpa Kutip agar aman saat lewat innerHTML)
                $newFontFace = "@font-face { font-family: 'boxicons'; font-style: normal; font-weight: 400; src: url(data:application/font-woff2;charset=utf-8;base64," . $base64Woff2 . ") format('woff2'); }\n";
                $combinedCss .= $newFontFace . $cssBoxicons;
            }
        }
    }

    // 2. Vite Assets Obfuscation (CSS & JS)
    $manifestPath = public_path('build/manifest.json');
    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        
        // CSS Processing
        foreach((array) $css as $cssItem) {
            if (isset($manifest[$cssItem]['file'])) {
                $path = public_path('build/' . $manifest[$cssItem]['file']);
                if(file_exists($path)) {
                    $rawCss = file_get_contents($path);
                    // Tebas font relasi Vite
                    $rawCss = preg_replace('/@font-face\s*\{[^}]*?font-family:\s*[\'"]?boxicons[\'"]?[^}]*\}/i', '', $rawCss);
                    $combinedCss .= $rawCss;
                }
            }
        }

        // JS Processing (JS tetap aman lewat Data URI Modular)
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

    // Eksekusi Injeksi CSS via Javascript Sinkronus 
    // Ini adalah 'The Ultimate Hack' untuk membypass Chrome CSP/Nested-Data-URI bug terhadap font murni.
    if (!empty($combinedCss)) {
        // Minify Super Ekstrim
        $combinedCss = preg_replace('/\s+/', ' ', str_replace(["\r\n", "\r", "\n", "\t"], '', $combinedCss));
        // Encode payload
        $payload = base64_encode($combinedCss);
        // Tulis via JS Sinkronus = Render langsung seakan Native HTML (No FOUC)
        $outputTags[] = "<script>\n    document.write('<style>' + decodeURIComponent(escape(atob('" . $payload . "'))) + '</style>');\n</script>";
    }

    // Output all obfuscated Payloads
    echo implode("\n", $outputTags);
@endphp
