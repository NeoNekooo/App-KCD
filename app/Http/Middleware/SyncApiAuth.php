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
        
        // 1. Cek di Database (Priority)
        $tokenDb = \App\Models\Setting::where('key', 'api_sync_token')->value('value');
        
        // 2. Fallback ke Config (.env)
        if (empty($tokenDb)) {
            $tokenDb = config('app.api_secret_key');
        }
        

        // Validasi
        if (!$tokenDb || $tokenHeader !== $tokenDb) {
             $msg = 'Unauthorized action. Token mismatch.';
             
             if (config('app.debug')) {
                 $received = $tokenHeader ? substr($tokenHeader, 0, 3) . '...' : 'NULL';
                 $expected = $tokenDb ? substr((string)$tokenDb, 0, 3) . '...' : 'NOT_SET';
                 $msg .= " (Sent: $received, Expected: $expected)";
             }
             abort(403, $msg);
        }

        return $next($request);
    }
}