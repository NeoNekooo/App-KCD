<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

trait FilterRegional
{
    /**
     * Boot the trait to apply global scope.
     */
    protected static function bootFilterRegional()
    {
        static::addGlobalScope('regional', function (Builder $builder) {
            // 🔥 Mencegah Infinite Recursion:
            // Jika kita sedang mengambil data User/Pengguna DAN user tersebut belum ter-resolusi oleh Auth,
            // maka kita harus bypass filter ini agar tidak terjadi looping maut.
            $model = $builder->getModel();
            if (($model instanceof \App\Models\User || $model instanceof \App\Models\Pengguna) && !Auth::hasUser()) {
                return;
            }

            if (Auth::check()) {
                $user = Auth::user();
                $model = $builder->getModel();
                $role = strtolower($user->role ?? '');
                $instansiId = $user->instansi_id ?? ($user->pegawaiKcd->instansi_id ?? null);

                // 1. CEK APAKAH DIA SUPER ADMIN GLOBAL?
                // Syarat: Role 'administrator' DAN instansi_id di tabel users KOSONG.
                $isSuperAdmin = ($role === 'administrator' && (is_null($user->instansi_id) || $user->instansi_id == ''));

                if ($isSuperAdmin) {
                    return; // Super Admin bebas akses semua data (termasuk yang NULL)
                }

                // 2. UNTUK ADMIN WILAYAH & USER LAINNYA
                $instansiId = $user->instansi_id ?? ($user->pegawaiKcd->instansi_id ?? null);
                $tableName = $model->getTable();
                $column = ($model instanceof \App\Models\Instansi) ? 'id' : 'instansi_id';

                if ($instansiId) {
                    // WAJIB COCOK (Otomatis mengecualikan yang NULL)
                    $builder->whereRaw("CAST({$tableName}.{$column} AS CHAR) = ?", [(string)$instansiId]);
                } else {
                    // Jika data wilayah user tidak ditemukan, kunci datanya
                    $builder->whereRaw('1 = 0');
                }
            }
        });

        // Event saat membuat data baru (Otomatis inject instansi_id)
        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                $instansiId = $user->instansi_id ?? ($user->pegawaiKcd->instansi_id ?? null);
                
                // 🔥 Jika user punya wilayah (Admin Wilayah), pasang identitas wilayahnya secara otomatis
                if ($instansiId && empty($model->instansi_id)) {
                    $model->instansi_id = $instansiId;
                }
            }
        });
    }

    /**
     * Relasi ke Master Instansi
     */
    public function instansi()
    {
        return $this->belongsTo(\App\Models\Instansi::class, 'instansi_id');
    }
}
