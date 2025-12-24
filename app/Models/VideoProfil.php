<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoProfil extends Model
{
    use HasFactory;

    protected $table = 'video_profils';
    protected $fillable = ['judul', 'url_youtube', 'deskripsi'];
}