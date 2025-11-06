<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiGtk extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'absensi_gtk';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Mendefinisikan relasi "belongsTo" ke model Gtk.
     * Setiap record absensi dimiliki oleh satu GTK.
     */
    public function gtk(): BelongsTo
    {
        return $this->belongsTo(Gtk::class);
    }
}
