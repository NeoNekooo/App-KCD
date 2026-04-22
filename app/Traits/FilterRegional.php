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
                $model = $builder->getModel();
                $role = strtolower($user->role ?? '');
                $instansiId = $user->instansi_id ?? ($user->pegawaiKcd->instansi_id ?? null);

                // Admin Pusat / Administrator hanya bypass filter jika kaitan instansi_id nya KOSONG
                if (in_array($role, ['admin', 'administrator']) && (is_null($instansiId) || $instansiId == '')) {
                    return;
                }

                if ($instansiId) {
                    $tableName = $model->getTable();
                    
                    // Tentukan kolom filter
                    $column = ($model instanceof \App\Models\Instansi) ? 'id' : 'instansi_id';
                    
                    // Langsung tempel filter tanpa banyak tanya Schema (Lebih agresif)
                    $builder->where($tableName . '.' . $column, '=', $instansiId);
                } else {
                    // Jika user biasa tapi tidak punya instansi_id, kunci datanya
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
                $role = strtolower($user->role ?? '');
                
                if (!in_array($role, ['admin', 'administrator'])) {
                    $instansiId = $user->instansi_id ?? ($user->pegawaiKcd->instansi_id ?? null);
                    if ($instansiId) {
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
