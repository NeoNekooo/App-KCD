<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterRegional;

class NomorSuratSetting extends Model
{
    use FilterRegional;

    protected $table = 'nomor_surat_settings';
    protected $guarded = ['id'];
}
