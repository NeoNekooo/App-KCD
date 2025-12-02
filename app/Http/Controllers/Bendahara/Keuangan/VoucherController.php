<?php

namespace App\Http\Controllers\Bendahara\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Tapel;
use App\Models\Tunggakan;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
    /**
     * Menampilkan halaman ringkasan voucher dan form tambah.
     */
    public function index()
    {
        // Data untuk tabel ringkasan
        $siswas = Siswa::with('vouchers', 'tagihans', 'tunggakans', 'pembayarans')
            ->where('status', 'Aktif')
            ->get();

        // Data untuk form tambah voucher
        $daftarSiswa = Siswa::where('status', 'Aktif')->orderBy('nama')->get();
        $tahunAjarans = Tapel::where('is_active', 1)->get();

        return view('bendahara.keuangan.voucher.index', compact('siswas', 'daftarSiswa', 'tahunAjarans'));
    }

    /**
     * Menyimpan voucher baru untuk siswa dan mengaplikasikannya ke tunggakan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tahun_pelajaran_id' => 'required|exists:tapel,id',
            'nilai_voucher' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat record voucher
            $voucher = Voucher::create($request->all());
            $sisaVoucher = $voucher->nilai_voucher;

            // 2. Ambil semua tunggakan 'Bebas' yang belum lunas
            $tunggakans = Tunggakan::where('siswa_id', $request->siswa_id)
                ->where('sisa_tunggakan', '>', 0)
                ->orderBy('id') // Proses dari tunggakan terlama
                ->get();

            // 3. Aplikasikan voucher ke setiap tunggakan
            foreach ($tunggakans as $tunggakan) {
                if ($sisaVoucher <= 0) break; // Hentikan jika nilai voucher sudah habis

                $potongan = min($sisaVoucher, $tunggakan->sisa_tunggakan);

                $tunggakan->sisa_tunggakan -= $potongan;
                if ($tunggakan->sisa_tunggakan <= 0) {
                    $tunggakan->status = 'Lunas';
                }
                $tunggakan->save();

                $sisaVoucher -= $potongan;
            }

            DB::commit();
            return back()->with('success', 'Voucher berhasil ditambahkan dan diaplikasikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan voucher: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat menyimpan voucher.')->withInput();
        }
    }

    /**
     * Menghapus voucher dan mengembalikan nilai potongan ke tunggakan.
     * PENTING: Fungsi update sengaja tidak dibuat karena logikanya sangat kompleks
     * (harus membatalkan dan mengaplikasikan ulang). Lebih aman untuk hapus dan buat baru.
     */
    public function destroy(Voucher $voucher)
    {
        DB::beginTransaction();
        try {
            $nilaiVoucher = $voucher->nilai_voucher;

            // Logika terbalik: Kembalikan nilai voucher ke tunggakan yang sudah lunas/berkurang
            $tunggakans = Tunggakan::where('siswa_id', $voucher->siswa_id)
                ->where(function ($query) use ($voucher) {
                    // Cari tunggakan yang mungkin telah dipotong oleh voucher ini
                    // Logika ini bisa disempurnakan jika ada tabel pivot antara voucher & tunggakan
                    $query->where('status', 'Lunas')
                          ->orWhere('sisa_tunggakan', '>', 0);
                })
                ->orderByDesc('id') // Prioritaskan pengembalian ke tunggakan terbaru
                ->get();

            foreach ($tunggakans as $tunggakan) {
                if ($nilaiVoucher <= 0) break;

                // Hitung berapa maksimal yang bisa dikembalikan ke tunggakan ini
                $nilaiBisaKembali = $tunggakan->total_tunggakan_awal - $tunggakan->sisa_tunggakan;
                $pengembalian = min($nilaiVoucher, $nilaiBisaKembali);

                $tunggakan->sisa_tunggakan += $pengembalian;
                if ($tunggakan->sisa_tunggakan > 0) {
                    $tunggakan->status = 'Belum Lunas';
                }
                $tunggakan->save();

                $nilaiVoucher -= $pengembalian;
            }

            // Hapus record voucher
            $voucher->delete();

            DB::commit();
            return back()->with('success', 'Voucher berhasil dihapus dan tunggakan dikembalikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus voucher: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat menghapus voucher.');
        }
    }
}
