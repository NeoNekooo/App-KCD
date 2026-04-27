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
            
            // 1. Ambil Judul Halaman Asli secara Dinamis
            $pageTitle = 'Sistem KCD';
            if (preg_match('/<title>(.*?)<\/title>/is', $content, $matches)) {
                $pageTitle = $matches[1];
            }

            // 2. Tambahkan Header Anti-Kepo
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');

            // 3. Lenyapkan Jejak SourceMapping
            $content = preg_replace('/(\/\/[#@]\s*sourceMappingURL=.*|\/\*[\s\S]*?sourceMappingURL=[\s\S]*?\*\/)/i', '', $content);
            
            // 4. Transformasikan seluruh BODY menjadi Ghaib (Base64)
            $htmlBase64 = base64_encode($content);
            
            $stealthHtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$pageTitle}</title>
</head>
<body style="background-color: #f5f5f9; margin:0;">
    <script>
        (function(){
            document.open();
            document.write(decodeURIComponent(escape(atob("{$htmlBase64}"))));
            document.close();
        })();
    </script>
    <noscript><div style="padding:20px;text-align:center;">Harap aktifkan JavaScript.</div></noscript>
</body>
</html>
HTML;
            $response->setContent($stealthHtml);
        }

        return $response;
    }
}
