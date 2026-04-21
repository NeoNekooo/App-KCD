<?php

namespace App\Models;

use App\Traits\FilterRegional;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory, FilterRegional;

    protected $table = 'sliders';

    protected $fillable = [
        'instansi_id',
        'image',
        'title',
        'subtitle',
        'order',
        'is_active',
    ];
}
