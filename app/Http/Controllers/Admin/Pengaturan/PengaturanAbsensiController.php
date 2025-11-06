<?php

namespace App\Http\Controllers\Admin\Pengaturan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Gunakan DB facade

class PengaturanAbsensiController extends Controller
{
    public function edit()
    {
        // Ambil SEMUA pengaturan untuk 7 hari, diurutkan berdasarkan ID/urutan hari
        $pengaturan = DB::table('pengaturan_absensi')->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")->get();
        return view('admin.pengaturan.absensi.edit', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'pengaturan' => 'required|array',
            'pengaturan.*.jam_masuk_sekolah' => 'required|date_format:H:i',
            'pengaturan.*.jam_pulang_sekolah' => 'required|date_format:H:i',
            'pengaturan.*.batas_toleransi_terlambat' => 'required|integer|min:0',
        ]);

        foreach ($request->pengaturan as $id => $data) {
            DB::table('pengaturan_absensi')->where('id', $id)->update([
                'jam_masuk_sekolah' => $data['jam_masuk_sekolah'],
                'jam_pulang_sekolah' => $data['jam_pulang_sekolah'],
                'batas_toleransi_terlambat' => $data['batas_toleransi_terlambat'],
                'is_active' => isset($data['is_active']) ? 1 : 0, // Cek apakah checkbox dicentang
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Pengaturan absensi berhasil diperbarui.');
    }
}