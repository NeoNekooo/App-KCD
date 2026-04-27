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
            
            // 1. Tambahkan Header Keamanan yang lebih ramah DevTools
            $response->headers->set('Cache-Control', 'no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');

            // Tetap biarkan content utuh agar tidak error
            $response->setContent($content);
        }

        return $response;
    }
}
