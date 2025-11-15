<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotaPendaftaran extends Model
{
    use HasFactory;

    protected $table = 'quota_pendaftarans';

    protected $fillable = [
        'tahunPelajaran_id',
        'keahlian',
        'jumlah_kelas',
        'quota'
    ];

    public function tahunPpdb()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahunPelajaran_id');
    }
}
