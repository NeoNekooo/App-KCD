<?php

namespace App\Models;

use App\Models\Sekolah;
use App\Exports\SiswaExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sekolah extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        // Ini kuncinya: Database simpan JSON <-> Laravel baca sebagai Array
        'social_media' => 'array',
    ];
    public function exportExcel(Request $request)
    {
        // 1. Cek apakah user memilih spesifik siswa (Export Selected)
        // Biasanya dikirim dalam format comma-separated string, misal: "1,5,10"
        $ids = null;
        if ($request->has('ids') && !empty($request->ids)) {
            $ids = explode(',', $request->ids);
        }

        // 2. Ambil Nama Sekolah dari Database untuk Nama File
        $sekolah = Sekolah::first();
        
        // Bersihkan nama sekolah agar aman untuk nama file (hilangkan spasi aneh/simbol)
        // Jika data sekolah kosong, default ke 'Sekolah'
        $namaSekolahClean = $sekolah ? \Illuminate\Support\Str::slug($sekolah->nama, '_') : 'Data_Siswa';
        
        // Format Nama File: NAMA_SEKOLAH_Data_Siswa_TANGGAL.xlsx
        // Contoh: SMK_NURUL_ISLAM_AFFANDIYAH_Data_Siswa_17-12-2025.xlsx
        $fileName = strtoupper($namaSekolahClean) . '_Data_Siswa_' . date('d-m-Y') . '.xlsx';
        
        // 3. Download Excel dengan parameter IDs
        return Excel::download(new SiswaExport($ids), $fileName);
    }
}