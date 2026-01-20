<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pengajuan extends Model
{
    protected $guarded = ['id'];

    // Auto UUID
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function sekolah() {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function gtk() {
        return $this->belongsTo(Gtk::class, 'gtk_id');
    }

    protected $casts = [
    'dokumen_syarat' => 'array', // <--- Tambahin baris ini
];
} 