<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\PegawaiKcd;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; 

class PegawaiKcdController extends Controller
{
    /**
     * Tampilkan daftar pegawai (Admin)
     */
    public function index()
    {
        // Karena sudah diproteksi middleware 'check_menu:kepegawaian-kcd' di route,
        // yang masuk sini PASTI Admin (atau yang punya hak akses menu tsb).
        
        $pegawais = PegawaiKcd::with('user')->latest()->paginate(10);
        return view('admin.kepegawaian_kcd.index', compact('pegawais'));
    }

    /**
     * [BARU] Menampilkan Profil Pegawai yang sedang Login (Tanpa ID di URL)
     * Diakses via menu 'Profil Saya'
     */
    public function showMe()
    {
        $userId = Auth::id();
        
        // Cari data pegawai berdasarkan user yang login
        $pegawai = PegawaiKcd::with('user')->where('user_id', $userId)->first();

        if (!$pegawai) {
            // Jika belum ada data pegawai (misal Admin baru), bisa redirect atau tampilkan error
            return abort(404, 'Data profil kepegawaian Anda belum dihubungkan. Hubungi Administrator Utama.');
        }

        // Gunakan view yang sama dengan 'show' biasa
        return view('admin.kepegawaian_kcd.show', compact('pegawai'));
    }

    /**
     * Tambah Pegawai Baru + Akun Login Otomatis
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'jabatan'       => 'required|string',
            'nip'           => 'nullable|string|max:50|unique:pegawai_kcds,nip',
            'nik'           => 'nullable|numeric|digits:16|unique:pegawai_kcds,nik',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir'  => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'no_hp'         => 'nullable|string|max:20',
            'email_pribadi' => 'nullable|email',
            'alamat'        => 'nullable|string',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password'      => 'nullable|string|min:6',
        ]);

        try {
            DB::transaction(function () use ($request) {
                
                $fotoPath = null;
                if ($request->hasFile('foto')) {
                    $fotoPath = $request->file('foto')->store('foto_pegawai', 'public');
                }

                // Generate Akun Login
                $username = $request->nip 
                    ? $request->nip 
                    : Str::slug(explode(' ', $request->nama)[0]) . rand(100, 999);
                
                $emailLogin = $request->email_pribadi ?: $username . '@kcd.system';
                $passwordFix = $request->password ?: 'kcd123';
                $role = ($request->jabatan === 'Administrator') ? 'Admin' : 'Pegawai'; 

                $user = User::create([
                    'name'     => $request->nama,
                    'username' => $username,
                    'email'    => $emailLogin,
                    'password' => Hash::make($passwordFix),
                    'role'     => $role,
                ]);

                PegawaiKcd::create([
                    'user_id'       => $user->id,
                    'nama'          => $request->nama,
                    'nip'           => $request->nip,
                    'nik'           => $request->nik,
                    'jabatan'       => $request->jabatan,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tempat_lahir'  => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'no_hp'         => $request->no_hp,
                    'email_pribadi' => $request->email_pribadi,
                    'alamat'        => $request->alamat,
                    'foto'          => $fotoPath,
                ]);
            });

            return back()->with('success', 'Profil Pegawai berhasil dibuat & Akun Login aktif!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan Detail Profil (Show by ID) - Untuk Admin melihat detail orang lain
     */
    public function show($id)
    {
        $pegawai = PegawaiKcd::with('user')->findOrFail($id);
        
        // SECURITY: Cek apakah user berhak melihat profil ini
        if (Auth::user()->role !== 'Admin' && $pegawai->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke profil ini.');
        }

        return view('admin.kepegawaian_kcd.show', compact('pegawai'));
    }

    /**
     * Update Data Profil + Sinkronisasi Akun Login
     */
    public function update(Request $request, $id)
    {
        $pegawai = PegawaiKcd::findOrFail($id);

        // SECURITY Check
        if (Auth::user()->role !== 'Admin' && $pegawai->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'nama'          => 'required|string|max:255',
            'nik'           => 'nullable|numeric|digits:16|unique:pegawai_kcds,nik,' . $id,
            'nip'           => 'nullable|string|max:50|unique:pegawai_kcds,nip,' . $id,
            'jabatan'       => 'required|string',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password'      => 'nullable|string|min:6', 
        ]);

        try {
            DB::transaction(function () use ($request, $pegawai) {
                
                $dataUpdate = $request->except(['foto', 'password']); 

                if ($request->hasFile('foto')) {
                    if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
                        Storage::disk('public')->delete($pegawai->foto);
                    }
                    $dataUpdate['foto'] = $request->file('foto')->store('foto_pegawai', 'public');
                }

                $pegawai->update($dataUpdate);

                if ($pegawai->user) {
                    $userUpdate = ['name' => $request->nama];

                    if ($request->email_pribadi) {
                        $userUpdate['email'] = $request->email_pribadi;
                    }

                    // Hanya Admin yang bisa ubah username via NIP
                    if (Auth::user()->role === 'Admin' && $request->nip) {
                        $userUpdate['username'] = $request->nip;
                    }

                    if ($request->filled('password')) {
                        $userUpdate['password'] = Hash::make($request->password);
                    }

                    // Update Role (Hanya Admin)
                    if (Auth::user()->role === 'Admin') {
                        if ($request->jabatan === 'Administrator') {
                            $userUpdate['role'] = 'Admin';
                        } elseif ($request->jabatan !== 'Administrator' && $pegawai->user->role === 'Admin') {
                             $userUpdate['role'] = 'Pegawai'; 
                        }
                    }

                    $pegawai->user->update($userUpdate);
                }
            });

            return back()->with('success', 'Data Profil & Akun berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Pegawai (Hanya Admin)
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Hanya Admin yang bisa menghapus pegawai.');
        }

        $pegawai = PegawaiKcd::findOrFail($id);
        
        if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
            Storage::disk('public')->delete($pegawai->foto);
        }

        if ($pegawai->user) {
            $pegawai->user->delete();
        } else {
            $pegawai->delete();
        }

        return back()->with('success', 'Pegawai dihapus permanen.');
    }

    // --- FITUR TAMBAHAN ---

    public function resetPassword($id)
    {
        if (Auth::user()->role !== 'Admin') abort(403);

        $pegawai = PegawaiKcd::findOrFail($id);
        
        if($pegawai->user) {
            $pegawai->user->update(['password' => Hash::make('kcd123')]);
            return back()->with('success', 'Password direset menjadi: <b>kcd123</b>');
        }
        return back()->with('error', 'User tidak ditemukan.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed', 
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah!'])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password akun kamu berhasil diperbarui!');
    }
}