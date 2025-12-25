<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PublicScanController extends Controller
{
    /**
     * Langkah 1: Tampilkan Halaman Input Password
     * URL: /scan/{id}
     */
    public function showVerificationPage($id)
    {
        // 1. Cek apakah ID ini milik Siswa
        $siswa = DB::table('siswas')
            ->where('peserta_didik_id', $id)
            ->orWhere('registrasi_id', $id)
            ->first();

        if ($siswa) {
            $name = $siswa->nama ?? 'Siswa Tanpa Nama'; // Pastikan ada kolom nama di tabel siswas (biasanya ada)
            $type = 'Siswa';
        } else {
            // 2. Jika bukan Siswa, Cek apakah GTK
            $gtk = DB::table('gtks')
                ->where('ptk_id', $id)
                ->first();

            if ($gtk) {
                $name = $gtk->nama ?? 'Guru/Staff';
                $type = 'GTK';
            } else {
                abort(404, 'Data tidak ditemukan atau QR Code tidak valid.');
            }
        }

        // Tampilkan view form password
        return view('scan.verify', [
            'id' => $id,
            'name' => $name, // Tampilkan nama agar orang tau siapa yg di-scan
            'type' => $type
        ]);
    }

    /**
     * Langkah 2: Proses Verifikasi Password
     * Method: POST
     */
    public function processVerification(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'password' => 'required',
        ]);

        $id = $request->id;
        $inputPassword = $request->password;

        // 1. Cari User di tabel Penggunas berdasarkan ID
        // Di Dapodik, pengguna_id biasanya sama dengan peserta_didik_id atau ptk_id
        $user = DB::table('penggunas')
            ->where('pengguna_id', $id)
            ->orWhere('peserta_didik_id', $id) // Jaga-jaga jika kolomnya beda
            ->orWhere('ptk_id', $id)           // Jaga-jaga untuk guru
            ->first();

        // Jika user tidak punya akun login, kita tidak bisa verifikasi password
        if (!$user) {
            return back()->withErrors(['password' => 'Akun pengguna untuk data ini tidak ditemukan. Hubungi Admin.']);
        }

        // 2. Cek Password (Hash match)
        // Password di 'penggunas' adalah bcrypt, jadi pakai Hash::check
        if (Hash::check($inputPassword, $user->password)) {

            // --- PASSWORD BENAR: AMBIL DATA LENGKAP ---

            if ($request->type == 'Siswa') {
                $data = DB::table('siswas')->where('peserta_didik_id', $id)->orWhere('registrasi_id', $id)->first();
                $view = 'scan.result-siswa';
            } else {
                $data = DB::table('gtks')->where('ptk_id', $id)->first();
                $view = 'scan.result-gtk';
            }

            // Render halaman hasil
            return view($view, ['data' => $data, 'user' => $user]);

        } else {
            // Password Salah
            return back()->withErrors(['password' => 'Password salah! Verifikasi gagal.']);
        }
    }
}
