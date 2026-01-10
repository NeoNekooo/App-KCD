<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\PegawaiKcd;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PegawaiKcdController extends Controller
{
    public function index()
    {
        $pegawais = PegawaiKcd::with('user')->latest()->paginate(10);
        return view('admin.kepegawaian_kcd.index', compact('pegawais'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama'    => 'required|string|max:255',
            'jabatan' => 'required|string',
            'nip'     => 'nullable|string|max:50|unique:pegawai_kcds,nip', 
            'no_hp'   => 'nullable|string|max:20',
            'password'=> 'nullable|string|min:6', // Validasi password (opsional)
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 2. Generate Username (Pake NIP atau Nama Depan + Angka Acak)
                $username = $request->nip 
                    ? $request->nip 
                    : Str::slug(explode(' ', $request->nama)[0]) . rand(100, 999);
                
                // 3. TENTUKAN PASSWORD (CUSTOM ATAU DEFAULT)
                // Kalau input password diisi, pakai itu. Kalau kosong, pakai 'kcd123'.
                $passwordFix = $request->password ?: 'kcd123';

                // 4. Logic Mapping Jabatan -> Role
                if ($request->jabatan === 'Administrator') {
                    $role = 'Admin';
                } else {
                    $role = $request->jabatan; 
                }

                // 5. Buat Akun Login (Tabel Users)
                $user = User::create([
                    'name'     => $request->nama,
                    'email'    => $username . '@kcd.system', // Email dummy
                    'username' => $username,
                    'password' => Hash::make($passwordFix), // <--- Password dari variabel di atas
                    'role'     => $role,
                ]);

                // 6. Buat Data Biodata (Tabel PegawaiKcd)
                PegawaiKcd::create([
                    'user_id' => $user->id,
                    'nama'    => $request->nama,
                    'nip'     => $request->nip,
                    'jabatan' => $request->jabatan,
                    'no_hp'   => $request->no_hp,
                ]);
            });

            return back()->with('success', 'Pegawai berhasil ditambahkan! Akun Login otomatis aktif.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan pegawai: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $pegawai = PegawaiKcd::findOrFail($id);
        
        $request->validate([
            'nama'    => 'required|string|max:255',
            'jabatan' => 'required|string',
            'nip'     => 'nullable|string|max:50|unique:pegawai_kcds,nip,' . $id,
        ]);

        try {
            DB::transaction(function () use ($request, $pegawai) {
                // Update Biodata
                $pegawai->update([
                    'nama'    => $request->nama,
                    'nip'     => $request->nip,
                    'jabatan' => $request->jabatan,
                    'no_hp'   => $request->no_hp
                ]);

                // Update Akun User (Nama & Role)
                if($pegawai->user) {
                    if ($request->jabatan === 'Administrator') {
                        $role = 'Admin';
                    } else {
                        $role = $request->jabatan;
                    }

                    $pegawai->user->update([
                        'name' => $request->nama,
                        'role' => $role
                        // Note: Password tidak diupdate disini, pake fitur reset aja biar aman
                    ]);
                }
            });

            return back()->with('success', 'Data pegawai diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $pegawai = PegawaiKcd::findOrFail($id);
        
        if($pegawai->user) {
            $pegawai->user->delete();
        } else {
            $pegawai->delete();
        }

        return back()->with('success', 'Pegawai dan akun login berhasil dihapus.');
    }

    public function resetPassword($id)
    {
        $pegawai = PegawaiKcd::findOrFail($id);
        
        if($pegawai->user) {
            $pegawai->user->update(['password' => Hash::make('kcd123')]);
            return back()->with('success', 'Password direset menjadi: <b>kcd123</b>');
        }

        return back()->with('error', 'User tidak ditemukan.');
    }
}