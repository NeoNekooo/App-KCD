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

            // Kembalikan ke normal, hanya set header anti-kepo ringan
            $response->headers->set('Cache-Control', 'no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
