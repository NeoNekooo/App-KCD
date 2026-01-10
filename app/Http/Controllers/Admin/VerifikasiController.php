<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerifikasiController extends Controller
{
    /**
     * Menampilkan semua data verifikasi masuk (Inbox Utama)
     */
    public function index(Request $request)
    {
        $title = 'Semua Verifikasi Masuk';
        
        // Inisialisasi Query
        $query = PengajuanSekolah::query();

        // Fitur Filter Status (Berdasarkan permintaan: melihat yang belum dicek, dll)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->latest()->paginate(10)->withQueryString();
        
        return view('admin.verifikasi.index', compact('data', 'title'));
    }

    /**
     * Menampilkan data berdasarkan kategori (Filter Sidebar)
     * Dengan perbaikan logic normalisasi agar data masuk ke kategori yang tepat
     */
    public function indexByKategori(Request $request, $kategori)
    {
        // 1. Normalisasi: Ubah slug 'kenaikan-pangkat' jadi string 'kenaikan pangkat'
        $keyword = str_replace('-', ' ', $kategori);

        // 2. Set judul halaman (UI)
        $title = ucwords($keyword);

        // 3. Query Dasar: Cari berdasarkan kategori yang mirip
        $query = PengajuanSekolah::where('kategori', 'LIKE', '%' . $keyword . '%');

        // 4. Tambahan Filter Status di dalam Kategori
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->latest()->paginate(10)->withQueryString();

        return view('admin.verifikasi.index', compact('data', 'title', 'kategori'));
    }

    /**
     * Step 1: Kirim daftar persyaratan ke Sekolah (Atur Syarat)
     */
    public function kirimPermintaan(Request $request, $id)
    {
        $data = PengajuanSekolah::findOrFail($id);

        $request->validate([
            'syarat'   => 'required|array|min:1',
            'syarat.*' => 'nullable|string',
        ]);

        $syaratList = [];
        foreach($request->syarat as $namaSyarat) {
            $bersihNama = trim($namaSyarat);
            if (empty($bersihNama)) continue;

            $syaratList[] = [
                'nama'    => htmlspecialchars($bersihNama),
                'file'    => null, 
                'valid'   => null, 
                'catatan' => null  
            ];
        }

        if (empty($syaratList)) {
            return back()->with('error', 'Minimal pilih atau isi satu persyaratan!');
        }

        // Update Lokal
        $data->update([
            'dokumen_syarat' => $syaratList, 
            'status'         => 'Menunggu Upload' 
        ]);

        // Kirim Notifikasi API ke Sekolah
        try {
            $response = Http::withHeaders([
                'X-API-KEY' => env('API_SECRET_KEY'),
                'Accept'    => 'application/json'
            ])->timeout(10)->post($data->url_callback, [
                'uuid'         => $data->uuid,
                'status'       => 'Lengkapi Berkas',
                'requirements' => $syaratList
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Daftar persyaratan berhasil dikirim ke Sekolah!');
            }
            return back()->with('warning', 'Data disimpan, tapi gagal sinkron ke server Sekolah.');
        } catch (\Exception $e) {
            Log::error("Koneksi gagal ke sekolah: " . $e->getMessage());
            return back()->with('error', 'Gagal hubungi server sekolah. Cek koneksi internet.');
        }
    }

    /**
     * Step 2: Simpan hasil pemeriksaan dokumen (ACC/Tolak)
     */
    public function simpanPemeriksaan(Request $request, $id)
    {
        $data = PengajuanSekolah::findOrFail($id);
        $syaratList = $data->dokumen_syarat; 
        
        if (!is_array($syaratList)) {
            return back()->with('error', 'Data persyaratan korup!');
        }

        $allValid = true;
        foreach($syaratList as $index => $item) {
            // Cek status validasi dari checkbox
            $isValid = isset($request->verifikasi[$index]['valid']);
            $syaratList[$index]['valid'] = $isValid;
            
            // Simpan catatan jika ditolak
            $catatanRaw = $request->verifikasi[$index]['catatan'] ?? 'Dokumen tidak sesuai';
            $syaratList[$index]['catatan'] = $isValid ? null : htmlspecialchars($catatanRaw);

            if(!$isValid) $allValid = false; 
        }

        // Tentukan Status Akhir
        $statusAkhir = $allValid ? 'ACC' : 'Ditolak';

        // Update Lokal
        $data->update([
            'dokumen_syarat' => $syaratList,
            'status'         => $statusAkhir
        ]);

        // Kirim Hasil ke Sekolah via API
        try {
            $response = Http::withHeaders([
                'X-API-KEY' => env('API_SECRET_KEY'),
                'Accept'    => 'application/json'
            ])->timeout(10)->post($data->url_callback, [
                'uuid'         => $data->uuid,
                'status'       => $statusAkhir,
                'requirements' => $syaratList 
            ]);

            return back()->with('success', 'Status pemeriksaan berhasil diperbarui dan dikirim!');
        } catch (\Exception $e) {
            Log::error("Gagal notif hasil ke sekolah: " . $e->getMessage());
            return back()->with('warning', 'Status diperbarui lokal, tapi gagal menghubungi server sekolah.');
        }
    }
}