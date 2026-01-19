<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleAccessController extends Controller
{
    // 🔥 DAFTAR ROLE PASTI (Hardcode)
    // Jadi tab-nya bakal selalu muncul, siap disetting kapan aja.
    private $roles = [
        'Admin',       // Sesuai Seeder terakhir
        'Kepala',
        'Kasubag',
        'Kepegawaian',
        'Kesiswaan',
        'Sarpras',
        'Divisi IT',
        'Staff'
    ];

    public function index()
    {
        // 1. Ambil Menu (Urut dari atas ke bawah)
        $menus = Menu::orderBy('urutan', 'asc')->get();

        // 2. Ambil data akses yang sudah tersimpan di database
        $allAccess = DB::table('menu_accesses')->get();
        
        $currentAccess = [];
        foreach ($this->roles as $role) {
            // Kita cari apakah role ini sudah punya akses di database
            // Pakai strtolower biar pencariannya tidak sensitif huruf besar/kecil
            $roleIds = $allAccess->filter(function($item) use ($role) {
                return strtolower($item->role_name) === strtolower($role);
            })->pluck('menu_id')->toArray();

            $currentAccess[$role] = $roleIds;
        }

        // Kirim $this->roles ke view biar jadi Tab
        return view('admin.settings.role_access.index', [
            'roles' => $this->roles, 
            'menus' => $menus,
            'currentAccess' => $currentAccess
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string',
            'menu_ids'  => 'array'
        ]);

        $role = $request->role_name;
        $menuIds = $request->menu_ids ?? []; // Kalau kosong berarti dicabut semua aksesnya

        DB::transaction(function () use ($role, $menuIds) {
            // 1. Hapus settingan lama buat role ini
            DB::table('menu_accesses')
                ->where(DB::raw('LOWER(role_name)'), strtolower($role))
                ->delete();

            // 2. Masukkan settingan baru
            $insertData = [];
            foreach ($menuIds as $menuId) {
                $insertData[] = [
                    'role_name' => $role,
                    'menu_id'   => $menuId
                ];
            }

            if (!empty($insertData)) {
                DB::table('menu_accesses')->insert($insertData);
            }
        });

        return back()->with('success', "Sip! Hak akses untuk **$role** berhasil disimpan.");
    }
}