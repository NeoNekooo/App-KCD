<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuManagementController extends Controller
{
    // 🔥 UPDATE: Ganti 'Administrator' jadi 'Admin' biar sinkron sama Seeder
    private $roles = [
        'Admin',       // <--- INI YANG DIGANTI
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
        // 1. Ambil Menu Induk & Anak (Urut berdasarkan 'urutan')
        $menus = Menu::whereNull('parent_id')
                    ->with(['children' => function($q) {
                        $q->orderBy('urutan', 'asc');
                    }])
                    ->orderBy('urutan', 'asc')
                    ->get();

        // 2. Ambil Data Akses (Siapa boleh liat menu apa)
        // Hasilnya: [ 1 => ['Admin', 'Staff'], 2 => ['...'] ]
        $menuAccesses = DB::table('menu_accesses')
                            ->get()
                            ->groupBy('menu_id')
                            ->map(function ($items) {
                                return $items->pluck('role_name')->toArray();
                            });

        // 3. List Parent untuk Dropdown (Header gak boleh jadi parent)
        $parents = Menu::whereNull('parent_id')->where('is_header', false)->get();

        return view('admin.settings.menu.index', [
            'menus' => $menus,
            'parents' => $parents,
            'roles' => $this->roles,
            'accesses' => $menuAccesses
        ]);
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);

        DB::transaction(function () use ($request) {
            // A. Simpan Data Menu
            $menu = Menu::create([
                'title'     => $request->title,
                'slug'      => $request->slug,
                'icon'      => $request->icon,
                'route'     => $request->route_name,
                'parent_id' => $request->parent_id,
                'urutan'    => $request->urutan ?? 0,
                'is_header' => $request->has('is_header'),
                'is_active' => true,
            ]);

            // B. Simpan Hak Akses Role
            if ($request->has('roles')) {
                foreach ($request->roles as $role) {
                    DB::table('menu_accesses')->insert([
                        'role_name' => $role,
                        'menu_id'   => $menu->id
                    ]);
                }
            }
        });

        return back()->with('success', 'Menu berhasil dibuat & akses role disimpan!');
    }

    public function update(Request $request, $id)
    {
        $this->validateRequest($request, $id);
        $menu = Menu::findOrFail($id);

        DB::transaction(function () use ($request, $menu) {
            // A. Update Data Menu
            $menu->update([
                'title'     => $request->title,
                'slug'      => $request->slug,
                'icon'      => $request->icon,
                'route'     => $request->route_name,
                'parent_id' => $request->parent_id,
                'urutan'    => $request->urutan ?? 0,
                'is_header' => $request->has('is_header'),
            ]);

            // B. Reset & Update Hak Akses Role
            DB::table('menu_accesses')->where('menu_id', $menu->id)->delete();
            
            if ($request->has('roles')) {
                foreach ($request->roles as $role) {
                    DB::table('menu_accesses')->insert([
                        'role_name' => $role,
                        'menu_id'   => $menu->id
                    ]);
                }
            }
        });

        return back()->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        // Hapus akses terkait (biar bersih)
        DB::table('menu_accesses')->where('menu_id', $id)->delete();
        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus!');
    }

    private function validateRequest($request, $id = null)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug'  => 'required|string|max:255|unique:menus,slug,' . $id,
            'roles' => 'array'
        ]);
    }
}