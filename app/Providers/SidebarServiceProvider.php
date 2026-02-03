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
            $role = session('role') ?? $user?->role;

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

                $baseQuery = PengajuanSekolah::whereIn('status', $targetStatus);

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
            }

            $allowedMenuIds = $role ? DB::table('menu_accesses')->where('role_name', $role)->pluck('menu_id') : collect([]);
            
            $menus = Menu::whereIn('id', $allowedMenuIds)->whereNull('parent_id')->where('is_active', true)
                        ->with(['children' => fn($q) => $q->whereIn('id', $allowedMenuIds)->where('is_active', true)->orderBy('urutan', 'asc')])
                        ->orderBy('urutan', 'asc')->get();

            $isStafVerifikator = $user?->pegawai_kcd_id && !in_array(strtolower($user->role), ['admin', 'administrator', 'kasubag', 'kepala']);

            foreach ($menus as $menu) {
                if ($menu->badge_key && isset($notifData[$menu->badge_key])) {
                    $menu->badge_value = $notifData[$menu->badge_key];
                }

                if ($menu->slug === 'layanan-gtk' && $isStafVerifikator) {
                    if ($kategoriTugas && is_array($kategoriTugas)) {
                        $isUmum = collect($kategoriTugas)->contains(fn($k) => in_array(strtolower($k), ['umum', 'all']));
                        if (!$isUmum) {
                            $mapTugasToSlug = [
                                'mutasi' => 'layanan-mutasi', 'kenaikan-pangkat' => 'layanan-kp',
                                'kgb' => 'layanan-kgb', 'relokasi' => 'layanan-relokasi',
                                'satya-lencana' => 'layanan-satya', 'hukuman-disiplin' => 'layanan-hukdis'
                            ];
                            $slugsDiizinkan = collect($kategoriTugas)->map(fn($k) => $mapTugasToSlug[$k] ?? null)->filter()->all();
                            $slugsDiizinkan[] = 'verifikasi-surat'; // Asumsi 'verifikasi-surat' selalu ada
                            
                            $filteredChildren = $menu->children->filter(fn($child) => in_array($child->slug, $slugsDiizinkan));
                            $menu->setRelation('children', $filteredChildren);
                        }
                    } else {
                        $menu->setRelation('children', collect([]));
                    }
                }

                foreach ($menu->children as $child) {
                    if ($child->badge_key && isset($notifData[$child->badge_key])) {
                        $child->badge_value = $notifData[$child->badge_key];
                    }
                }
            }
            $view->with('menus', $menus);
        });
    }
}