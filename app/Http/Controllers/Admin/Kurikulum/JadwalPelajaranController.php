<?php

namespace App\Http\Controllers\Admin\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use App\Models\Pembelajaran;
use App\Models\JamPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
}
