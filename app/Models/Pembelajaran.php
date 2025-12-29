<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pembelajaran extends Model
{
    protected $guarded = [];

    // Relasi ke Guru (GTK)
    // Asumsi: Di tabel 'gtks' ada kolom 'ptk_id' yang menyimpan UUID
    public function guru()
    {
        return $this->belongsTo(Gtk::class, 'ptk_id', 'ptk_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }
}
