<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instansi;
use App\Models\PegawaiKcd;
use App\Models\JabatanKcd;
use Illuminate\Support\Facades\Storage;

class InstansiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        // Cek apakah dia Super Admin (Role Admin/Administrator tapi instansi_id KOSONG)
        $isSuperAdmin = in_array(strtolower($user->role ?? ''), ['admin', 'administrator', 'operator kcd']) && empty($user->instansi_id);
        
        // 1. Jika Super Admin dan tidak bawa ID -> Tampilkan TABEL LIST SEMUA KCD
        if ($isSuperAdmin && !$request->has('id')) {
            $listInstansi = Instansi::orderBy('nama_instansi', 'asc')->get();
            return view('admin.instansi.list', compact('listInstansi'));
        }

        // 2. Tampilkan Detail Profil (Untuk Admin Wilayah atau Super Admin yang sudah pilih ID)
        $targetId = $request->get('id');
        
        if ($isSuperAdmin && $targetId) {
            // Super Admin melihat spesifik ID (Tanpa filter regional)
            $instansi = Instansi::withoutGlobalScopes()->findOrFail($targetId);
        } else {
            // Default (Admin Wilayah akan terfilter otomatis oleh FilterRegional)
            $instansi = Instansi::first();
        }

        if (!$instansi) {
            $instansi = Instansi::create(['nama_instansi' => 'KCD Wilayah Baru']);
        }

        // --- SINKRONISASI KEPALA (AUTOMATIC) ---
        // Sesuai instansi yang sedang dilihat
        $jabatanKepala = JabatanKcd::withoutGlobalScopes()
                                    ->where('instansi_id', $instansi->id)
                                    ->where('nama', 'like', '%Kepala%')
                                    ->first();
        $kepala = null;
        if ($jabatanKepala) {
            // Syarat Tambahan: instansi_id harus cocok dengan instansi yang sedang dilihat
            $kepala = PegawaiKcd::where('instansi_id', $instansi->id)
                                ->where('jabatan_kcd_id', $jabatanKepala->id)
                                ->first();
            
            // Opsional: Update record instansi agar tetap sinkron di DB
            if ($kepala && ($instansi->nama_kepala !== $kepala->nama || $instansi->nip_kepala !== $kepala->nip)) {
                $instansi->update([
                    'nama_kepala' => $kepala->nama,
                    'nip_kepala'  => $kepala->nip
                ]);
            }
        }

        return view('admin.instansi.index', compact('instansi', 'kepala', 'isSuperAdmin'));
    }

    public function update(Request $request)
    {
        // Ambil target instansi (Prioritaskan ID jika ada, biasanya dari input hidden)
        $targetId = $request->id;
        if ($targetId) {
            $instansi = Instansi::withoutGlobalScopes()->findOrFail($targetId);
        } else {
            $instansi = Instansi::first();
        }

        // 1. Validasi Input
        $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'nama_brand'    => 'nullable|string|max:255',
            'peta'          => 'nullable|string',
            'social_media'  => 'nullable|array',
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tanda_tangan'  => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'lintang'       => 'nullable|numeric',
            'bujur'         => 'nullable|numeric',
            'visi'          => 'nullable|string',
            'misi'          => 'nullable|string',
            'sejarah_singkat' => 'nullable|string',
        ]);

        // 2. Ambil data input KECUALI logo, ttd, & social_media
        $data = $request->except(['id', 'logo', 'tanda_tangan', 'social_media', 'nama_kepala', 'nip_kepala']);

        // 3. Rapikan Array Social Media
        if ($request->has('social_media')) {
            $data['social_media'] = array_values($request->input('social_media'));
        } else {
            $data['social_media'] = []; 
        }

        // 4. Handle Upload Logo
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($instansi->logo && Storage::disk('public')->exists($instansi->logo)) {
                Storage::disk('public')->delete($instansi->logo);
            }
            // Simpan logo baru
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        // 5. Handle Upload Tanda Tangan (TTD)
        if ($request->hasFile('tanda_tangan')) {
            // Hapus TTD lama jika ada
            if ($instansi->tanda_tangan && Storage::disk('public')->exists($instansi->tanda_tangan)) {
                Storage::disk('public')->delete($instansi->tanda_tangan);
            }
            // Simpan TTD baru di folder 'signatures'
            $data['tanda_tangan'] = $request->file('tanda_tangan')->store('signatures', 'public');
        }

        // 6. Simpan Perubahan ke Database
        $instansi->update($data);

        return redirect()->back()->with('success', 'Profil Instansi dan Aset Surat berhasil diperbarui!');
    }
}