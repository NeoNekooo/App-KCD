<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rombel;
use App\Models\Gtk;
use App\Models\TugasPegawai; 
use Illuminate\Support\Collection; 

class TugasPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // --- PENGATURAN TAHUN AKTIF ---
        $tahunAktifTampil = '2024/2025';
        $semesterAktifTampil = 'Ganjil';
        $semesterAktifId = '20251'; // ID Semester dari rombels.sql

        // --- AMBIL DATA TUGAS POKOK (Sudah Benar) ---
        
        $rombels = Rombel::where('semester_id', $semesterAktifId)->get();
        $allPembelajaran = new Collection(); 

        foreach ($rombels as $rombel) {
            if (!empty($rombel->pembelajaran)) {
                $pembelajaranArray = $rombel->pembelajaran; 
                if (is_array($pembelajaranArray)) {
                    foreach ($pembelajaranArray as $pembelajaran) {
                        $allPembelajaran->push($pembelajaran);
                    }
                }
            }
        }

        $ptkUuids = $allPembelajaran->pluck('ptk_id')->unique()->filter()->all();
        $gtks = Gtk::whereIn('ptk_id', $ptkUuids)->get()->keyBy('ptk_id');

        $tugasPokok = $allPembelajaran->map(function ($tugas) use ($gtks) {
            
            $tugasObj = is_array($tugas) ? (object) $tugas : $tugas;
            $gtk = $gtks->get($tugasObj->ptk_id);
            $tugasObj->gtk = $gtk; 

            $tugasObj->tugas_pokok = $tugasObj->nama_mata_pelajaran ?? 'N/A';
            $tugasObj->jumlah_jam = $tugasObj->jam_mengajar_per_minggu ?? 0;
            
            if ($gtk) {
                $tugasObj->tmt = $gtk->tanggal_surat_tugas; 
                $rwyKepangkatan = json_decode($gtk->rwy_kepangkatan); 
                
                if (!empty($rwyKepangkatan) && is_array($rwyKepangkatan) && isset($rwyKepangkatan[0])) {
                    $skTerbaru = (object) $rwyKepangkatan[0];
                    if (isset($skTerbaru->nomor_sk)) {
                        $tugasObj->nomor_sk = $skTerbaru->nomor_sk;
                    } else {
                        $tugasObj->nomor_sk = null;
                    }
                } else {
                    $tugasObj->nomor_sk = null; 
                }
            } else {
                $tugasObj->tmt = null;
                $tugasObj->nomor_sk = null;
            }
            return $tugasObj;
        });

        $tugasPokok = $tugasPokok->unique(function ($item) {
            return $item->ptk_id . $item->tugas_pokok;
        });


        // --- AMBIL DATA TUGAS TAMBAHAN ---
        
        // 1. Wali Kelas (INI YANG DIPERBAIKI: Ditambah filter jenis_rombel_str)
        $waliKelas = Rombel::with('waliKelas') 
            ->where('semester_id', $semesterAktifId) 
            ->whereNotNull('ptk_id')
            ->where('jenis_rombel_str', 'Kelas') // <-- PERBAIKAN DI SINI
            ->get();

        // 2. Pembina Ekskul (Datanya belum ada di SQL yg kamu kirim)
        // $pembinaEkskul = new Collection(); // Kita buat kosong dulu
        
        // 3. Jabatan Struktural (Sudah Benar)
        $jabatanStrukturalList = [
            'Kepala Sekolah',
            'Wakil Kepala Sekolah Bidang Kurikulum',
            'Wakil Kepala Sekolah Bidang Kesiswaan',
            'Kepala Program Keahlian'
        ];
        $jabatanStruktural = Gtk::whereIn('jabatan_ptk_id_str', $jabatanStrukturalList)->get();

        // 4. Tenaga Kependidikan (INI YANG BARU)
        $tendik = Gtk::whereNotIn('jabatan_ptk_id_str', $jabatanStrukturalList) // Ambil yang BUKAN struktural
                    ->where('jenis_ptk_id_str', 'Tenaga Kependidikan') // Filter hanya tendik
                    ->get();


        // 5. KIRIM SEMUA DATA KE VIEW
        return view('admin.kepegawaian.tugas-pegawai.index', compact(
            'tugasPokok',
            'waliKelas',
            // 'pembinaEkskul', 
            'jabatanStruktural',
            'tendik', // <-- Kirim data baru
            'tahunAktifTampil',
            'semesterAktifTampil'
        ));
    }

    // ... sisa Controller ...
    
    public function create(){}
    public function store(Request $request){}
    public function show(string $id){}
    public function edit(string $id){}
    public function update(Request $request, string $id){}
    public function destroy(string $id){}
}