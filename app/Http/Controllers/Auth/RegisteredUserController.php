<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Siswa;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
{
    $request->validate([
        'nik' => ['required', 'digits:16', 'exists:siswas,nik'],
        'username' => ['required','unique:penggunas,username'],
        'password' => ['required'],
    ]);

    $siswa = Siswa::where('nik', $request->nik)->firstOrFail();

    $exists = Pengguna::where('peserta_didik_id', $siswa->peserta_didik_id)
        ->where('peran_id_str', 'wali')
        ->exists();

    if ($exists) {
        return back()->withErrors([
            'nik' => 'Siswa ini sudah memiliki akun wali'
        ]);
    }

    Pengguna::create([
        'pengguna_id' => (string) \Str::uuid(),
        'username' => $request->username,
        'password' => \Hash::make($request->password),
        'peran_id_str' => 'wali',
        'peserta_didik_id' => $siswa->peserta_didik_id,
    ]);

    return redirect()->route('login')
        ->with('success', 'Akun berhasil dibuat.');
}





    public function cekNik(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'digits:16']
        ]);

        $siswa = Siswa::where('nik', $request->nik)->first();

        if (!$siswa) {
            return response()->json([
                'status' => false,
                'message' => 'Siswa dengan NIK tersebut tidak ditemukan'
            ], 200);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'nama' => $siswa->nama,
                'kelas' => $siswa->nama_rombel ?? '-',
                'tanggal_lahir' => $siswa->tanggal_lahir,
                'ayah' => $siswa->nama_ayah,
                'ibu' => $siswa->nama_ibu,
            ]
        ], 200);
    }



}
