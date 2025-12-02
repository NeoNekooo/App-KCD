<?php

namespace App\Http\Controllers\Bendahara\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\KasMutasi;
use App\Models\MasterKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan ini

class KasController extends Controller
{
    /**
     * Menampilkan halaman buku kas (ledger).
     */
    public function index(Request $request)
    {
        // 1. Ambil semua kas yang aktif untuk dropdown filter
        $daftarKas = MasterKas::where('is_active', true)->get();

        // 2. Tentukan Kas yang dipilih: dari request atau ambil kas pertama
        $kasDipilih = $request->filled('kas_id')
            ? $daftarKas->firstWhere('id', $request->kas_id)
            : ($daftarKas->isNotEmpty() ? $daftarKas->first() : null); // Jika kas_id kosong, ambil kas pertama

        $mutasi = collect();
        $saldoAwal = 0;

        // HANYA jalankan query jika ada kas yang ditemukan/dipilih
        if ($kasDipilih) {
            // **PENTING: Mutasi Awal selalu diambil TANPA filter tipe mutasi (debit/kredit)
            // agar perhitungan saldo berjalan tetap akurat.**
            $queryAwal = KasMutasi::where('master_kas_id', $kasDipilih->id);

            // Logika filter berdasarkan bulan
            if ($request->filled('bulan')) {
                $bulan = substr($request->bulan, 5, 2);
                $tahun = substr($request->bulan, 0, 4);

                // Hitung Saldo Awal: total debit - total kredit dari semua transaksi SEBELUM bulan yang dipilih
                $saldoAwal = (clone $queryAwal) // Clone query agar filter bulan tidak mempengaruhi saldo awal
                    ->where('tanggal', '<', "$tahun-$bulan-01")
                    ->sum(DB::raw('debit - kredit'));

                $queryAwal->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
            } else {
                 // Jika tidak ada filter bulan, saldo awal adalah 0 dan ambil semua transaksi
                 $saldoAwal = 0;
            }

            // Ambil semua mutasi untuk periode yang sudah difilter (bulan/tahun)
            $mutasiPeriode = $queryAwal->orderBy('tanggal')->orderBy('id')->get();

            // Logika untuk menghitung saldo berjalan (running balance)
            $saldoBerjalan = $saldoAwal;
            $mutasiPeriode->transform(function ($item) use (&$saldoBerjalan) {
                $saldoBerjalan += $item->debit;
                $saldoBerjalan -= $item->kredit;
                $item->saldo = $saldoBerjalan;
                return $item;
            });

            // **TERAPKAN Filter Tipe Mutasi**
            if ($request->filled('tipe_mutasi')) {
                $tipe = $request->tipe_mutasi;
                if ($tipe === 'debit') {
                    // Filter hanya yang punya nilai debit > 0
                    $mutasi = $mutasiPeriode->filter(fn ($item) => $item->debit > 0);
                } elseif ($tipe === 'kredit') {
                    // Filter hanya yang punya nilai kredit > 0
                    $mutasi = $mutasiPeriode->filter(fn ($item) => $item->kredit > 0);
                }
            } else {
                // Jika tidak ada filter tipe mutasi, gunakan semua mutasi periode
                $mutasi = $mutasiPeriode;
            }
        }

        // Perluas compact untuk mengirim daftarKas ke view
        return view('bendahara.keuangan.kas_kecil.index', compact('mutasi', 'kasDipilih', 'daftarKas', 'saldoAwal'));
    }
}
