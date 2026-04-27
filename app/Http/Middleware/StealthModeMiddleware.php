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
            
            // 1. Tambahkan Header Anti-Kepo
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');

            // 2. Lenyapkan Jejak SourceMapping secara TOTAL
            // Ini yang bikin folder webpack:// dan build/assets Lenyap dari F12
            $content = preg_replace('/(\/\/[#@]\s*sourceMappingURL=.*|\/\*[\s\S]*?sourceMappingURL=[\s\S]*?\*\/)/is', '', $content);
            
            $response->setContent($content);
        }

        return $response;
    }
}
