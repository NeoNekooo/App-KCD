<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ekstrakurikuler extends Model {
    public function pembina() {
        return $this->belongsTo(Ptk::class, 'pembina_id');
    }
}
