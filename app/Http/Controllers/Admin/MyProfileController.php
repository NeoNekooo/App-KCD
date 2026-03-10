<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PegawaiKcd;
use App\Models\JabatanKcd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MyProfileController extends Controller
{
    /**
     * Display the user's own profile.
     */
    public function show()
    {
        $userId = Auth::id();
        $pegawai = PegawaiKcd::with('user')->where('user_id', $userId)->firstOrFail();
        
        // Jabatans are needed for the dropdown if the user happens to be an Admin
        $jabatans = Auth::user()->role === 'Admin' ? JabatanKcd::all() : [];

        return view('admin.kepegawaian_kcd.show', compact('pegawai', 'jabatans'));
    }

    /**
     * Update the user's own profile.
     */
    public function update(Request $request)
    {
        $userId = Auth::id();
        $pegawai = PegawaiKcd::where('user_id', $userId)->firstOrFail();
        $id = $pegawai->id; // Get the ID for validation rules

        $rules = [
            'nama'          => 'required|string|max:255',
            'nik'           => 'nullable|numeric|digits:16|unique:pegawai_kcds,nik,' . $id,
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password'      => 'nullable|string|min:6',
        ];

        // Admin-specific rules, in case an admin is editing their own profile
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

                if ($request->hasFile('foto')) {
                    if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
                        Storage::disk('public')->delete($pegawai->foto);
                    }
                    $dataUpdate['foto'] = $request->file('foto')->store('foto_pegawai', 'public');
                }

                if (Auth::user()->role === 'Admin') {
                    $jabatan = JabatanKcd::find($request->jabatan_kcd_id);
                    $dataUpdate['jabatan_kcd_id'] = $jabatan->id;
                    $dataUpdate['jabatan'] = $jabatan->nama; 
                    $dataUpdate['nip'] = $request->nip;
                }

                $pegawai->update($dataUpdate);

                if ($pegawai->user) {
                    $userUpdate = ['name' => $request->nama];

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

    /**
     * Change the user's own password.
     */
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
