<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\TahunPelajaran;
use App\Models\JalurPendaftaran;
use App\Models\SyaratPendaftaran;
use App\Models\KompetensiPendaftaran;
use Illuminate\Http\Request;
use App\Models\BerandaPpdb;
use App\Models\KeunggulanPpdb;
use App\Models\KompetensiPpdb;
use App\Models\ProfilSekolah;
use App\Models\TingkatPendaftaran;

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

        // isi otomatis kolom tingkat dari tingkat aktif
        $validated['tingkat'] = $tingkatAktif->tingkat;
        $validated['tahun_id'] = $tahunAktif->id;

        // generate nomor resi otomatis
        $prefix = "137";
        $tanggal = now()->format('ymd');
        $last = CalonSiswa::whereDate('created_at', now()->toDateString())
            ->orderByDesc('id')
            ->first();

        $urutan = $last ? str_pad((int)substr($last->nomor_resi, -3) + 1, 3, '0', STR_PAD_LEFT) : "001";
        $validated['nomor_resi'] = "{$prefix}-{$tanggal}.{$urutan}";

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
}
