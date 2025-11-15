<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gtk extends Model
{
    use HasFactory;

    /**
     * Tabel terkait dengan model.
     * Diasumsikan menggunakan tabel 'gtks' seperti pada dump database.
     *
     * @var string
     */
    protected $table = 'gtks';

    /**
     * Kunci utama tabel.
     *
     * @var string
     */
    protected $primaryKey = 'id'; // Menggunakan 'id' sesuai struktur tabel 'gtks'

    /**
     * Menonaktifkan timestamps karena tabel gtks biasanya tidak menggunakannya.
     *
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * Atribut yang dapat diisi secara massal.
     * Hanya memasukkan 'nama' dan 'ptk_id' untuk tujuan tampilan.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama', 
        'ptk_id', // Jika diperlukan NIP/PTK ID
    ];
}
