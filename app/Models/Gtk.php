<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Gtk extends Model
    {
        use HasFactory;

        protected $table = 'gtks'; // Menentukan nama tabel secara eksplisit
        protected $guarded = [];
    }