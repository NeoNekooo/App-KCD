<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterRegional;

class VideoProfil extends Model
{
    use FilterRegional;

    protected $table = 'video_profils';
    protected $fillable = ['instansi_id', 'judul', 'url_youtube', 'deskripsi'];
}