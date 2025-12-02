<?php

namespace App\Http\Controllers\Bendahara\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\KasMutasi;
use App\Models\MasterKas;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengeluaranController extends Controller
{
    /**
     * Menampilkan daftar pengeluaran dan form tambah.
     */
    public function index()
    {
        $pengeluarans = Pengeluaran::with('masterKas', 'petugas')->latest()->get();
      $daftarKas = MasterKas::where('is_active', true)->get();

        return view('bendahara.keuangan.pengeluaran.index', compact('pengeluarans', 'daftarKas'));
    }

    /**
     * Menyimpan data pengeluaran baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'master_kas_id' => 'required|exists:master_kas,id',
            'uraian' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            // 1. Catat di tabel Pengeluaran
            $pengeluaran = Pengeluaran::create([
                'tanggal' => $request->tanggal,
                'master_kas_id' => $request->master_kas_id,
                'uraian' => $request->uraian,
                'nominal' => $request->nominal,
                'petugas_id' =>  null, // Asumsi user login memiliki relasi ke GTK
            ]);

            // 2. Catat di Jurnal Kas (Kas Mutasi) sebagai KREDIT (Uang Keluar)
            KasMutasi::create([
                'master_kas_id' => $request->master_kas_id,
                'tanggal' => $request->tanggal,
                'sumber_transaksi' => 'Pengeluaran',
                'transaksi_id' => $pengeluaran->id,
                'keterangan' => $request->uraian,
                'debit' => 0,
                'kredit' => $request->nominal,
            ]);

            DB::commit();
            return back()->with('success', 'Data pengeluaran berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan pengeluaran: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat menyimpan pengeluaran.')->withInput();
        }
    }

    /**
     * Memperbarui data pengeluaran.
     */
    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'master_kas_id' => 'required|exists:master_kas,id',
            'uraian' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update data pengeluaran utama
            $pengeluaran->update($request->all());

            // 2. Cari atau buat baru data di Kas Mutasi yang terkait
            KasMutasi::updateOrCreate(
                [
                    'sumber_transaksi' => 'Pengeluaran',
                    'transaksi_id' => $pengeluaran->id,
                ],
                [
                    'master_kas_id' => $request->master_kas_id,
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->uraian,
                    'debit' => 0,
                    'kredit' => $request->nominal,
                ]
            );

            DB::commit();
            return redirect()->route('bendahara.keuangan.pengeluaran.index')->with('success', 'Data pengeluaran berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat update pengeluaran: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem.')->withInput();
        }
    }

    /**
     * Menghapus data pengeluaran.
     */
    public function destroy(Pengeluaran $pengeluaran)
    {
        DB::beginTransaction();
        try {
            // 1. Hapus dulu catatan di Kas Mutasi
            KasMutasi::where('sumber_transaksi', 'Pengeluaran')
                ->where('transaksi_id', $pengeluaran->id)
                ->delete();

            $pengeluaran->delete();

            DB::commit();
            return back()->with('success', 'Data pengeluaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus pengeluaran: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat menghapus data.');
        }
    }
}
