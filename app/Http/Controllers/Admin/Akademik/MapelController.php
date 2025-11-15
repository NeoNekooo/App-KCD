<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator; // Digunakan untuk paginasi array manual

/**
 * Controller untuk mengelola data Mata Pelajaran (Mapel) dengan Search dan Pagination.
 * Lokasi: app/Http/Controllers/Admin/Akademik/MapelController.php
 */
class MapelController extends Controller
{
    /**
     * Mengambil dan menampilkan daftar unik Mata Pelajaran
     * dari kolom JSON 'pembelajaran' di tabel 'rombels', dengan paginasi dan search.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Ambil kolom 'pembelajaran' dari semua rombel
        $rombels = DB::table('rombels')
            ->select('pembelajaran')
            ->get();
            
        $uniqueMapels = [];
            
        // 2. Iterasi dan ekstrak Mata Pelajaran (Mapel) yang unik
        foreach ($rombels as $rombel) {
            $pembelajaran_data = json_decode($rombel->pembelajaran, true);
            
            if (is_array($pembelajaran_data)) {
                foreach ($pembelajaran_data as $pembelajaran) {
                    $mapel_id = $pembelajaran['mata_pelajaran_id'] ?? null;
                    $mapel_nama = $pembelajaran['mata_pelajaran_id_str'] ?? 'N/A';

                    if ($mapel_id && $mapel_nama !== 'N/A') {
                        // Gunakan ID sebagai key untuk memastikan Mata Pelajaran unik
                        if (!isset($uniqueMapels[$mapel_id])) {
                            $uniqueMapels[$mapel_id] = [
                                'kode' => $mapel_id,
                                'nama_mapel' => $mapel_nama,
                            ];
                        }
                    }
                }
            }
        }

        // Konversi array asosiatif (dengan key ID) menjadi array biasa
        $daftarMapel = array_values($uniqueMapels);

        // 3. Implementasi Search (Filter)
        $searchQuery = $request->input('search');
        if ($searchQuery) {
            $daftarMapel = array_filter($daftarMapel, function ($mapel) use ($searchQuery) {
                // Cari di kolom nama_mapel atau kode (case-insensitive)
                return stripos($mapel['nama_mapel'], $searchQuery) !== false || 
                       stripos($mapel['kode'], $searchQuery) !== false;
            });
            // Re-index array setelah difilter
            $daftarMapel = array_values($daftarMapel);
        }

        // 4. Implementasi Pagination Manual
        $totalItems = count($daftarMapel);
        $perPage = 15; // Tentukan jumlah item per halaman
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        
        // Ambil item yang sesuai untuk halaman saat ini
        $currentItems = array_slice($daftarMapel, ($currentPage - 1) * $perPage, $perPage);

        // Buat objek Paginator
        $mapelsPaginator = new LengthAwarePaginator(
            $currentItems,
            $totalItems,
            $perPage,
            $currentPage,
            // Opsional: untuk mempertahankan query string (search) saat navigasi
            ['path' => $request->url(), 'query' => $request->query()] 
        );

        // Kirim objek Paginator ke view
        return view('admin.akademik.mapels.index', [
            'mapels' => $mapelsPaginator,
            'searchQuery' => $searchQuery
        ]);
    }
}