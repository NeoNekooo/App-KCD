<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMenuAccess
{
    public function handle(Request $request, Closure $next, string $slug)
    {
        $user    = auth()->user();
        $role    = $user?->peran_id_str;
        $subRole = session('sub_role');

        $roleMap    = config('menu_access.role_map', []);
        $subRoleMap = config('menu_access.sub_role_map', []);
        $adminExcluded = config('menu_access.admin_excluded_menus', []);

        $hasFullAccess = isset($roleMap[$role]) && in_array('*', $roleMap[$role]);

        if (!canAccessMenu(
            $slug,
            $role,
            $subRole,
            $roleMap,
            $subRoleMap,
            $hasFullAccess,
            $adminExcluded
        )) {
            abort(403);
        }

        return $next($request);
    }
}
