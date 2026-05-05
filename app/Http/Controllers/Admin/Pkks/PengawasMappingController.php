<?php

namespace App\Http\Controllers\Admin\Pkks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\PengawasPembina;
use Illuminate\Support\Facades\DB;

class PengawasMappingController extends Controller
{
    /**
     * Tampilkan halaman mapping pengawas
     */
    public function index()
    {
        // Ambil daftar pengawas
        $pengawas = User::where(DB::raw('LOWER(role)'), 'LIKE', '%pengawas%')
            ->with(['pengawasPembinas.sekolah'])
            ->withCount('pengawasPembinas')
            ->orderBy('name', 'asc')
            ->get();

        // 🔥 PRE-CALCULATE JENJANG: Jika profil jenjang kosong, ambil dari sekolah binaan pertama
        foreach ($pengawas as $p) {
            if (!$p->jenjang) {
                $firstMapping = $p->pengawasPembinas->first();
                if ($firstMapping && $firstMapping->sekolah) {
                    $p->jenjang = $firstMapping->sekolah->bentuk_pendidikan_id_str;
                }
            }
        }

        // Ambil daftar Jenjang yang ada di sekolah
        $jenjangs = Sekolah::select('bentuk_pendidikan_id_str as nama')
            ->whereNotNull('bentuk_pendidikan_id_str')
            ->groupBy('bentuk_pendidikan_id_str')
            ->orderBy('bentuk_pendidikan_id_str', 'asc')
            ->get();

        // Ambil SEMUA sekolah dengan info pengawasnya (kalau ada)
        $sekolahs = Sekolah::with(['pengawasPembina.pengawas'])->orderBy('nama', 'asc')->get();
        
        // Buat mapping: sekolah_id => pengawas_id (untuk mempermudah di JS)
        $mapping = PengawasPembina::pluck('pengawas_id', 'sekolah_id')->toArray();

        return view('admin.pkks.mapping_pengawas.index', compact('pengawas', 'sekolahs', 'jenjangs', 'mapping'));
    }

    /**
     * Ambil mapping semua sekolah (AJAX)
     */
    public function getFullMapping()
    {
        $mappings = PengawasPembina::with('pengawas:id,name')->get();
        return response()->json($mappings);
    }

    /**
     * Ambil mapping dan list sekolah yang tersedia (AJAX)
     */
    public function getMapping($pengawasId)
    {
        // 1. Sekolah yang dipegang pengawas ini
        $mySchools = PengawasPembina::where('pengawas_id', $pengawasId)
            ->pluck('sekolah_id')
            ->toArray();

        // 2. Sekolah yang dipegang pengawas LAIN
        $otherSchools = PengawasPembina::where('pengawas_id', '!=', $pengawasId)
            ->pluck('sekolah_id')
            ->toArray();

        return response()->json([
            'my_schools' => $mySchools,
            'other_schools' => $otherSchools
        ]);
    }

    /**
     * Tampilkan daftar pengawas yang sudah dimapping (Rekapitulasi)
     */
    public function list()
    {
        // Ambil pengawas yang punya minimal 1 sekolah binaan
        $pembina = User::whereHas('pengawasPembinas')
            ->with(['pengawasPembinas.sekolah'])
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.pkks.mapping_pengawas.list', compact('pembina'));
    }

    /**
     * Simpan pemetaan pengawas ke sekolah
     */
    public function update(Request $request)
    {
        // 🔥 LOG MATA-MATA
        \Log::info("PKKS_MAPPING_UPDATE_START", $request->all());

        try {
            $request->validate([
                'pengawas_id' => 'required|exists:users,id',
                'sekolah_ids' => 'nullable|array',
            ]);

            DB::beginTransaction();

            // Hapus mapping lama
            PengawasPembina::where('pengawas_id', $request->pengawas_id)->delete();

            // Simpan mapping baru
            if (!empty($request->sekolah_ids)) {
                $data = [];
                foreach ($request->sekolah_ids as $sid) {
                    $data[] = [
                        'pengawas_id' => $request->pengawas_id,
                        'sekolah_id'  => $sid,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
                // Pake DB::table langsung biar lebih cepet dan pasti
                DB::table('pengawas_pembinas')->insert($data);
            }

            // 1. Update Jenjang di tabel Users (Otomatis jadi Pengawas jenjang tersebut)
            if ($request->jenjang) {
                User::where('id', $request->pengawas_id)->update(['jenjang' => $request->jenjang]);
            }

            DB::commit();

            \Log::info("PKKS_MAPPING_UPDATE_SUCCESS", ['pengawas_id' => $request->pengawas_id]);

            return response()->json([
                'success' => true,
                'message' => 'Pemetaan Pengawas Pembina berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("PKKS_MAPPING_UPDATE_ERROR: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }
}
