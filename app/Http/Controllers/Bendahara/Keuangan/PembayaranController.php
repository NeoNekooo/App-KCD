<?php

namespace App\Http\Controllers\Bendahara\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\KasMutasi;
use App\Models\MasterKas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Tunggakan;
use App\Models\Iuran;
use App\Models\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembayaranController extends Controller
{
    /**
     * Menampilkan halaman utama Penerimaan/Pembayaran.
     */
    public function index(Request $request)
    {
        $siswas = Siswa::where('status', 'Aktif')->orderBy('nama')->get();
$daftarKas = MasterKas::where('is_active', true)->get(); // Data untuk dropdown kas
        $siswaDipilih = null;

        if ($request->filled('siswa_id')) {
            $siswaDipilih = Siswa::with([
                'tagihans' => fn($q) => $q->where('sisa_tagihan', '>', 0),
                'tagihans.iuran',
                'tunggakans' => fn($q) => $q->where('sisa_tunggakan', '>', 0),
                'tunggakans.iuran',
                'pembayarans' => fn($q) => $q->with('iuran', 'petugas')->latest()->limit(10),
            ])->findOrFail($request->siswa_id);
        }

        return view('bendahara.keuangan.penerimaan.index', compact('siswas', 'siswaDipilih', 'daftarKas'));
    }

    /**
     * Menyimpan transaksi pembayaran baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'master_kas_id' => 'required|exists:master_kas,id',
            'pembayaran_untuk' => 'required|string', // Format: 'tagihan_1' atau 'tunggakan_2'
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        list($tipe, $id) = explode('_', $request->pembayaran_untuk);
        $jumlahBayar = $request->jumlah_bayar;

        DB::beginTransaction();
        try {
            $model = $tipe === 'tagihan' ? Tagihan::with('iuran')->findOrFail($id) : Tunggakan::with('iuran')->findOrFail($id);
            $sisa = $tipe === 'tagihan' ? 'sisa_tagihan' : 'sisa_tunggakan';

            if ($jumlahBayar > $model->$sisa) {
                return back()->with('error', 'Jumlah bayar melebihi sisa tagihan.')->withInput();
            }

            // 1. Catat di tabel Pembayaran
            $pembayaran = Pembayaran::create([
                'siswa_id' => $request->siswa_id,
                'iuran_id' => $model->iuran_id,
                'tagihan_id' => $tipe === 'tagihan' ? $id : null,
                'tunggakan_id' => $tipe === 'tunggakan' ? $id : null,
                'tanggal_bayar' => $request->tanggal_bayar,
                'jumlah_bayar' => $jumlahBayar,
                'master_kas_id' => $request->master_kas_id,
                'petugas_id' => null, // Asumsi user login memiliki relasi ke GTK
                'keterangan' => $request->keterangan,
            ]);

            // 2. Update sisa tagihan/tunggakan
            $model->$sisa -= $jumlahBayar;
            if ($model->$sisa <= 0) {
                $model->status = 'Lunas';
                $model->$sisa = 0; // Pastikan sisa tidak minus
            } elseif ($tipe === 'tagihan') {
                 $model->status = 'Cicilan';
            }
            $model->save();

            // 3. Catat di Jurnal Kas (Kas Mutasi) sebagai DEBIT (Uang Masuk)
            $siswa = Siswa::find($request->siswa_id);
            $keteranganKas = sprintf(
                'Pembayaran %s a/n %s',
                $model->iuran->nama_iuran,
                $siswa->nama
            );

            KasMutasi::create([
                'master_kas_id' => $request->master_kas_id,
                'tanggal' => $request->tanggal_bayar,
                'sumber_transaksi' => 'Pembayaran',
                'transaksi_id' => $pembayaran->id,
                'keterangan' => $keteranganKas,
                'debit' => $jumlahBayar,
                'kredit' => 0,
            ]);

            DB::commit();
            return back()->with('success', 'Pembayaran berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan pembayaran: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat menyimpan pembayaran.')->withInput();
        }
    }
}
