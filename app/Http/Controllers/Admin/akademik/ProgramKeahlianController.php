<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\ProgramKeahlian;
use Illuminate\Http\Request;

class ProgramKeahlianController extends Controller
{
    public function index()
    {
        $programs = ProgramKeahlian::orderBy('kode')->get();

        return view('admin.akademik.program-keahlian.index', compact('programs'));
    }
}
