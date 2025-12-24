<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\CalonSiswa;
use App\Models\TahunPelajaran;
use App\Models\JalurPendaftaran;
use App\Models\SyaratPendaftaran;
use App\Models\KompetensiPendaftaran;
use Illuminate\Http\Request;
use App\Models\BerandaPpdb;
use App\Models\KeunggulanPpdb;
use App\Models\KompetensiPpdb;
use App\Models\TingkatPendaftaran;
use App\Models\Sekolah;

class LandingPpdbController extends Controller
{

    public function beranda()
    {
        $beranda = BerandaPpdb::first();
        $keunggulanList = KeunggulanPpdb::all();
        $tahunAktif = TahunPelajaran::where('is_active', 1)->first();

        return view('landing.ppdb.beranda', compact('beranda', 'keunggulanList', 'tahunAktif'));
    }

    public function kompetensiKeahlian()
    {
        $kompetensiList = KompetensiPpdb::all();
        return view('landing.ppdb.kompetensiKeahlian', compact('kompetensiList'));
    }

    public function daftarCalonSiswa(Request $request)
{
    $tingkatAktif = TingkatPendaftaran::where('is_active', 1)->first();
    if (!$tingkatAktif) $tingkatAktif = (object)['tingkat' => 0];

    if ($request->ajax()) {
        $tahunAktif = TahunPelajaran::where('is_active', 1)->first();
        if (!$tahunAktif) return response()->json(['applicants' => [], 'tingkat' => $tingkatAktif->tingkat]);

        $applicants = CalonSiswa::where('tahun_id', $tahunAktif->id)
            ->where('tingkat', $tingkatAktif->tingkat)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'applicants' => $applicants,
            'tingkat'    => $tingkatAktif->tingkat
        ]);
    }


    return view('landing.ppdb.daftarCalonSiswa', compact('tingkatAktif'));
}


    public function formulirPendaftaran()
    {
        $tahunAktif = TahunPelajaran::where('is_active', 1)->first();
        $tingkatAktif = TingkatPendaftaran::where('is_active', 1)->first();

        $jurusans = KompetensiPendaftaran::all();

        $jalurs = $tahunAktif
            ? JalurPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)
                ->where('is_active', true)
                ->get()
            : collect();

        $syarats = $tahunAktif
            ? SyaratPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)
                ->where('is_active', true)
                ->get()
            : collect();

        

            // Tentukan kelas asal sesuai tingkat
            $kelasAsal = [];
            switch($tingkatAktif->tingkat ?? null) {
                case 10:
                    // IX A - IX K
                    for ($i = 65; $i <= 75; $i++) {
                        $kelasAsal[] = "IX " . chr($i);
                    }
                    break;
                case 7:
                    // VI A - VI K
                    for ($i = 65; $i <= 75; $i++) {
                        $kelasAsal[] = "VI " . chr($i);
                    }
                    break;
                case 1:
                    // I A - I K
                    for ($i = 65; $i <= 75; $i++) {
                        $kelasAsal[] = chr($i);
                    }
                    break;
    }

        return view('landing.ppdb.formulirPendaftaran', compact(
            'tahunAktif', 'jalurs', 'jurusans', 'syarats', 'tingkatAktif', 'kelasAsal'
        ));
    }

    public function kontak()
    {
        return view('landing.ppdb.kontak');
    }

    public function submitForm(Request $request)
    {
        dd($request->all());
    }

    public function formulirStore(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('is_active', 1)->first();
        $tingkatAktif = TingkatPendaftaran::where('is_active', 1)->first();
        $sekolah = Sekolah::first();

        if (!$tahunAktif || !$tingkatAktif) {
            return redirect()->back()->with('error', 'Tahun pelajaran atau tingkat aktif belum diset.');
        }

        $validated = $request->validate([
            'tahun_id'      => 'required|exists:tahun_pelajarans,id',
            'jalur_id'      => 'required|exists:jalur_pendaftarans,id',
            'nama_lengkap'  => 'required|string|max:255',
            'nisn'          => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tgl_lahir'     => 'nullable|date',
            'alamat_lengkap'=> 'nullable|string',
            'nama_ayah'     => 'nullable|string|max:255',
            'nama_ibu'      => 'nullable|string|max:255',
            'kontak'        => 'nullable|string|max:20',
            'asal_sekolah'  => 'nullable|string|max:255',
            'kelas'         => 'nullable|string|max:20',
            'jurusan'       => 'nullable|string|max:50',
            'ukuran_pakaian'=> 'nullable|string|max:20',
            'syarat_file_*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:1024',
        ]);

        // gunakan $request->filled agar hanya cek saat ada input NISN
        if ($request->filled('nisn') && CalonSiswa::where('nisn', $request->nisn)->exists()) {
            // pakai 'error' supaya toast/layout lo nangkep
            return redirect()->back()
                ->with('error', 'NISN sudah terdaftar.')
                ->withInput();
        }

        // isi otomatis kolom tingkat dari tingkat aktif
        $validated['tingkat'] = $tingkatAktif->tingkat;
        $validated['tahun_id'] = $tahunAktif->id;

        // ambil kode sekolah dan pastikan 3 digit
        $kodeSekolah = $sekolah ? str_pad($sekolah->kode_sekolah, 3, '0', STR_PAD_LEFT) : 'XXX';
        
        // ambil angka terahir tahun
        $tahunString = $tahunAktif->tahun_pelajaran; // misal '2025 - 2026' atau '20252026'

        // hapus spasi dulu biar aman
        $tahunString = str_replace(' ', '', $tahunString);

        // cek kalau ada '-' atau '/' pakai explode, kalau nggak ambil 4 digit pertama dan terakhir
        if (strpos($tahunString, '-') !== false) {
            $tahunParts = explode('-', $tahunString);
            $tahunAwal = substr($tahunParts[0], 2, 2);
            $tahunAkhir = substr($tahunParts[1], 2, 2);
        } elseif (strpos($tahunString, '/') !== false) {
            $tahunParts = explode('/', $tahunString);
            $tahunAwal = substr($tahunParts[0], 2, 2);
            $tahunAkhir = substr($tahunParts[1], 2, 2);
        } else {
            // asumsikan format 8 digit '20252026'
            $tahunAwal = substr($tahunString, 2, 2);
            $tahunAkhir = substr($tahunString, 6, 2);
        }

        $kodeTahun = $tahunAwal . $tahunAkhir;

        // nomor urut per tahun pelajaran
        $last = CalonSiswa::where('tahun_id', $tahunAktif->id)
            ->orderByDesc('id')
            ->first();
        $urutan = $last ? str_pad((int)substr($last->nomor_resi, -3)+1, 3, '0', STR_PAD_LEFT) : '001';

        // gabungkan jadi nomor resi
        $validated['nomor_resi'] = "{$kodeSekolah}-{$kodeTahun}-{$urutan}";

        $calon = CalonSiswa::create($validated);

        // simpan syarat dan file_path
        $syaratIds = $request->syarat_id ?? [];
        foreach ($syaratIds as $id) {
            $filePath = null;
            if ($request->hasFile("syarat_file_{$id}")) {
                $file = $request->file("syarat_file_{$id}");
                $filePath = $file->store("syarat/{$calon->id}", 'public');
            }

            $calon->syarat()->attach($id, [
                'is_checked' => true,
                'file_path'  => $filePath,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // cek syarat wajib
        $syaratWajib = SyaratPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)
            ->where('jalurPendaftaran_id', $validated['jalur_id'])
            ->where('is_active', true)
            ->count();

        $syaratTerpenuhi = $calon->syarat()
            ->wherePivot('is_checked', true)
            ->count();

        $calon->status = $syaratTerpenuhi >= $syaratWajib ? 1 : 0;
        $calon->save();

        return redirect()->back()->with('success', 'Formulir calon peserta didik berhasil disimpan.');
    }

    /**
     * LOGIKA UTAMA: Cek apakah hari ini masuk jadwal PPDB?
     * (Digunakan oleh tombol "Daftar Sekarang" di Navbar/Home)
     */
    public function cekStatusPpdb()
    {
        $sekarang = Carbon::now();

        // Cari Agenda dengan kategori 'PPDB' yang SEDANG BERLANGSUNG hari ini
        $agendaAktif = Agenda::where('kategori', 'PPDB') 
                            ->whereDate('tanggal_mulai', '<=', $sekarang)
                            ->whereDate('tanggal_selesai', '>=', $sekarang)
                            ->first();

        // Jika ditemukan agenda PPDB yang aktif
        if ($agendaAktif) {
            // BUKA: Arahkan ke halaman pendaftaran utama
            return redirect()->route('ppdb.beranda');
        } 
        
        // TUTUP: Arahkan ke halaman pengumuman tutup
        return redirect()->route('ppdb.tutup');
    }

    /**
     * Menampilkan Halaman Pengumuman (Jika Tutup)
     */
    public function halamanTutup()
    {
        // Cari Agenda PPDB yang AKAN DATANG (untuk memberi info ke user)
        $agendaAkanDatang = Agenda::where('kategori', 'PPDB')
                                  ->whereDate('tanggal_mulai', '>', Carbon::now())
                                  ->orderBy('tanggal_mulai', 'asc')
                                  ->first();
        
        return view('landing.ppdb.closed', compact('agendaAkanDatang'));
    }
}
