<?php

namespace App\Http\Controllers\Admin\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use App\Models\Pembelajaran;
use App\Models\JamPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\Sekolah;
use App\Models\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalPelajaranController extends Controller
{
    /**
     * Helper: Cari ID Rombel Induk & Saudara-saudaranya (Mapel Pilihan)
     */
    private function getRelatedRombelIds($mainRombelId)
    {
        $mainRombel = Rombel::find($mainRombelId);
        if (!$mainRombel) return [];

        // Bersihkan nama dari embel-embel "Pilihan"
        // "XII RPL 1 (Mapel Pilihan)" -> jadi "XII RPL 1"
        $keywords = ['(Mapel Pilihan)', 'Mapel Pilihan', ' Pilihan', '- Pilihan'];
        $baseName = trim(str_replace($keywords, '', $mainRombel->nama));

        // Cari semua rombel yang namanya mengandung Base Name
        return Rombel::where(function($query) use ($baseName) {
                $query->where('nama', '=', $baseName)
                      ->orWhere('nama', 'LIKE', $baseName . ' (%')
                      ->orWhere('nama', 'LIKE', $baseName . ' -%');
            })
            ->pluck('id')
            ->toArray();
    }

    public function index(Request $request)
    {
        $rombelId = $request->query('rombel_id');

        // [DROPDOWN BERSIH]
        // Hanya tampilkan kelas induk (Filter nama 'Pilihan')
        // Pakai unique() untuk membuang duplikat nama murni jika ada
        $rombels = Rombel::query()
            ->where('nama', 'NOT LIKE', '%Pilihan%')
            ->orderBy('nama')
            ->get()
            ->unique(fn($item) => trim($item->nama));

        $tapelAktif = Tapel::where('is_active', 1)->first();

        $data = [
            'rombels' => $rombels,
            'rombelId' => $rombelId,
            'tapelAktif' => $tapelAktif
        ];

        if ($rombelId && $tapelAktif) {

            // 1. Data Jadwal Existing
            $existingJadwal = JadwalPelajaran::where('rombel_id', $rombelId)
                ->where('tahun_ajaran_id', $tapelAktif->id)
                ->get();

            $mappedJadwal = [];
            $countPerMapel = [];

            foreach($existingJadwal as $jadwal) {
                if($jadwal->jam_pelajaran_id) {
                    $mappedJadwal[$jadwal->jam_pelajaran_id] = $jadwal->pembelajaran_id;
                }
                if($jadwal->pembelajaran_id) {
                    if(!isset($countPerMapel[$jadwal->pembelajaran_id])) {
                        $countPerMapel[$jadwal->pembelajaran_id] = 0;
                    }
                    $countPerMapel[$jadwal->pembelajaran_id]++;
                }
            }

            // 2. Data Panel Kiri
            // CUKUP ambil berdasarkan rombel_id yang dipilih user
            // Karena saat SYNC nanti, kita sudah menyatukan semua mapel ke ID ini.
            $pembelajarans = Pembelajaran::with('guru')
                ->where('rombel_id', $rombelId)
                ->orderBy('nama_mata_pelajaran')
                ->get();

            foreach($pembelajarans as $p) {
                $terpakai = $countPerMapel[$p->id] ?? 0;
                $p->terpakai = $terpakai;
                $max = $p->jam_mengajar_per_minggu ?? 0;
                $p->sisa = max(0, $max - $terpakai);
                $p->is_full = ($terpakai >= $max);
            }

            $data['pembelajarans'] = $pembelajarans;
            $data['existingJadwal'] = $mappedJadwal;

            // 3. Master Jam
            $allJam = JamPelajaran::orderBy('urutan')->get();
            $data['jamPelajarans'] = $allJam->groupBy('hari');
            $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            $data['days'] = collect($hariOrder)->filter(fn($day) => $data['jamPelajarans']->has($day))->values();
        }

        return view('admin.kurikulum.jadwal-pelajaran.index', $data);
    }

    // [SYNC GABUNGAN KE SATU ID]
    public function syncMapel(Request $request)
    {
        $mainRombelId = $request->rombel_id; // ID Kelas Induk (contoh: XII RPL 1)

        // 1. Cari semua saudara (XII RPL 1 & XII RPL 1 Pilihan)
        $allFamilyIds = $this->getRelatedRombelIds($mainRombelId);

        if (empty($allFamilyIds)) {
            return back()->with('error', 'Rombel tidak ditemukan.');
        }

        try {
            DB::beginTransaction();

            // 2. Hapus data pembelajaran lama HANYA di ID Induk
            // Karena kita akan menimpa ulang isinya dengan gabungan mapel
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Pembelajaran::where('rombel_id', $mainRombelId)->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // 3. Loop semua saudara untuk mengumpulkan mapel (Harvesting)
            $allMapelData = [];
            $now = now();

            foreach ($allFamilyIds as $sourceId) {
                $rombelSource = Rombel::find($sourceId);
                if(!$rombelSource) continue;

                $decoded = is_array($rombelSource->pembelajaran)
                           ? $rombelSource->pembelajaran
                           : json_decode($rombelSource->pembelajaran, true);

                if (is_array($decoded)) {
                    // Gabungkan semua ke dalam satu array besar
                    $allMapelData = array_merge($allMapelData, $decoded);
                }
            }

            // 4. Masukkan semua mapel ke database ATAS NAMA MAIN ID
            $batchData = [];
            foreach ($allMapelData as $item) {
                $batchData[] = [
                    'rombel_id' => $mainRombelId, // <--- KUNCI: Semua mapel dipaksa masuk ke ID Induk
                    'mata_pelajaran_id' => $item['mata_pelajaran_id'] ?? null,
                    'nama_mata_pelajaran' => $item['mata_pelajaran_id_str'] ?? ($item['nama_mata_pelajaran'] ?? 'Tanpa Nama'),
                    'ptk_id' => $item['ptk_id'] ?? null,
                    'jam_mengajar_per_minggu' => $item['jam_mengajar_per_minggu'] ?? 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (count($batchData) > 0) {
                Pembelajaran::insert($batchData);
            }

            DB::commit();
            return back()->with('success', 'Sync sukses! Mapel Wajib & Pilihan telah disatukan ke kelas ini.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Sync Mapel Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal sync: ' . $e->getMessage());
        }
    }

    public function updateJadwal(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required',
            'jam_pelajaran_id' => 'required',
            'pembelajaran_id' => 'nullable',
        ]);

        $tapelAktif = Tapel::where('is_active', 1)->first();

        try {
            // [LOGIC CEK BENTROK GURU]
            // Hanya jalankan jika user sedang MENARUH mapel (bukan menghapus)
            if ($request->pembelajaran_id) {

                // 1. Ambil data guru dari mapel yang sedang di-drag
                $mapelBaru = Pembelajaran::with('guru')->find($request->pembelajaran_id);

                // Pastikan mapel ada dan GURUNYA ADA (kalau mapel kosong/belum ada guru, skip cek)
                if ($mapelBaru && $mapelBaru->ptk_id) {

                    // 2. Cek apakah Guru ini sibuk di kelas lain pada jam & tahun ajaran yang sama?
                    $guruSibuk = JadwalPelajaran::query()
                        ->where('tahun_ajaran_id', $tapelAktif->id)
                        ->where('jam_pelajaran_id', $request->jam_pelajaran_id) // Jam yang sama
                        ->where('rombel_id', '!=', $request->rombel_id) // Di KELAS LAIN (bukan kelas ini)
                        ->whereHas('pembelajaran', function($query) use ($mapelBaru) {
                            $query->where('ptk_id', $mapelBaru->ptk_id); // Guru yang sama
                        })
                        ->with(['rombel', 'pembelajaran.guru']) // Load relasi untuk pesan error
                        ->first();

                    // 3. Jika ketemu, tolak simpan!
                    if ($guruSibuk) {
                        $namaGuru = $mapelBaru->guru->nama ?? 'Guru';
                        $kelasBentrok = $guruSibuk->rombel->nama ?? 'Kelas Lain';

                        return response()->json([
                            'status' => 'error',
                            'message' => "BENTROK: {$namaGuru} sudah mengajar di kelas {$kelasBentrok} pada jam ini."
                        ]);
                    }
                }
            }

            // --- Jika Lolos Cek Bentrok, Lanjut Simpan ---

            // Logic Simpan (Sama seperti sebelumnya)
            $jadwal = JadwalPelajaran::where('rombel_id', $request->rombel_id)
                ->where('tahun_ajaran_id', $tapelAktif->id)
                ->where('jam_pelajaran_id', $request->jam_pelajaran_id)
                ->first();

            if ($request->pembelajaran_id) {
                if ($jadwal) {
                    $jadwal->update(['pembelajaran_id' => $request->pembelajaran_id]);
                } else {
                    JadwalPelajaran::create([
                        'tahun_ajaran_id' => $tapelAktif->id,
                        'rombel_id' => $request->rombel_id,
                        'jam_pelajaran_id' => $request->jam_pelajaran_id,
                        'pembelajaran_id' => $request->pembelajaran_id,
                        'semester_id' => 1,
                    ]);
                }
            } else {
                if ($jadwal) $jadwal->delete();
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Helper untuk menyingkat Nama Mapel
     * Contoh: "Pendidikan Agama Islam" -> "PAI"
     * Contoh: "Matematika" -> "MTK" (Manual/Custom)
     */
    private function getSingkatan($nama)
    {
        // 1. Cek singkatan manual/umum (Bisa ditambah sendiri)
        $manual = [
            'Matematika' => 'MTK',
            'Bahasa Indonesia' => 'BIN',
            'Bahasa Inggris' => 'BIG',
            'Pendidikan Agama Islam' => 'PAI',
            'Pendidikan Kewarganegaraan' => 'PKN',
            'Sejarah Indonesia' => 'SEJ',
        ];

        // Cek jika ada di daftar manual, atau jika mengandung kata kunci
        foreach ($manual as $key => $val) {
            if (stripos($nama, $key) !== false) return $val;
        }

        // 2. Jika tidak ada, ambil huruf depan setiap kata kapital
        // "Pengembangan Perangkat Lunak" -> "PPL"
        $words = explode(' ', $nama);
        $acronym = '';
        foreach ($words as $w) {
            if(!empty($w)) $acronym .= strtoupper($w[0]);
        }

        // Jika hasil singkatan > 4 huruf, ambil 3 huruf pertama saja biar rapi
        return substr($acronym, 0, 4);
    }

    // --- FITUR LIHAT REKAP JADWAL (VIEW & PDF) ---

    public function rekap(Request $request)
{
    $selectedRombels = $request->rombel_ids ?? [];
    $tapelAktif = Tapel::where('is_active', 1)->first();

    $rombels = collect();
    $jadwalGrouped = [];
    $listGuru = collect();

    if (!empty($selectedRombels)) {
        // ... (Logic pengambilan data sama seperti sebelumnya) ...
        $rombels = Rombel::with('waliKelas')
            ->whereIn('id', $selectedRombels)
            ->orderBy('nama')
            ->get();

        $jadwals = JadwalPelajaran::with(['pembelajaran.guru', 'jamPelajaran'])
            ->whereIn('rombel_id', $selectedRombels)
            ->when($tapelAktif, function($q) use ($tapelAktif) {
                $q->where('tahun_ajaran_id', $tapelAktif->id);
            })
            ->get();

        foreach ($jadwals as $j) {
            if ($j->jamPelajaran) {
                $jadwalGrouped[$j->rombel_id][$j->jamPelajaran->hari][$j->jamPelajaran->urutan] = $j;
                if ($j->pembelajaran && $j->pembelajaran->guru) {
                    $listGuru->push($j->pembelajaran->guru);
                }
            }
        }
    }

    $listGuru = $listGuru->unique('id')->sortBy('nama');

    // LOGIC FILTER ROMBEL GROUPS (Sama seperti sebelumnya)
    $rawRombels = Rombel::where('nama', 'NOT LIKE', '%Pilihan%')
        ->orderBy('nama')->get()->unique(fn($i) => trim($i->nama));

    $kelasX = $rawRombels->filter(fn($r) => str_starts_with($r->nama, 'X ') || str_starts_with($r->nama, 'X-') || str_starts_with($r->nama, '10 '));
    $kelasXI = $rawRombels->filter(fn($r) => str_starts_with($r->nama, 'XI ') || str_starts_with($r->nama, 'XI-') || str_starts_with($r->nama, '11 '));
    $kelasXII = $rawRombels->filter(fn($r) => str_starts_with($r->nama, 'XII ') || str_starts_with($r->nama, 'XII-') || str_starts_with($r->nama, '12 '));

    $mappedIds = $kelasX->pluck('id')->merge($kelasXI->pluck('id'))->merge($kelasXII->pluck('id'))->toArray();
    $kelasLain = $rawRombels->whereNotIn('id', $mappedIds);

    $rombelGroups = array_filter([
        'Tingkat X' => $kelasX, 'Tingkat XI' => $kelasXI,
        'Tingkat XII' => $kelasXII, 'Lainnya' => $kelasLain
    ], fn($g) => $g->isNotEmpty());

    $masterJam = JamPelajaran::where('hari', 'Senin')->orderBy('urutan')->get();

    $data = [
        'rombelGroups'    => $rombelGroups,
        'selectedRombels' => $selectedRombels,
        'rombels'         => $rombels,
        'jadwalGrouped'   => $jadwalGrouped,
        'masterJam'       => $masterJam,
        'listGuru'        => $listGuru,
        'days'            => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
        'controller'      => $this,
        'tapelAktif'      => $tapelAktif
    ];

    // --- UPDATE PENTING UNTUK AJAX ---
    if ($request->ajax()) {
        // Jika request dari AJAX JS, kembalikan hanya file partial _table_result
        return view('admin.kurikulum.jadwal-pelajaran._table_result', $data);
    }

    // Jika request biasa (refresh browser), kembalikan full page rekap
    return view('admin.kurikulum.jadwal-pelajaran.rekap', $data);
}

    public function cetakPdf(Request $request)
    {
        $selectedRombels = $request->rombel_ids ?? [];
        if(empty($selectedRombels)) {
            return back()->with('error', 'Pilih minimal satu kelas.');
        }

        $sekolah = Sekolah::first();
        $tapelAktif = Tapel::where('is_active', 1)->first();
        if(!$tapelAktif) return back()->with('error', 'Tahun ajaran aktif tidak ditemukan.');

        $rombels = Rombel::with('waliKelas')
            ->whereIn('id', $selectedRombels)
            ->orderBy('nama')
            ->get();

        $jadwals = JadwalPelajaran::with(['pembelajaran.guru', 'jamPelajaran'])
            ->whereIn('rombel_id', $selectedRombels)
            ->where('tahun_ajaran_id', $tapelAktif->id)
            ->get();

        $jadwalGrouped = [];
        $listGuru = collect();

        foreach ($jadwals as $j) {
            if ($j->jamPelajaran) {
                $jadwalGrouped[$j->rombel_id][$j->jamPelajaran->hari][$j->jamPelajaran->urutan] = $j;
                if ($j->pembelajaran && $j->pembelajaran->guru) {
                    $listGuru->push($j->pembelajaran->guru);
                }
            }
        }

        $listGuru = $listGuru->unique('id')->sortBy('nama')->values();

        // [PERBAIKAN DISINI]
        // Ambil SEMUA jam pelajaran, urutkan field hari agar Senin -> Minggu, lalu urutan jam
        $rawJams = JamPelajaran::orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->orderBy('urutan')
            ->get();

        // Grouping per hari agar di View bisa dipanggil spesifik per hari
        $allMasterJams = $rawJams->groupBy('hari');

        // Pastikan urutan hari sesuai keinginan
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        // Filter hari yang benar-benar punya jam pelajaran
        $activeDays = collect($days)->filter(function($day) use ($allMasterJams) {
            return isset($allMasterJams[$day]);
        });

        $pdf = \PDF::loadView('admin.kurikulum.jadwal-pelajaran.pdf', [
            'sekolah'       => $sekolah,
            'rombels'       => $rombels,
            'jadwalGrouped' => $jadwalGrouped,
            'allMasterJams' => $allMasterJams, // <--- Kirim variable baru ini
            'listGuru'      => $listGuru,
            'days'          => $activeDays,
            'tapelAktif'    => $tapelAktif,
            'controller'    => $this
        ]);

        return $pdf->setPaper('a4', 'landscape')->stream('Jadwal-Pelajaran.pdf');
    }

    // Agar helper bisa dipanggil di View blade: {{ $controller->getSingkatan(...) }}
    // Kita buat public sementara atau pakai helper Laravel, tapi cara ini paling cepat:
    public function helperSingkatan($nama) {
        return $this->getSingkatan($nama);
    }
}
