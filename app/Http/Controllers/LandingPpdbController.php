<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\TahunPelajaran;
use App\Models\JalurPendaftaran;
use App\Models\SyaratPendaftaran;
use App\Models\Rombel;
use Illuminate\Http\Request;
use App\Models\BerandaPpdb;
use App\Models\KeunggulanPpdb;
use App\Models\KompetensiPpdb;
use App\Models\ProfilSekolah;

class LandingPpdbController extends Controller
{
    // public function beranda() {
    //     return view('interface.beranda');
    // }

    // public function jalurSyarat() {
    //     return view('interface.jalurSyarat');
    // }

    // public function quota() {
    //     return view('interface.quota');
    // }

    // public function kontak() {
    //     return view('interface.kontak');
    // }

    public function beranda() {

        $beranda = BerandaPpdb::first();
        $keunggulanList = KeunggulanPpdb::all();
        $tahunAktif = TahunPelajaran::where('is_active', 1)->first();

        return view('landing.ppdb.beranda', compact('beranda', 'keunggulanList','tahunAktif'));
    }

    public function kompetensiKeahlian() {

        $kompetensiList = KompetensiPpdb::all();
        return view('landing.ppdb.kompetensiKeahlian',compact('kompetensiList'));
    }

    public function daftarCalonSiswa(Request $request) {
        if ($request->ajax()) {
            $tahunAktif = TahunPelajaran::where('is_active', 1)->first();
            if (!$tahunAktif) {
                return response()->json(['applicants' => []]);
            }

            $yearSuffix = substr($tahunAktif->tahun_pelajaran, -2); // ambil 2 digit terakhir tahun akhir, misal '26'

            $applicants = CalonSiswa::where('tahun_id', $tahunAktif->id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($applicant) use ($yearSuffix) {
                    $nomerResi = $applicant->nomor_resi ?? '0';
                    // ambil 3 digit terakhir setelah titik (misal 251018.001 -> 001)
                    $parts = explode('.', $nomerResi);
                    $lastThree = end($parts); 
                    $lastThree = str_pad($lastThree, 3, '0', STR_PAD_LEFT);
                    $applicant->registration_number = 'PPDB'.$yearSuffix.'-'.$lastThree;
                    return $applicant;
                });

            return response()->json(['applicants' => $applicants]);
        }

        return view('landing.ppdb.daftarCalonSiswa');
    }

    public function formulirPendaftaran() {
        $tahunAktif = TahunPelajaran::where('is_active', 1)->first();

        $jurusans = Rombel::select('jurusan_id_str')
            ->distinct()
            ->orderBy('jurusan_id_str')
            ->pluck('jurusan_id_str');

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

        return view('landing.ppdb.formulirPendaftaraan',compact('tahunAktif','jalurs', 'jurusans', 'syarats'));
    }

    public function kontak() {
        return view('landing.ppdb.kontak');
    }

    public function submitForm(Request $request) {
        dd($request->all());
    }

    public function formulirStore(Request $request)
    {
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
            // validasi file syarat
            'syarat_file_*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:1024',
        ]);
    
        // generate nomor resi
        $prefix = "137";
        $tanggal = now()->format('ymd');
    
        $last = CalonSiswa::whereDate('created_at', now()->toDateString())
            ->orderByDesc('id')
            ->first();
    
        if ($last) {
            $lastNumber = (int) substr($last->nomor_resi, -3);
            $urutan = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $urutan = "001";
        }
    
        $nomerSeri = "{$prefix}-{$tanggal}.{$urutan}";
        $validated['nomor_resi'] = $nomerSeri;
    
        $calon = CalonSiswa::create($validated);
    
        // simpan syarat dan file_path
        $syaratIds = $request->syarat_id ?? [];
        foreach ($syaratIds as $id) {
            $filePath = null;
            if ($request->hasFile("syarat_file_{$id}")) {
                $file = $request->file("syarat_file_{$id}");
                $filePath = $file->store("syarat/{$calon->id}", 'public'); // simpan di storage/app/public/syarat/{calon_id}
            }
        
            // attach ke pivot table
            $calon->syarat()->attach($id, [
                'is_checked' => true,
                'file_path'  => $filePath,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        // cek syarat wajib
        $tahunId = $validated['tahun_id'];
        $jalurId = $validated['jalur_id'];
    
        $syaratWajib = SyaratPendaftaran::where('tahunPelajaran_id', $tahunId)
            ->where('jalurPendaftaran_id', $jalurId)
            ->where('is_active', true)
            ->count();
    
        $syaratTerpenuhi = $calon->syarat()
            ->wherePivot('is_checked', true)
            ->count();
    
        $calon->status = ($syaratTerpenuhi >= $syaratWajib) ? 1 : 0;
        $calon->save();
    
        return redirect()->back()->with('success', 'Formulir calon peserta didik berhasil disimpan.');

    }
}
