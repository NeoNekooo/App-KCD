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
        return $this->hasMany(SyaratPendaftaran::class, 'tahunPelajaran_id');
    }
}
