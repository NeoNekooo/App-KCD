<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gtk extends Model
{
    use HasFactory;

<<<<<<< HEAD
        protected $table = 'gtks'; // Menentukan nama tabel secara eksplisit
        protected $guarded = [];
    }
=======
    protected $guarded = [];

    public function riwayatTugas()
{
   return $this->hasMany(TugasPegawai::class, 'pegawai_id', 'ptk_id');
}
}
>>>>>>> origin/modul/kepegawaian
