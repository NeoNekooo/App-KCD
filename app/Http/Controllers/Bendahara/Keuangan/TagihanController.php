<?php

namespace App\Http\Controllers\Bendahara\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Iuran;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Tapel; // Pastikan ini di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagihanController extends Controller
{
    /**
     * Menampilkan form untuk generate tagihan.
     */
    public function create()
    {
        $iurans = Iuran::orderBy('nama_iuran')->get();
        $rombels = Rombel::has('siswas')->orderBy('nama')->get();

        return view('bendahara.keuangan.tagihan.create', compact('iurans', 'rombels'));
    }

    /**
     * Menyimpan (Generate) tagihan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'iuran_id' => 'required|exists:iurans,id',
            'rombel_id' => 'nullable|array',
            // VALIDASI INI BERGANTUNG PADA PRIMARY KEY TABEL ROMBELS
            'rombel_id.*' => 'exists:rombels,id',
            'jumlah_tagihan' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
        ]);

        $iuran = Iuran::findOrFail($request->iuran_id);
        $jumlahTagihan = $request->jumlah_tagihan;

        // AMBIL TAPEL AKTIF
        $tapel = Tapel::where('is_active', true)->first();

        // 🛑 PENCEGAHAN (Sudah Anda tambahkan, tapi harus diulang untuk kejelasan)
        if (!$tapel) {
            return back()->with('error', 'Gagal: Tidak ditemukan Tahun Pelajaran aktif di sistem. Harap aktifkan satu Tahun Pelajaran terlebih dahulu.')->withInput();
        }

        $siswaQuery = Siswa::query();

        if (!empty($request->rombel_id)) {
            // PASTIKAN KOLOM DI TABEL SISWAS UNTUK ROMBEL ADALAH 'rombongan_belajar_id'
            $siswaQuery->whereIn('rombongan_belajar_id', $request->rombel_id);
        }

        $siswas = $siswaQuery->get();

        if ($siswas->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa aktif yang ditemukan berdasarkan filter Rombel.')->withInput();
        }

        DB::beginTransaction();
        $counter = 0;

        try {
            foreach ($siswas as $siswa) {
                $existingTagihan = Tagihan::where('siswa_id', $siswa->id)
                                          ->where('iuran_id', $iuran->id)
                                          ->where('status', '!=', 'Lunas')
                                          ->exists();

                if ($existingTagihan) {
                    continue;
                }

                // PERHATIKAN ARRAY CREATE INI: SEMUA KOLOM NOT NULL HARUS ADA
                Tagihan::create([
                    'siswa_id' => $siswa->id,
                    'iuran_id' => $iuran->id,

                    // ✅ PASTIKAN INI TERKIRIM
                    'tahun_pelajaran_id' => $tapel->id,

                    // Kolom yang harus diisi dari request
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'jumlah_tagihan' => $jumlahTagihan,

                    'sisa_tagihan' => $jumlahTagihan,
                    'status' => 'Belum Lunas',
                    'periode' => date('Ym'),
                ]);
                $counter++;
            }

            DB::commit();

            if ($counter > 0) {
                return redirect()->route('bendahara.keuangan.penerimaan.index')
                                 ->with('success', "Berhasil membuat $counter tagihan untuk iuran '{$iuran->nama_iuran}'.");
            } else {
                 return back()->with('warning', 'Tagihan sudah dibuat untuk semua siswa yang dipilih (tidak ada tagihan baru yang ditambahkan).')->withInput();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            // Tulis error detail ke log untuk debugging di masa depan
            Log::error('Error saat generate tagihan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat generate tagihan.')->withInput();
        }
    }
}
