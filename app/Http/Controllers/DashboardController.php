<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Siswa;
use App\Models\Gtk;
use App\Models\Rombel;
use App\Models\Sekolah;


class DashboardController extends Controller
{
    public function index()
    {
        $totalSiswa = Siswa::aktif()->count();

        // Menghitung Guru/GTK
        $totalGuru  = Gtk::count();

        // Menghitung Kelas dari model Rombel
        $totalKelas = Rombel::count();

        // Menghitung jumlah Mata Pelajaran unik berdasarkan data 'pembelajaran' di tabel rombels
        $uniqueMapels = [];
        $rombels = Rombel::select('pembelajaran')->get();
        foreach ($rombels as $rombel) {
            $pembelajaran_data = json_decode($rombel->pembelajaran, true);
            if (is_array($pembelajaran_data)) {
                foreach ($pembelajaran_data as $pembelajaran) {
                    $mapel_id = $pembelajaran['mata_pelajaran_id'] ?? null;
                    $mapel_nama = $pembelajaran['mata_pelajaran_id_str'] ?? ($pembelajaran['nama_mata_pelajaran'] ?? null);

                    if ($mapel_id) {
                        $uniqueMapels[$mapel_id] = $mapel_nama ?? $mapel_id;
                    } elseif (!empty($mapel_nama)) {
                        // fallback jika tidak ada ID, gunakan nama sebagai key
                        $uniqueMapels[$mapel_nama] = $mapel_nama;
                    }
                }
            }
        }

        $totalMapel = count($uniqueMapels);

        // Ambil beberapa contoh nama mapel untuk tooltip (maks 8)
        $mapelSample = array_slice(array_values($uniqueMapels), 0, 8);

        $siswaPerTahun = Siswa::select(
                DB::raw('YEAR(tanggal_masuk_sekolah) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('tanggal_masuk_sekolah') // Hindari data kosong
            ->where('tanggal_masuk_sekolah', '>=', Carbon::now()->subYears(4)) // 4 tahun terakhir
            ->groupBy('year')
            ->orderBy('year', 'ASC')
            ->get();
$sekolah = Sekolah::firstOrCreate(['id' => 1]);
        // Siapkan array untuk Chart
        $chartCategories = $siswaPerTahun->pluck('year')->toArray();
        $chartData       = $siswaPerTahun->pluck('total')->toArray();

        // Fallback data dummy jika database masih kosong (agar dashboard tetap cantik)
        if (empty($chartCategories)) {
            $chartCategories = [date('Y')-3, date('Y')-2, date('Y')-1, date('Y')];
            $chartData       = [0, 0, 0, 0];
        }

        return view('admin.dashboard', compact(
            'totalSiswa',
            'totalGuru',
            'totalKelas',
            'totalMapel',
            'mapelSample',
            'chartCategories',
            'chartData',
            'sekolah'
        ));
    }
}
