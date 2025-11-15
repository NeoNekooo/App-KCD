<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JalurPendaftaran extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'tahunPelajaran_id', 
        'kode', 
        'jalur', 
        'keterangan', 
        'is_active'];

    public function tahunPpdb()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function syaratPendaftaran()
    {
        // relasi ke syarat berdasarkan kolom jalurPendaftaran_id
        return $this->hasMany(SyaratPendaftaran::class, 'jalurPendaftaran_id');
    }

    
}
