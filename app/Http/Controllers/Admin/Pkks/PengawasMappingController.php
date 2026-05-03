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
        // Ambil daftar pengawas (Filter: role mengandung kata 'pengawas')
        $pengawas = User::where(DB::raw('LOWER(role)'), 'LIKE', '%pengawas%')
            ->withCount('pengawasPembinas')
            ->orderBy('name', 'asc')
            ->get();

        // Ambil SEMUA sekolah (nanti kita sortir di View pake JS agar lebih dinamis)
        $sekolahs = Sekolah::orderBy('nama', 'asc')->get();
        
        // Ambil ID sekolah-sekolah yang sudah di-mapping (Global)
        $mappedSchoolIds = PengawasPembina::pluck('sekolah_id')->toArray();

        return view('admin.pkks.mapping_pengawas.index', compact('pengawas', 'sekolahs', 'mappedSchoolIds'));
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
