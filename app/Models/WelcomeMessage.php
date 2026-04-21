<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterRegional;

class WelcomeMessage extends Model
{
    use HasFactory, FilterRegional;

    protected $fillable = [
        'instansi_id',
        'title',
        'content',
        'image',
        'pimpinan_name',
        'pimpinan_role',
    ];
}
