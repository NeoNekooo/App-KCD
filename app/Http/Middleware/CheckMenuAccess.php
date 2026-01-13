<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMenuAccess
{
    public function handle(Request $request, Closure $next, string $slug)
    {
        $user = Auth::user();
        
        // Pastikan user login
        if (!$user) {
            return redirect('/');
        }

        $role    = $user->role; 
        $subRole = session('sub_role');

        // ==================================================================
        // PERBAIKAN DISINI:
        // Ganti 'menu_access' menjadi 'sidebar_menu' (sesuai nama file di folder config/)
        // ==================================================================
        $roleMap       = config('sidebar_menu.role_map', []);
        $subRoleMap    = config('sidebar_menu.sub_role_map', []);
        $adminExcluded = config('sidebar_menu.admin_excluded_menus', []);

        // Debugging Darurat (Aktifkan kalau masih error, nanti hapus lagi)
        /*
        dd([
            'File Config Terbaca?' => !empty($roleMap) ? 'YA' : 'TIDAK (Cek nama file config!)',
            'Role User' => $role,
            'Slug Tujuan' => $slug,
            'Izin Role Ini' => $roleMap[$role] ?? 'ZONK',
        ]);
        */

        $hasFullAccess = isset($roleMap[$role]) && in_array('*', $roleMap[$role]);

        // Helper function (Pastikan helper ini sudah di-load di composer.json / autoload)
        if (!canAccessMenu(
            $slug,
            $role,
            $subRole,
            $roleMap,
            $subRoleMap,
            $hasFullAccess,
            $adminExcluded
        )) {
            // Biar tau errornya kenapa, kita kasih pesan spesifik di log/layar
            abort(403, "Akses Ditolak. Role: $role tidak punya izin ke menu: $slug");
        }

        return $next($request);
    }
}