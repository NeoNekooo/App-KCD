<?php

namespace App\Http\Controllers\Pkks;

use App\Http\Controllers\Controller;
use App\Models\PkksInstrumen;
use App\Models\Gtk;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PkksPenilaianController extends Controller
{
    /**
     * Halaman Daftar Instrumen yang Tersedia
     */
    public function index()
    {
        $user = auth('pengguna')->user();
        $sekolah = Sekolah::where('sekolah_id', $user->sekolah_id)->first();
        
        if (!$sekolah) {
            return back()->with('error', 'Data sekolah tidak ditemukan.');
        }

        // Cari instrumen aktif buat jenjang sekolah dia tahun ini
        $instrumen = PkksInstrumen::with(['kompetensis.indikators'])
            ->where('jenjang', $sekolah->bentuk_pendidikan_id_str)
            ->where('is_active', true)
            ->where('tahun', date('Y'))
            ->first();

        if (!$instrumen) {
            return view('user.pkks.not_available', compact('sekolah'));
        }

        // Validasi Waktu (Auto-Lock)
        $now = Carbon::now();
        $kepsek = Gtk::where('sekolah_id', $user->sekolah_id)
            ->where('jenis_ptk_id_str', 'LIKE', '%Kepala Sekolah%')
            ->first();

        // Jika Belum Buka atau Sudah Tutup, arahkan ke halaman status
        if (($instrumen->start_at && $now->lt($instrumen->start_at)) || ($instrumen->end_at && $now->gt($instrumen->end_at))) {
            return view('user.pkks.status', compact('instrumen', 'sekolah', 'kepsek', 'now'));
        }

        // Kalau semua OK, langsung gass tampilkan soal!
        return view('user.pkks.show', compact('instrumen', 'kepsek'));
    }

    /**
     * Halaman Form Pengisian
     */
    public function show($id)
    {
        $user = auth('pengguna')->user();
        $instrumen = PkksInstrumen::with(['kompetensis.indikators'])->findOrFail($id);
        
        // Validasi Waktu (Auto-Lock)
        $now = Carbon::now();
        if ($instrumen->start_at && $now->lt($instrumen->start_at)) {
            return redirect()->route('user.pkks.index')->with('error', 'Penilaian belum dibuka. Silakan tunggu jadwal dimulai.');
        }
        if ($instrumen->end_at && $now->gt($instrumen->end_at)) {
            return redirect()->route('user.pkks.index')->with('error', 'Maaf, waktu penilaian sudah ditutup.');
        }

        $kepsek = Gtk::where('sekolah_id', $user->sekolah_id)
            ->where('jenis_ptk_id_str', 'LIKE', '%Kepala Sekolah%')
            ->first();

        return view('user.pkks.show', compact('instrumen', 'kepsek'));
    }

    /**
     * Simpan Jawaban
     */
    public function store(Request $request, $id)
    {
        $user = auth('pengguna')->user();
        $instrumen = PkksInstrumen::findOrFail($id);
        $sekolah = Sekolah::where('sekolah_id', $user->sekolah_id)->first();
        
        $request->validate([
            'jawaban' => 'required|array',
            'jawaban.*' => 'required|integer|min:1|max:' . $instrumen->skor_maks,
        ]);

        $kepsek = Gtk::where('sekolah_id', $user->sekolah_id)
            ->where('jenis_ptk_id_str', 'LIKE', '%Kepala Sekolah%')
            ->first();

        \DB::beginTransaction();
        try {
            // 1. Buat Header Penilaian
            $penilaian = \App\Models\PkksPenilaian::create([
                'instansi_id' => $sekolah->instansi_id, 
                'pkks_instrumen_id' => $id,
                'sekolah_id' => $user->sekolah_id,
                'kepala_sekolah_id' => $kepsek->id ?? null,
                'penilai_id' => $user->id,
                'penilai_type' => get_class($user),
                'skor_total' => array_sum($request->jawaban),
                'catatan' => $request->catatan
            ]);

            // 2. Simpan Detail Jawaban
            foreach ($request->jawaban as $indikatorId => $skor) {
                \App\Models\PkksJawabanIndikator::create([
                    'pkks_penilaian_id' => $penilaian->id,
                    'pkks_indikator_id' => $indikatorId,
                    'skor' => $skor
                ]);
            }

            \DB::commit();
            return redirect()->route('pkks.penilaian.index')->with('success', 'Terima kasih! Penilaian Anda telah berhasil dikirim.');
            
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }
}
