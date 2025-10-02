<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TapelController extends Controller
{
    public function index()
    {
        // Ambil semester_id unik dari tabel rombels
        $tapel = DB::table('rombels')
            ->select('semester_id as tapel')
            ->whereNotNull('semester_id')
            ->distinct()
            ->orderByDesc('semester_id')
            ->get();

        return view('admin.akademik.tapel.index', compact('tapel'));
    }
}
