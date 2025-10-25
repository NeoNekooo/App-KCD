<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\PaketKeahlian;

class PaketKeahlianController extends Controller
{
    public function index()
    {
        $paket = PaketKeahlian::all();
        return view('admin.akademik.paket-keahlian.index', compact('paket'));
    }
}
