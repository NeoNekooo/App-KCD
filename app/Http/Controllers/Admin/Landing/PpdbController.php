<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BerandaPpdb;
use App\Models\KeunggulanPpdb;
use App\Models\KompetensiPpdb;
use App\Models\CalonSiswa;
use App\Models\SyaratPendaftaran;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PpdbController extends Controller
{
    public function index()
    {
        $beranda = BerandaPpdb::first();
        $keunggulanList = KeunggulanPpdb::all();
        $kompetensiList = KompetensiPpdb::all();

        return view('admin.pengaturan.ppdb.landingPpdb', compact('beranda', 'keunggulanList', 'kompetensiList'));
    }

    public function store(Request $request)
    {
        // BERANDA
        $beranda = BerandaPpdb::first() ?? new BerandaPpdb();
        $beranda->slogan_utama = $request->slogan_utama;
        $beranda->deskripsi_singkat = $request->deskripsi_singkat;
        $beranda->point_keunggulan_1 = $request->point_keunggulan_1;
        $beranda->save();
    
        // KEUNGGULAN
        $idKeunggulan = $request->id_keunggulan ?? [];
        $judulItem = $request->judul_item ?? [];
        $deskripsiItem = $request->deskripsi_item ?? [];
        $icons = $request->icon_keunggulan ?? [];
        $deleteKeunggulan = $request->delete_keunggulan ?? [];
        $judulKeunggulan = $request->judul_keunggulan;
        $deskripsiKeunggulan = $request->deskripsi_keunggulan;
    
        foreach ($judulItem as $i => $judul) {
            $id = $idKeunggulan[$i] ?? null;
            $delete = $deleteKeunggulan[$i] ?? 0;
        
            if ($id && $delete) {
                $keunggulan = KeunggulanPpdb::find($id);
                if($keunggulan){
                    if($keunggulan->icon) Storage::disk('public')->delete($keunggulan->icon);
                    $keunggulan->delete();
                }
                continue;
            }
        
            $keunggulan = $id ? KeunggulanPpdb::find($id) : new KeunggulanPpdb();
            $keunggulan->judul_keunggulan = $judulKeunggulan;
            $keunggulan->deskripsi_keunggulan = $deskripsiKeunggulan;
            $keunggulan->judul_item = $judul;
            $keunggulan->deskripsi_item = $deskripsiItem[$i] ?? '';
        
            if (isset($icons[$i]) && $icons[$i] && $icons[$i]->isValid()) {
                if($keunggulan->icon) Storage::disk('public')->delete($keunggulan->icon);
                $keunggulan->icon = $icons[$i]->store('keunggulan_icons', 'public');
            }
        
            $keunggulan->save();
        }
    
        // KOMPETENSI
        $idKompetensi = $request->id_kompetensi ?? [];
        $namaKompetensi = $request->nama_kompetensi ?? [];
        $kodeKompetensi = $request->kode_kompetensi ?? [];
        $deskripsiJurusan = $request->deskripsi_jurusan ?? [];
        $iconKompetensi = $request->icon_kompetensi ?? [];
        $deleteKompetensi = $request->delete_kompetensi ?? [];
        $judulKompetensi = $request->judul_kompetensi;
        $deskripsiKompetensi = $request->deskripsi_kompetensi;
    
        foreach ($namaKompetensi as $i => $nama) {
            $id = $idKompetensi[$i] ?? null;
            $delete = $deleteKompetensi[$i] ?? 0;
        
            if ($id && $delete) {
                $kompetensi = KompetensiPpdb::find($id);
                if($kompetensi){
                    if($kompetensi->icon) Storage::disk('public')->delete($kompetensi->icon);
                    $kompetensi->delete();
                }
                continue;
            }
        
            $kompetensi = $id ? KompetensiPpdb::find($id) : new KompetensiPpdb();
            $kompetensi->judul_kompetensi = $judulKompetensi;
            $kompetensi->deskripsi_kompetensi = $deskripsiKompetensi;
            $kompetensi->nama_kompetensi = $nama;
            $kompetensi->kode_kompetensi = $kodeKompetensi[$i] ?? '';
            $kompetensi->deskripsi_jurusan = $deskripsiJurusan[$i] ?? '';
        
            if (isset($iconKompetensi[$i]) && $iconKompetensi[$i] && $iconKompetensi[$i]->isValid()) {
                if($kompetensi->icon) Storage::disk('public')->delete($kompetensi->icon);
                $kompetensi->icon = $iconKompetensi[$i]->store('kompetensi_icons', 'public');
            }
        
            $kompetensi->save();
        }
    
        return back()->with('success', 'Data Landing PPDB berhasil disimpan.');
    }

}
