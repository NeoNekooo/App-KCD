<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;
    
    protected $table = 'jadwal_pelajaran'; 

    /**
     * [FIX] Memastikan semua kolom wajib ada
     */
    protected $fillable = [
        'rombel_id',
        'mata_pelajaran', 
        'ptk_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'tahun_ajaran_id', 
        'semester_id', // <-- PASTIKAN INI ADA
    ];

    public function rombel() { return $this->belongsTo(Rombel::class, 'rombel_id'); }
    public function ptk() { return $this->belongsTo(Gtk::class, 'ptk_id'); }
    public function tahunPelajaran() { return $this->belongsTo(Tapel::class, 'tahun_ajaran_id'); }
    


      public function gtk()
 {
      return $this->belongsTo(Gtk::class, 'ptk_id', 'ptk_id');
 }

}