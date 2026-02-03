<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\JabatanKcd;
use App\Models\PegawaiKcd;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PegawaiKcdController extends Controller
{
    public function index(Request $request)
    {
        $query = PegawaiKcd::with('user', 'jabatanKcd');

        // Logic Sorting
        switch ($request->get('sort')) {
            case 'alpha':
                $query->orderBy('nama', 'asc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                // Default sorting (terlama)
                $query->orderBy('created_at', 'asc');
                break;
        }

        $pegawais = $query->paginate(10)->appends($request->query());
        $jabatans = JabatanKcd::all();

        return view('admin.kepegawaian_kcd.index', compact('pegawais', 'jabatans', 'request'));
    }

    public function showMe()
    {
        $userId = Auth::id();
        $pegawai = PegawaiKcd::with('user')->where('user_id', $userId)->first();

        if (!$pegawai) {
            return abort(404, 'Data profil kepegawaian Anda belum dihubungkan.');
        }

        return view('admin.kepegawaian_kcd.show', compact('pegawai'));
    }

    // --- STORE: SIMPAN DATA BARU ---
    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'jabatan_kcd_id' => 'required|exists:jabatan_kcd,id',
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
                
                $jabatan = JabatanKcd::find($request->jabatan_kcd_id);

                // 1. Handle Upload Foto
                $fotoPath = null;
                if ($request->hasFile('foto')) {
                    $fotoPath = $request->file('foto')->store('foto_pegawai', 'public');
                }

                // 2. Generate Username & Email Login
                $username = $request->nip 
                    ? $request->nip 
                    : Str::slug(explode(' ', $request->nama)[0]) . rand(100, 999);
                
                $emailLogin = $request->email_pribadi ?: $username . '@kcd.system';
                $passwordFix = $request->password ?: 'kcd123';

                // 3. BUAT USER (AKUN LOGIN)
                $user = User::create([
                    'name'     => $request->nama,
                    'username' => $username,
                    'email'    => $emailLogin,
                    'password' => Hash::make($passwordFix),
                    'role'     => $jabatan->role, 
                ]);

                // 4. BUAT DATA PEGAWAI (BIODATA)
                $pegawai = PegawaiKcd::create([
                    'user_id'       => $user->id,
                    'nama'          => $request->nama,
                    'nip'           => $request->nip,
                    'nik'           => $request->nik,
                    'jabatan'       => $jabatan->nama, // backward compatibility
                    'jabatan_kcd_id' => $jabatan->id,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tempat_lahir'  => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'no_hp'         => $request->no_hp,
                    'email_pribadi' => $request->email_pribadi,
                    'alamat'        => $request->alamat,
                    'foto'          => $fotoPath,
                ]);

                // 5. UPDATE RELASI DI TABEL USER (Agar menu muncul)
                $user->update(['pegawai_kcd_id' => $pegawai->id]);
            });

            $jabatan = JabatanKcd::find($request->jabatan_kcd_id);
            return back()->with('success', "Pegawai berhasil dibuat dengan Role: " . $jabatan->role);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $pegawai = PegawaiKcd::with('user')->findOrFail($id);
        $jabatans = JabatanKcd::all();

        // Validasi Akses: Hanya Admin atau pemilik data yang boleh lihat
        if (Auth::user()->role !== 'Admin' && $pegawai->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke profil ini.');
        }

        return view('admin.kepegawaian_kcd.show', compact('pegawai', 'jabatans'));
    }

    // --- UPDATE: EDIT DATA ---
        public function update(Request $request, $id)
        {
            $pegawai = PegawaiKcd::findOrFail($id);
    
            // Validasi Akses
            if (Auth::user()->role !== 'Admin' && $pegawai->user_id !== Auth::id()) {
                abort(403, 'Akses Ditolak.');
            }
    
            $rules = [
                'nama'          => 'required|string|max:255',
                'nik'           => 'nullable|numeric|digits:16|unique:pegawai_kcds,nik,' . $id,
                'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'password'      => 'nullable|string|min:6',
            ];
    
            if (Auth::user()->role === 'Admin') {
                $rules['nip'] = 'nullable|string|max:50|unique:pegawai_kcds,nip,' . $id;
                $rules['jabatan_kcd_id'] = 'required|exists:jabatan_kcd,id';
            }
    
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                if ($validator->errors()->has('nik')) {
                    $nikError = $validator->errors()->first('nik');
                    return redirect()->back()->withInput()->with('error', $nikError);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }
    
            try {
                DB::transaction(function () use ($request, $pegawai) {
                    
                    $allowedFields = [
                        'nama', 'email_pribadi', 'no_hp', 'alamat', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir'
                    ];
                    $dataUpdate = $request->only($allowedFields);
    
                    // 1. Handle Ganti Foto
                    if ($request->hasFile('foto')) {
                        if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
                            Storage::disk('public')->delete($pegawai->foto);
                        }
                        $dataUpdate['foto'] = $request->file('foto')->store('foto_pegawai', 'public');
                    }
    
                    // Update protected fields only if Admin
                    if (Auth::user()->role === 'Admin') {
                        $jabatan = JabatanKcd::find($request->jabatan_kcd_id);
                        $dataUpdate['jabatan_kcd_id'] = $jabatan->id;
                        $dataUpdate['jabatan'] = $jabatan->nama; 
                        $dataUpdate['nip'] = $request->nip;
                    }
    
                    // 2. Update Data Pegawai
                    $pegawai->update($dataUpdate);
    
                    // 3. Update Akun User (Sinkronisasi)
                    if ($pegawai->user) {
                        $userUpdate = [
                            'name' => $request->nama,
                        ];
    
                        if (Auth::user()->role === 'Admin') {
                            $jabatan = JabatanKcd::find($request->jabatan_kcd_id);
                            $userUpdate['role'] = $jabatan->role;
                            if ($request->nip) {
                                $userUpdate['username'] = $request->nip;
                            }
                        }
    
                        if ($request->email_pribadi) {
                            $userUpdate['email'] = $request->email_pribadi;
                        }
    
                        // Update Password jika diisi
                        if ($request->filled('password')) {
                            $userUpdate['password'] = Hash::make($request->password);
                        }
    
                        $pegawai->user->update($userUpdate);
                    }
                });
    
                return back()->with('success', 'Data Profil & Akun berhasil diperbarui.');
    
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal update: ' . $e->getMessage());
            }
        }
    public function destroy($id)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Hanya Admin yang bisa menghapus pegawai.');
        }

        $pegawai = PegawaiKcd::findOrFail($id);
        
        // Hapus file foto
        if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
            Storage::disk('public')->delete($pegawai->foto);
        }

        // Hapus akun user
        if ($pegawai->user) {
            $pegawai->user->delete();
        } 
        
        // Hapus data pegawai
        $pegawai->delete();

        return back()->with('success', 'Pegawai dihapus permanen.');
    }

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