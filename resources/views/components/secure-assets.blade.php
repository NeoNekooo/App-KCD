@props([
    'css' => [], 
    'js' => [], 
    'boxicons' => false
])

@php
    $outputTags = [];

    // 1. Boxicons Font Obfuscation (If Requested)
    if ($boxicons) {
        if(file_exists(public_path('vendor/fonts/boxicons.css'))) {
            $cssBoxicons = file_get_contents(public_path('vendor/fonts/boxicons.css'));
            $woff2Path = public_path('vendor/fonts/boxicons.woff2');
            
            if(file_exists($woff2Path)) {
                $base64Woff2 = base64_encode(file_get_contents($woff2Path));
                
                // BONGKAR PASANG @font-face: 
                // Karena Cascading CSS, 'src' terakhir yang menang. Ganti seluruh blok @font-face aslinya ke string base64 murni kita
                $cssBoxicons = preg_replace('/@font-face\s*\{[^}]+\}/', '', $cssBoxicons);
                
                $newFontFace = "@font-face { font-family: 'boxicons'; font-weight: normal; font-style: normal; src: url('data:font/woff2;charset=utf-8;base64," . $base64Woff2 . "') format('woff2'); }\n";
                $cssBoxicons = $newFontFace . $cssBoxicons;

                // CSS Minification
                $cssBoxicons = preg_replace('/\s+/', ' ', str_replace(["\r\n", "\r", "\n", "\t"], '', $cssBoxicons));
                $outputTags[] = '<link rel="stylesheet" href="data:text/css;base64,' . base64_encode($cssBoxicons) . '">';
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
                    $rawCss = preg_replace('/\s+/', ' ', str_replace(["\r\n", "\r", "\n", "\t"], '', file_get_contents($path)));
                    $outputTags[] = '<link rel="stylesheet" href="data:text/css;base64,' . base64_encode($rawCss) . '">';
                }
            }
        }

        // JS Processing
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
