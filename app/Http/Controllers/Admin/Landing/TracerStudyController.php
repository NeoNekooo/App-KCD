<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\TracerStudy; // Pastikan Model TracerStudy/Tracer sudah ada
use Illuminate\Http\Request;

class TracerStudyController extends Controller
{
    public function index()
    {
        // Ambil data tracer + relasi siswa
        $tracers = TracerStudy::with('siswa')->latest()->get();
        return view('admin.landing.tracer.index', compact('tracers'));
    }
}