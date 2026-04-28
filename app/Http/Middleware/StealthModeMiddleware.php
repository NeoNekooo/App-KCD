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

        // Hanya proses response berupa HTML biasa
        if ($response instanceof Response && !$request->expectsJson() && !$request->ajax()) {
            
            // Abaikan rute proxy agar tidak infinite loop
            if ($request->is('assets/v1_*')) {
                return $response;
            }

            $contentType = $response->headers->get('Content-Type');
            if ($contentType && strpos($contentType, 'text/html') === false) {
                return $response;
            }

            $content = $response->getContent();
            $baseUrl = url('/');
            
            // --- HASH OBFUSCATION (PROFESSIONAL PATH MASKING) ---
            // Kita ganti folder sensitif dengan path yang terlihat seperti sistem CDN profesional
            
            // 1. Masking Vite / Build
            $content = str_replace($baseUrl . '/build/', $baseUrl . '/assets/v1_core/', $content);
            $content = str_replace('"/build/', '"/assets/v1_core/', $content);
            $content = str_replace("'build/", "'assets/v1_core/", $content);
            
            // 2. Masking Storage (Foto/File) - SANGAT PENTING
            $content = str_replace($baseUrl . '/storage/', $baseUrl . '/assets/v1_media/', $content);
            $content = str_replace('"/storage/', '"/assets/v1_media/', $content);
            $content = str_replace("'storage/", "'assets/v1_media/", $content);
            
            // 3. Masking Vendor (CSS/JS Library)
            $content = str_replace($baseUrl . '/vendor/', $baseUrl . '/assets/v1_lib/', $content);
            $content = str_replace('"/vendor/', '"/assets/v1_lib/', $content);
            $content = str_replace("'vendor/", "'assets/v1_lib/", $content);
            
            // Kembalikan konten yang sudah disamarkan
            $response->setContent($content);

            // Header anti-kepo ringan
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
