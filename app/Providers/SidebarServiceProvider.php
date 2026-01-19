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
            $user = Auth::user();
            
            // Cek session role, atau ambil dari user role, atau default null
            $role = session('role') ?? $user?->role;

            // ==========================================================
            // 1. HITUNG NOTIFIKASI (BADGE LOGIC)
            // ==========================================================
            $notifData = [];

            if ($user && Schema::hasTable('pengajuan_sekolahs')) {
                $userRole = strtolower($user->role ?? '');
                
                // Identifikasi Jabatan
                $isKasubag = ($user->pegawaiKcd && strcasecmp($user->pegawaiKcd->jabatan, 'Kasubag') === 0) || $userRole === 'kasubag';
                $isKepala = $userRole === 'kepala';

                // Tentukan Status Target Meja Kerja
                if ($isKasubag) {
                    $targetStatus = ['Verifikasi Kasubag'];
                } elseif ($isKepala) {
                    $targetStatus = ['Verifikasi Kepala'];
                } else {
                    $targetStatus = ['Proses', 'Verifikasi Berkas'];
                }

                // Cek Tugas Pegawai (untuk filtering query)
                $kategoriTugas = null;
                if (!$isKasubag && !$isKepala && $user->pegawai_kcd_id) {
                    $tugasActive = TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)
                                                  ->where('is_active', 1)
                                                  ->first();
                    $kategoriTugas = $tugasActive ? $tugasActive->kategori_layanan : null;
                }

                // Base Query
                $baseQuery = PengajuanSekolah::whereIn('status', $targetStatus);

                // Filter Query berdasarkan Tugas Pegawai
                if ($kategoriTugas && !in_array(strtolower($kategoriTugas), ['umum', 'all'])) {
                    $baseQuery->where(function($q) use ($kategoriTugas) {
                        $q->where('kategori', 'LIKE', '%' . $kategoriTugas . '%')
                          ->orWhere('kategori', 'LIKE', '%' . str_replace('-', ' ', $kategoriTugas) . '%');
                    });
                }

                // Hitung per Kategori
                $mapKategori = [
                    'notif_kp'       => 'pangkat',
                    'notif_kgb'      => 'kgb',
                    'notif_mutasi'   => 'mutasi',
                    'notif_relokasi' => 'relokasi',
                    'notif_satya'    => 'satya',
                    'notif_hukdis'   => 'hukuman',
                ];

                foreach ($mapKategori as $key => $keyword) {
                    $count = (clone $baseQuery)->where('kategori', 'LIKE', '%' . $keyword . '%')->count();
                    $notifData[$key] = $count > 0 ? $count : '';
                }

                // Total Induk
                $totalGtk = (clone $baseQuery)->count();
                $notifData['total_layanan_gtk'] = $totalGtk > 0 ? $totalGtk : '';
            }

            // ==========================================================
            // 2. AMBIL MENU DARI DATABASE (FIXED)
            // ==========================================================
            
            $allowedMenuIds = DB::table('menu_accesses')
                                ->where('role_name', $role)
                                ->pluck('menu_id');

            $menus = Menu::whereIn('id', $allowedMenuIds)
                        ->whereNull('parent_id') 
                        ->where('is_active', true) // Pastikan kolom ini ada di DB
                        ->with(['children' => function($query) use ($allowedMenuIds) {
                            $query->whereIn('id', $allowedMenuIds)
                                  ->where('is_active', true)
                                  ->orderBy('urutan', 'asc'); // 🔥 GANTI 'order' JADI 'urutan'
                        }])
                        ->orderBy('urutan', 'asc') // 🔥 GANTI 'order' JADI 'urutan'
                        ->get();

            // ==========================================================
            // 3. GABUNGKAN DATA (INJECT BADGE & FILTER MENU)
            // ==========================================================
            
            $roleStr = strtolower($role ?? ''); // Fix null safety
            
            $isStafVerifikator = $user?->pegawai_kcd_id 
                                 && $roleStr !== 'admin' 
                                 && $roleStr !== 'kasubag'
                                 && $roleStr !== 'kepala';

            foreach ($menus as $menu) {

                // A. Inject Badge Induk
                if ($menu->badge_key && isset($notifData[$menu->badge_key])) {
                    $menu->badge_value = $notifData[$menu->badge_key];
                }

                // B. LOGIC FILTER MENU LAYANAN GTK
                if ($menu->slug === 'layanan-gtk' && $isStafVerifikator) {
                    
                    if (isset($kategoriTugas) && $kategoriTugas) { // Fix undefined variable check
                        $mapTugasToSlug = [
                            'mutasi' => 'layanan-mutasi', 'kenaikan-pangkat' => 'layanan-kp',
                            'kgb' => 'layanan-kgb', 'relokasi' => 'layanan-relokasi',
                            'satya-lencana' => 'layanan-satya', 'hukuman-disiplin' => 'layanan-hukdis'
                        ];

                        $isUmum = in_array(strtolower($kategoriTugas), ['umum', 'all']);
                        
                        if (!$isUmum && isset($mapTugasToSlug[$kategoriTugas])) {
                            $slugDiizinkan = $mapTugasToSlug[$kategoriTugas];
                            
                            $filteredChildren = $menu->children->filter(function($child) use ($slugDiizinkan) {
                                return $child->slug === $slugDiizinkan || $child->slug === 'verifikasi-surat';
                            });
                            $menu->setRelation('children', $filteredChildren);
                        }
                    } else {
                        // Tidak ada tugas aktif -> kosongkan submenu
                        $menu->setRelation('children', collect([]));
                    }
                }

                // C. Inject Badge Anak
                foreach ($menu->children as $child) {
                    if ($child->badge_key && isset($notifData[$child->badge_key])) {
                        $child->badge_value = $notifData[$child->badge_key];
                    }
                }
            }

            // Kirim ke View
            $view->with('menus', $menus);
        });
    }
}