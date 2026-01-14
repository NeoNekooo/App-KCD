<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ProfilSekolah;
use App\Models\KontakPpdb;
use App\Models\PengajuanSekolah; 
use App\Models\TugasPegawaiKcd; 

class AppServiceProvider extends ServiceProvider
{
    public function register(): void { }

    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Carbon::setLocale('id');

        if (Schema::hasTable('profil_sekolahs')) {
            try { view()->share('profilSekolah', ProfilSekolah::first()); } catch (\Exception $e) {}
        }
        if (Schema::hasTable('kontak_ppdbs')) {
            try { view()->share('kontakPpdb', KontakPpdb::first()); } catch (\Exception $e) {}
        }

        // HANYA LOGIKA BADGE NOTIFIKASI (Injeksi Menu sudah diurus Middleware)
        View::composer('*', function ($view) {
            $notif_data = [];
            $pegawaiTugasKategori = null; 

            if (Auth::check() && Schema::hasTable('pengajuan_sekolahs')) {
                $user = Auth::user();
                $targetStatus = [];

                if (strcasecmp($user->role, 'Pegawai') === 0 && $user->pegawai_kcd_id) {
                    $penugasan = TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)
                                                ->where('is_active', 1)->first();
                    if ($penugasan) $pegawaiTugasKategori = $penugasan->kategori_layanan;
                }

                if (strcasecmp($user->role, 'kasubag') === 0) $targetStatus = ['Verifikasi Kasubag'];
                elseif (strcasecmp($user->role, 'kepala') === 0) $targetStatus = ['Verifikasi Kepala'];
                else $targetStatus = ['Proses', 'Verifikasi Berkas'];

                $hitung = function ($kategoriDb) use ($targetStatus, $pegawaiTugasKategori, $user) {
                    if (strcasecmp($user->role, 'Pegawai') === 0 && $pegawaiTugasKategori) {
                        if (stripos($pegawaiTugasKategori, $kategoriDb) === false && stripos($kategoriDb, $pegawaiTugasKategori) === false) return 0; 
                    }
                    return PengajuanSekolah::where('kategori', 'LIKE', '%' . $kategoriDb . '%')
                                           ->whereIn('status', $targetStatus)->count();
                };

                $total = 0;
                $notif_data['notif_kp'] = $hitung('kenaikan pangkat'); $total += $notif_data['notif_kp'];
                $notif_data['notif_kgb'] = $hitung('kgb'); $total += $notif_data['notif_kgb'];
                $notif_data['notif_mutasi'] = $hitung('mutasi'); $total += $notif_data['notif_mutasi'];
                $notif_data['notif_relokasi'] = $hitung('relokasi'); $total += $notif_data['notif_relokasi'];
                $notif_data['notif_satya'] = $hitung('satya'); $total += $notif_data['notif_satya'];
                $notif_data['notif_hukdis'] = $hitung('hukuman'); $total += $notif_data['notif_hukdis'];
                $notif_data['total_layanan_gtk'] = $total > 0 ? $total : '';

                // Ubah 0 jadi string kosong biar rapi
                foreach($notif_data as $key => $val) if($val === 0) $notif_data[$key] = '';
            }
            $view->with('notif_data', $notif_data)->with('badges', $notif_data); 
        });
    }
}