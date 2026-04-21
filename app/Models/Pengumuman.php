<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\FilterRegional;

class Pengumuman extends Model
{
    use FilterRegional;

    protected $table = 'pengumumans';

    protected $fillable = [
        'instansi_id',
        'judul', 'slug', 'isi', 'lampiran', 'prioritas',
        'tanggal_terbit', 'tanggal_berakhir', 'status',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->judul) . '-' . Str::random(5);
            }
        });
    }
}
