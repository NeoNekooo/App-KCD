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
            
            // --- Trik Ghaib Versi Aman (Manipulasi Path di HTML) ---
            // Kita ganti path aslinya dengan path bayangan di HTML
            // agar di DevTools tidak terlihat folder build/assets, storage, dll.
            $baseUrl = url('/');
            
            // 1. Sembunyikan folder build (Vite)
            $content = str_replace($baseUrl . '/build/', $baseUrl . '/sys-assets/core/', $content);
            $content = str_replace('"/build/', '"/sys-assets/core/', $content);
            $content = str_replace("'build/", "'sys-assets/core/", $content);
            
            // 2. Sembunyikan folder storage
            $content = str_replace($baseUrl . '/storage/', $baseUrl . '/sys-assets/media/', $content);
            $content = str_replace('"/storage/', '"/sys-assets/media/', $content);
            $content = str_replace("'storage/", "'sys-assets/media/", $content);
            
            // 3. Sembunyikan folder vendor
            $content = str_replace($baseUrl . '/vendor/', $baseUrl . '/sys-assets/vendor/', $content);
            $content = str_replace('"/vendor/', '"/sys-assets/vendor/', $content);
            $content = str_replace("'vendor/", "'sys-assets/vendor/", $content);

            $response->setContent($content);

            // Header anti-kepo ringan
            $response->headers->set('Cache-Control', 'no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
