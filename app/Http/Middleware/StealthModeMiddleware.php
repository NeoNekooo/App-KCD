<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StealthModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof Response && 
            str_contains($response->headers->get('Content-Type'), 'text/html') && 
            !$request->expectsJson() && 
            !$request->ajax()) {

            $content = $response->getContent();
            
            // Bungkus Seluruh HTML dalam Base64 (Ghaib Total)
            $encoded = base64_encode($content);
            
            $stealthHtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">
    <title>System Loading...</title>
    <style>body{background:#f5f5f9;margin:0;display:flex;align-items:center;justify-content:center;height:100vh;font-family:sans-serif;color:#696cff;}</style>
</head>
<body>
    <div id="_sys_loader">Initializing Secure Session...</div>
    <script>
        (function(){
            try {
                var data = "{$encoded}";
                
                // Dekoder UTF-8 Modern agar tidak ada tulisan aneh (ðUUU)
                var binStr = atob(data);
                var bytes = new Uint8Array(binStr.length);
                for (var i = 0; i < binStr.length; i++) {
                    bytes[i] = binStr.charCodeAt(i);
                }
                var decoded = new TextDecoder("utf-8").decode(bytes);
                
                document.open();
                document.write(decoded);
                document.close();
                
                // Pastikan Judul Halaman kembali Normal
                var parser = new DOMParser();
                var doc = parser.parseFromString(decoded, 'text/html');
                if (doc.title) document.title = doc.title;

                // --- TRIK SAKTI AGAR CHART TETAP NYALA ---
                setTimeout(function(){
                    window.dispatchEvent(new Event('load'));
                    document.dispatchEvent(new Event('DOMContentLoaded'));
                }, 100);
            } catch(e) {
                console.error("Stealth Error:", e);
                document.getElementById('_sys_loader').innerText = "System Error. Please Refresh.";
            }
        })();
    </script>
</body>
</html>
HTML;
            
            $response->setContent($stealthHtml);
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
