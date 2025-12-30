<?php

namespace App\Http\Controllers\Admin\Kesiswaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Rombel;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function rekapSiswa(Request $request)
    {
        // Header/status counts
        $totalActive = DB::table('siswas')->where('status', 'Aktif')->count();
        $matchedCount = DB::table('siswas')
            ->join('rombels', 'siswas.rombongan_belajar_id', '=', 'rombels.rombongan_belajar_id')
            ->where('siswas.status', 'Aktif')
            ->count();
        $unmatchedCount = $totalActive - $matchedCount;

        // 1. AMBIL DATA DARI ROMBELS (prioritaskan jurusan_id_str jika tersedia)
        $query = DB::table('siswas')
            ->join('rombels', 'siswas.rombongan_belajar_id', '=', 'rombels.rombongan_belajar_id')
            ->select(
                DB::raw("COALESCE(rombels.jurusan_id_str, rombels.jurusan_id, 'UMUM') as jurusan"),
                DB::raw("COALESCE(rombels.tingkat_pendidikan_id_str, rombels.tingkat_pendidikan_id, '') as tingkat"),
                'siswas.jenis_kelamin',
                DB::raw('count(*) as total')
            )
            ->where('siswas.status', 'Aktif');

        // Filters
        if ($request->filled('tingkat')) {
            $t = strtoupper($request->input('tingkat'));
            $num = null;
            if ($t === 'X') $num = '10';
            elseif ($t === 'XI') $num = '11';
            elseif ($t === 'XII') $num = '12';

            if ($num) {
                $query->where(function ($q) use ($num) {
                    $q->where('rombels.tingkat_pendidikan_id_str', 'like', "%{$num}%")
                      ->orWhere('rombels.tingkat_pendidikan_id', 'like', "%{$num}%");
                });
            }
        }

        if ($request->filled('q')) {
            $qStr = $request->input('q');
            $query->whereRaw("COALESCE(rombels.jurusan_id_str, rombels.jurusan_id, 'UMUM') like ?", ["%{$qStr}%"]);
        }

        $data = $query
            ->groupBy(DB::raw("COALESCE(rombels.jurusan_id_str, rombels.jurusan_id, 'UMUM')"), DB::raw("COALESCE(rombels.tingkat_pendidikan_id_str, rombels.tingkat_pendidikan_id, '')"), 'siswas.jenis_kelamin')
            ->get();

        $rekap = [];

        // 2. PROSES DATA (gunakan nama jurusan sesuai yang ada di rombel)
        foreach ($data as $d) {
            $jurusanLabel = $d->jurusan ?? 'UMUM';

            // Konversi tingkat: tangani format seperti "Kelas 10", "10", "X", dsb.
            $tingkatRaw = strtoupper((string) $d->tingkat);
            if (preg_match('/\b(10|X)\b/i', $tingkatRaw)) {
                $tKey = 'X';
            } elseif (preg_match('/\b(11|XI)\b/i', $tingkatRaw)) {
                $tKey = 'XI';
            } elseif (preg_match('/\b(12|XII)\b/i', $tingkatRaw)) {
                $tKey = 'XII';
            } else {
                $tKey = 'LAIN';
            }

            if ($tKey === 'LAIN') continue;

            // Inisialisasi baris jurusan
            if (!isset($rekap[$jurusanLabel])) {
                $rekap[$jurusanLabel] = [
                    'nama' => $jurusanLabel,
                    'data' => [
                        'X'   => ['L' => 0, 'P' => 0],
                        'XI'  => ['L' => 0, 'P' => 0],
                        'XII' => ['L' => 0, 'P' => 0],
                    ],
                    'total_jurusan' => 0
                ];
            }

            $jk = (strtoupper(substr((string) $d->jenis_kelamin, 0, 1)) === 'L') ? 'L' : 'P';

            $rekap[$jurusanLabel]['data'][$tKey][$jk] += $d->total;
            $rekap[$jurusanLabel]['total_jurusan'] += $d->total;
        }

        // Urutkan jurusan secara alfabet agar konsisten
        ksort($rekap);


        // 4. HITUNG GRAND TOTAL
        $grandTotal = [
            'X'   => ['L' => 0, 'P' => 0, 'JML' => 0],
            'XI'  => ['L' => 0, 'P' => 0, 'JML' => 0],
            'XII' => ['L' => 0, 'P' => 0, 'JML' => 0],
            'ALL' => 0
        ];

        foreach ($rekap as $jurusan) {
            foreach (['X', 'XI', 'XII'] as $t) {
                $l = $jurusan['data'][$t]['L'];
                $p = $jurusan['data'][$t]['P'];
                $grandTotal[$t]['L'] += $l;
                $grandTotal[$t]['P'] += $p;
                $grandTotal[$t]['JML'] += ($l + $p);
            }
            $grandTotal['ALL'] += $jurusan['total_jurusan'];
        }

        return view('admin.laporan.rekap_siswa', compact('rekap', 'grandTotal', 'totalActive', 'matchedCount', 'unmatchedCount'));
    }
}
