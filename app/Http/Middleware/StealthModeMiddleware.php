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
            
            // --- GHAIB TOTAL (BASE64) ---
            // Karena user ingin 'View Page Source' benar-benar bersih seperti halaman login.
            $encoded = base64_encode($content);
            
            $stealthHtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">
    <title>System Loading...</title>
</head>
<body style="margin: 0; background-color: #f5f5f9;">
    <div id="_sys_loader" style="display:flex; height:100vh; width:100vw; align-items:center; justify-content:center; font-family:sans-serif; color:#696cff; font-weight:bold;">
        Initializing Secure Session...
    </div>
    <textarea id="_sys_data" style="display:none">{$encoded}</textarea>
    <script>
        (function(){
            try {
                var data = document.getElementById('_sys_data').value;
                var binStr = atob(data);
                var bytes = new Uint8Array(binStr.length);
                for (var i = 0; i < binStr.length; i++) {
                    bytes[i] = binStr.charCodeAt(i);
                }
                var decoded = new TextDecoder("utf-8").decode(bytes);
                
                // Hapus atribut body/html bawaan loader agar tidak merusak scroll
                document.documentElement.removeAttribute("style");
                document.body.removeAttribute("style");
                
                document.open("text/html", "replace");
                document.write(decoded);
                document.close();
                
                // Pancing ulang event agar Chart.js / ApexCharts jalan
                setTimeout(function(){
                    window.dispatchEvent(new Event('load'));
                    document.dispatchEvent(new Event('DOMContentLoaded'));
                }, 150);
            } catch(e) {
                document.getElementById('_sys_loader').innerText = "Security Error. Please Refresh.";
            }
        })();
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
