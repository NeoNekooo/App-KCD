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
                    ->with('childrenRecursive') // Load all children recursively
                    ->orderBy('urutan', 'asc')
                    ->get();

        // 2. Ambil Data Akses (Siapa boleh liat menu apa)
        $menuAccesses = DB::table('menu_accesses')
                            ->get()
                            ->groupBy('menu_id')
                            ->map(function ($items) {
                                return $items->pluck('role_name')->toArray();
                            });

        // 3. Siapkan List Menu untuk Dropdown Parent (dengan Indentasi)
        $allPossibleParents = Menu::where('is_header', false)->orderBy('urutan', 'asc')->get();
        $nestedParents = $this->buildNestedMenu($allPossibleParents);
        $flattenedParents = [];
        $this->flattenMenu($nestedParents, $flattenedParents);

        return view('admin.settings.menu.index', [
            'menus' => $menus,
            'parents' => $flattenedParents, // Pass flattened parents with depth
            'roles' => $this->roles,
            'accesses' => $menuAccesses
        ]);
    }

    /**
     * Build a nested array from a flat collection of menus.
     *
     * @param \Illuminate\Support\Collection $elements
     * @param int $parentId
     * @return array
     */
    private function buildNestedMenu($elements, $parentId = null)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildNestedMenu($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    /**
     * Flatten a nested menu array into a linear list with depth information.
     *
     * @param array $nestedMenu
     * @param array $flattenedList
     * @param int $depth
     * @return void
     */
    private function flattenMenu($nestedMenu, array &$flattenedList, $depth = 0)
    {
        foreach ($nestedMenu as $menuItem) {
            $menuItem->depth = $depth;
            $flattenedList[] = $menuItem;
            if (isset($menuItem->children) && count($menuItem->children) > 0) {
                $this->flattenMenu($menuItem->children, $flattenedList, $depth + 1);
            }
        }

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