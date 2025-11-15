<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;

class EkstrakurikulerController extends Controller
{
    public function index()
    {
        // Data dummy sementara (belum dari database)
        $ekskul = [
            [
                'nama' => 'Pramuka',
                'alias' => 'Kepramukaan',
                'keterangan' => 'Kegiatan wajib setiap hari Jumat',
            ],
            [
                'nama' => 'Paskibra',
                'alias' => 'Pasukan Pengibar Bendera',
                'keterangan' => 'Ekskul baris-berbaris dan upacara',
            ],
            [
                'nama' => 'PMR',
                'alias' => 'Palang Merah Remaja',
                'keterangan' => 'Ekskul pertolongan pertama dan kesehatan',
            ],
        ];

        return view('admin.akademik.ekstrakurikuler.index', compact('ekskul'));
    }
}
