<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah; // Sesuaikan dengan nama Model Sekolah Akang
use Illuminate\Http\Request;

class DataSpasialController extends Controller
{
    public function index()
    {
        // 1. Ambil data sekolah yang punya koordinat (Lintang & Bujur tidak null)
        // Kita select field yang diperlukan saja biar ringan
        $sekolahs = Sekolah::whereNotNull('lintang')
            ->whereNotNull('bujur')
            ->get([
                'id', 'nama', 'npsn', 'status_sekolah_str', 'bentuk_pendidikan_id_str', 
                'alamat_jalan', 'kabupaten_kota', 'kecamatan', 'lintang', 'bujur', 'logo'
            ]);

        // 2. Ambil Data Unik untuk Filter (Dropdown)
        $filter_kabupaten = Sekolah::distinct()->pluck('kabupaten_kota')->filter();
        $filter_kecamatan = Sekolah::distinct()->pluck('kecamatan')->filter();
        $filter_jenjang   = Sekolah::distinct()->pluck('bentuk_pendidikan_id_str')->filter();
        $filter_status    = Sekolah::distinct()->pluck('status_sekolah_str')->filter();

        return view('admin.dataspasial.index', compact(
            'sekolahs', 
            'filter_kabupaten', 
            'filter_kecamatan', 
            'filter_jenjang', 
            'filter_status'
        ));
    }
}