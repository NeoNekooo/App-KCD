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
            
            $baseUrl = url('/');
            
            // 1. Sembunyikan folder build (Vite)
            $content = str_replace($baseUrl . '/build/', $baseUrl . '/sys-assets/core/', $content);
            $content = str_replace('"/build/', '"/sys-assets/core/', $content);
            $content = str_replace("'build/", "'sys-assets/core/", $content);
            
            // 2. Sembunyikan folder storage (Foto dll)
            $content = str_replace($baseUrl . '/storage/', $baseUrl . '/sys-assets/media/', $content);
            $content = str_replace('"/storage/', '"/sys-assets/media/', $content);
            $content = str_replace("'storage/", "'sys-assets/media/", $content);
            
            // 3. Sembunyikan folder vendor
            $content = str_replace($baseUrl . '/vendor/', $baseUrl . '/sys-assets/vendor/', $content);
            $content = str_replace('"/vendor/', '"/sys-assets/vendor/', $content);
            $content = str_replace("'vendor/", "'sys-assets/vendor/", $content);
            
            // Ekstrak title asli agar tab browser tidak stuck di "System Loading..."
            preg_match('/<title>(.*?)<\/title>/is', $content, $matches);
            $realTitle = $matches[1] ?? 'System Secure Loading';
            
            // --- GHAIB TOTAL (BASE64) ---
            // Bungkus HTML yang sudah disamarkan ini ke dalam Base64
            $encoded = base64_encode($content);
            
            $stealthHtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">
    <title>{$realTitle}</title>
</head>
<body style="margin: 0; background-color: #f5f5f9;">
    <div id="_sys_loader" style="display:flex; height:100vh; width:100vw; align-items:center; justify-content:center; font-family:sans-serif; color:#696cff; font-weight:bold;">
        Initializing Secure Session...
    </div>
    <textarea id="_sys_data" style="display:none">{$encoded}</textarea>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            try {
                var data = document.getElementById('_sys_data').value;
                var binStr = atob(data);
                var bytes = new Uint8Array(binStr.length);
                for (var i = 0; i < binStr.length; i++) {
                    bytes[i] = binStr.charCodeAt(i);
                }
                var decoded = new TextDecoder("utf-8").decode(bytes);
                
                // Dokumen sudah selesai diload, document.open akan MERESET seluruh isi DOM (menghapus loader).
                document.open("text/html", "replace");
                document.write(decoded);
                document.close();
                
                // Pastikan title terupdate
                document.title = "{$realTitle}";
            } catch(e) {
                var loader = document.getElementById('_sys_loader');
                if(loader) loader.innerText = "Security Error. Please Refresh.";
            }
        });
    </script>
</body>
</html>
HTML;
            
            $response->setContent($stealthHtml);

            // Header anti-kepo ringan
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
