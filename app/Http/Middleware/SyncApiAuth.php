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

    // Ambil token dari DB yang tadi diinput manual
    $tokenDb = \App\Models\Setting::where('key', 'api_sync_token')->value('value');

    // Validasi:
    // 1. Token di DB tidak boleh kosong (belum di-setting)
    // 2. Token header harus sama persis dengan di DB
    if (!$tokenDb || $tokenHeader !== $tokenDb) {
         abort(403, 'Unauthorized action. Token mismatch or not configured.');
    }

    return $next($request);
}
}
