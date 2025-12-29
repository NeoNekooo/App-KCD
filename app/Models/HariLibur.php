<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HariLibur extends Model
{
    protected $table = 'hari_libur';
    protected $fillable = ['keterangan', 'tanggal_mulai', 'tanggal_selesai', 'tipe'];

    // Relasi ke Rombel
    public function rombels()
    {
        return $this->belongsToMany(Rombel::class, 'hari_libur_rombel', 'hari_libur_id', 'rombel_id');
    }

    // Accessor untuk menampilkan tanggal dengan rapi
    public function getPeriodeAttribute()
    {
        $start = Carbon::parse($this->tanggal_mulai);
        $end = Carbon::parse($this->tanggal_selesai);

        if ($start->equalTo($end)) {
            return $start->isoFormat('D MMMM Y');
        }
        return $start->isoFormat('D MMMM') . ' - ' . $end->isoFormat('D MMMM Y');
    }
}
