<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait FilterRegional
{
    /**
     * Boot the trait to apply global scope.
     */
    protected static function bootFilterRegional()
    {
        static::addGlobalScope('regional', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();

                // Admin Pusat / Super Admin dapat melihat seluruh data secara global
                $role = strtolower($user->role ?? '');
                if ($role === 'administrator' || $role === 'admin') {
                    return;
                }

                // Ambil instansi_id dari User langsung atau dari profil pegawai_kcd
                $instansiId = $user->instansi_id ?? ($user->pegawaiKcd->instansi_id ?? null);

                if ($instansiId) {
                    $model = $builder->getModel();
                    $tableName = $model->getTable();
                    
                    // Tentukan kolom filter
                    $column = 'instansi_id';
                    
                    // Case khusus untuk model Instansi: filter berdasarkan 'id'
                    if ($model instanceof \App\Models\Instansi) {
                        $column = 'id';
                    } 
                    // Atau jika model mendefinisikan kolom regional secara custom
                    elseif (property_exists($model, 'regionalColumn')) {
                        $column = $model->regionalColumn;
                    }

                    // 1. Jika tabel memiliki kolom yang ditentukan (id / instansi_id)
                    if (Schema::hasColumn($tableName, $column)) {
                        // Paksa perbandingan sebagai string untuk menghindari mismatch tipe data (int vs string)
                        $builder->where($tableName . '.' . $column, (string)$instansiId);
                    } 
                    // 2. Jika tidak ada kolom instansi_id namun memiliki relasi sekolah, saring via relasi
                    elseif (method_exists($model, 'sekolah')) {
                        $builder->whereHas('sekolah', function ($q) use ($instansiId) {
                            $q->where('instansi_id', (string)$instansiId);
                        });
                    }
                } else {
                    // Jika user tidak terikat ke instansi manapun, jangan tampilkan data (Kecuali Admin Global)
                    if (!in_array($role, ['admin', 'administrator'])) {
                        $builder->whereRaw('1 = 0');
                    }
                }
            }
        });

        // Event saat membuat data baru (Otomatis inject instansi_id)
        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                
                // Super Admin / Administrator tidak di-inject otomatis
                if ($user->role !== 'administrator') {
                    $instansiId = $user->instansi_id ?? ($user->pegawaiKcd->instansi_id ?? null);
                    
                    // Isi instansi_id jika tabel tujuan memilikinya
                    if ($instansiId && Schema::hasColumn($model->getTable(), 'instansi_id')) {
                        $model->instansi_id = $instansiId;
                    }
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
