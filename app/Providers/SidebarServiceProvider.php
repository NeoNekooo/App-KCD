<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Menu;
use App\Models\TugasPegawaiKcd;
use App\Models\PengajuanSekolah;

class SidebarServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.partials.sidebar', function ($view) {
            // 🔥 FIX: Cari user di guard Admin, kalau gak ada cari di guard Siswa
            $user = Auth::guard('web')->user() ?? Auth::guard('pengguna')->user();
            
            // 🔥 Ambil role dari session, atau dari kolom 'role' (Admin/Pegawai), 
            // atau dari 'peran_id_str' (Siswa/Guru/Pengguna)
            $role = session('role') ?? ($user instanceof \App\Models\Pengguna ? $user->peran_id_str : $user?->role);

            $notifData = [];
            if ($user && Schema::hasTable('pengajuan_sekolahs')) {
                $userRole = strtolower($user->role ?? '');
                
                $isKasubag = ($user->pegawaiKcd && strcasecmp($user->pegawaiKcd->jabatan, 'Kasubag') === 0) || $userRole === 'kasubag';
                $isKepala = $userRole === 'kepala';

                $targetStatus = $isKasubag ? ['Verifikasi Kasubag'] : ($isKepala ? ['Verifikasi Kepala'] : ['Proses', 'Verifikasi Berkas']);

                $kategoriTugas = null;
                if (!$isKasubag && !$isKepala && $user->pegawai_kcd_id) {
                    $tugasActive = TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)->where('is_active', 1)->first();
                    $kategoriTugas = $tugasActive ? $tugasActive->kategori_layanan : null;
                }

                $baseQuery = PengajuanSekolah::whereIn('status', $targetStatus)
                    ->where(function($q) {
                        $q->where('tipe_pengaju', '!=', 'PD')->orWhereNull('tipe_pengaju');
                    });

                // --- FIX: Filter Query based on Task Array ---
                if ($kategoriTugas && is_array($kategoriTugas)) {
                    $isGeneral = collect($kategoriTugas)->contains(fn($k) => in_array(strtolower($k), ['umum', 'all']));
                    if (!$isGeneral) {
                        $baseQuery->where(function($q) use ($kategoriTugas) {
                            foreach ($kategoriTugas as $kategori) {
                                if (!empty($kategori)) {
                                    $q->orWhere('kategori', 'LIKE', '%' . $kategori . '%')
                                      ->orWhere('kategori', 'LIKE', '%' . str_replace('-', ' ', $kategori) . '%');
                                }
                            }
                        });
                    }
                }

                $mapKategori = [
                    'notif_kp' => 'pangkat', 'notif_kgb' => 'kgb', 'notif_mutasi' => 'mutasi',
                    'notif_relokasi' => 'relokasi', 'notif_satya' => 'satya', 'notif_hukdis' => 'hukuman',
                ];

                foreach ($mapKategori as $key => $keyword) {
                    $count = (clone $baseQuery)->where('kategori', 'LIKE', '%' . $keyword . '%')->count();
                    $notifData[$key] = $count > 0 ? $count : '';
                }
                $totalGtk = (clone $baseQuery)->count();
                $notifData['total_layanan_gtk'] = $totalGtk > 0 ? $totalGtk : '';

                $pdQuery = PengajuanSekolah::where('tipe_pengaju', 'PD')
                                           ->whereIn('status', $targetStatus);
                
                $totalPd = $pdQuery->count();
                $notifData['total_layanan_pd'] = $totalPd > 0 ? $totalPd : '';
            }

            $allowedMenuIds = $role 
                ? DB::table('menu_accesses')->where(DB::raw('LOWER(role_name)'), strtolower($role))->pluck('menu_id') 
                : collect([]);
            
            // Eager load childrenRecursive untuk meliput sub-submenu kedalaman berapapun
            $menus = Menu::whereIn('id', $allowedMenuIds)->whereNull('parent_id')->where('is_active', true)
                        ->with('childrenRecursive')
                        ->orderBy('urutan', 'asc')->get();

            $isStafVerifikator = $user?->pegawai_kcd_id && !in_array(strtolower($user->role ?? ''), ['admin', 'administrator', 'kasubag', 'kepala']);
            $isNotAdminOrOperator = !in_array(strtolower($user?->role ?? ''), ['admin', 'administrator', 'operator kcd']);

            foreach ($menus as $menu) {
                // --- BEND ROUTE DASHBOARD KHUSUS PEGAWAI ---
                if ($menu->route === 'admin.dashboard' && $isNotAdminOrOperator) {
                    $menu->route = 'admin.dashboard.pegawai';
                }

                if ($menu->slug === 'layanan-kcd' && $isStafVerifikator) {
                    if ($kategoriTugas && is_array($kategoriTugas)) {
                        $isUmum = collect($kategoriTugas)->contains(fn($k) => in_array(strtolower($k), ['umum', 'all']));
                        if (!$isUmum) {
                            $mapTugasToSlug = [
                                'mutasi' => 'layanan-mutasi', 'kenaikan-pangkat' => 'layanan-kp',
                                'kgb' => 'layanan-kgb', 'relokasi' => 'layanan-relokasi',
                                'satya-lencana' => 'layanan-satya', 'hukuman-disiplin' => 'layanan-hukdis',
                                'peserta-didik' => 'layanan-peserta-didik'
                            ];
                            $slugsDiizinkan = collect($kategoriTugas)->map(fn($k) => $mapTugasToSlug[strtolower($k)] ?? null)->filter()->all();
                            $slugsDiizinkan[] = 'verifikasi-surat'; // Asumsi selalu ada
                            
                            // Fungsi rekursif untuk menyaring menu
                            $filterChildren = function ($items) use (&$filterChildren, $slugsDiizinkan) {
                                return $items->filter(function ($item) use ($filterChildren, $slugsDiizinkan) {
                                    // 1. Jika menu punya anak, saring anaknya
                                    if ($item->relationLoaded('childrenRecursive') && $item->childrenRecursive->isNotEmpty()) {
                                        $filteredSons = $filterChildren($item->childrenRecursive);
                                        $item->setRelation('childrenRecursive', $filteredSons);
                                        // Tetap tampilkan parent/folder ini jika masih punya anak yang lolos filter
                                        if ($filteredSons->isNotEmpty()) return true;
                                    }
                                    
                                    // 2. Jika leaf node (tidak punya anak), cek apakah slug-nya diizinkan
                                    // (Jika merupakan parent kosong, otomatis tak ter-return berkat kondisi 1)
                                    return in_array($item->slug, $slugsDiizinkan);
                                })->values();
                            };

                            $filteredChildren = $filterChildren($menu->childrenRecursive);
                            $menu->setRelation('childrenRecursive', $filteredChildren);
                        }
                    } else {
                        $menu->setRelation('childrenRecursive', collect([]));
                    }
                }
            }

            // Buat fungsi closure rekursif untuk assign badge dari root hingga ke sub-submenu terdalam
            // sekaligus menjumlahkan/mengakumulasi badge dari anak ke induknya.
            $assignBadges = function($items) use (&$assignBadges, $notifData) {
                $totalBadgeContext = 0;

                foreach ($items as $item) {
                    $myBadge = 0;
                    
                    // 1. Ambil badge diri sendiri jika ada
                    if ($item->badge_key && isset($notifData[$item->badge_key])) {
                        $myBadge = (int) $notifData[$item->badge_key];
                    } elseif ($item->slug === 'layanan-peserta-didik' && isset($notifData['total_layanan_pd'])) {
                        $myBadge = (int) $notifData['total_layanan_pd'];
                    } elseif ($item->slug === 'layanan-gtk' && isset($notifData['total_layanan_gtk'])) {
                        $myBadge = (int) $notifData['total_layanan_gtk'];
                    }

                    // 2. Akumulasi dari anak-anaknya (Bottom-Up)
                    $childrenBadge = 0;
                    if ($item->relationLoaded('childrenRecursive') && $item->childrenRecursive->isNotEmpty()) {
                        $childrenBadge = $assignBadges($item->childrenRecursive);
                    } elseif ($item->relationLoaded('children') && $item->children->isNotEmpty()) {
                        $childrenBadge = $assignBadges($item->children);
                    }

                    // 3. Gabungkan badge diri sendiri + badge dari anak secara cerdas (mencegah overlapping)
                    // Jika Menu Induk memegang agregat manual tetapi sebenarnya Punya Anak,
                    // maka jangan tampilkan aggregated count-nya agar tidak bocor dari menu yang disembunyikan.
                    // Prioritaskan MURNI dari hasil tambah children-nya saja (bottom-up strict).
                    $hasChildren = ($item->relationLoaded('childrenRecursive') && $item->childrenRecursive->isNotEmpty()) || 
                                   ($item->relationLoaded('children') && $item->children->isNotEmpty());
                                   
                    if ($hasChildren) {
                        // Jika anak tidak merender badge satupun (karena belum dipasang badge_key di DB),
                        // maka prioritaskan memunculkan badge manual agregat yang ada pada induknya
                        $itemTotal = $childrenBadge > 0 ? $childrenBadge : $myBadge;
                    } else {
                        $itemTotal = $myBadge;
                    }
                    
                    // Assign ke menu ini jika > 0
                    if ($itemTotal > 0) {
                        $item->badge_value = $itemTotal;
                    }

                    // 4. Tambahkan ke total context tingkat ini untuk dilempar ke atas (parent)
                    $totalBadgeContext += $itemTotal;
                }

                return $totalBadgeContext;
            };
            
            $assignBadges($menus);
            $view->with('menus', $menus);
        });
    }
}