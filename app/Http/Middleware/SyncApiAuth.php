<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting; // Import Model Setting

class SyncApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $tokenHeader = $request->header('X-Sync-Token');

            $tokenDb = env('API_SECRET_KEY');
        

        // Validasi
        if (!$tokenDb || $tokenHeader !== $tokenDb) {
             // Debugging message: beri tahu apa yang diharapkan server (Hanya di mode debug/local)
             $msg = 'Unauthorized action. Token mismatch.';
             if (env('APP_DEBUG')) {
                 $msg .= " (Server expects: " . substr($tokenDb, 0, 5) . "...)";
             }
             abort(403, $msg);
        }

        return $next($request);
    }
}