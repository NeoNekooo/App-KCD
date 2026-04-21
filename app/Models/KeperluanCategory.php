<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterRegional;

class KeperluanCategory extends Model
{
    use FilterRegional;

    protected $fillable = ['instansi_id', 'name'];
}
