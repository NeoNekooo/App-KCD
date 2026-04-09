<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Berita extends Model
{
    protected $table = 'beritas';

    protected $fillable = [
        'judul', 'slug', 'ringkasan', 'isi', 'gambar', 'penulis', 'status', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
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