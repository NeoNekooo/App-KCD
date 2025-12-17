<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class ApiSettingsController extends Controller
{
    /**
     * Menampilkan halaman input token
     */
    public function index()
    {
        // Ambil data token dari database
        // Kita gunakan first() agar bisa mengecek apakah data ada atau null
        $setting = Setting::where('key', 'api_sync_token')->first();

        // Jika belum ada, nilainya kosong (jangan diisi random)
        $tokenValue = $setting ? $setting->value : '';

        return view('admin.settings.webservice.index', compact('tokenValue'));
    }

    /**
     * Menyimpan token yang diinput manual oleh user
     */
    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'api_token' => 'required|string',
        ], [
            'api_token.required' => 'Token Webservice Dapodik wajib diisi.',
        ]);

        // Simpan atau Update ke database
        Setting::updateOrCreate(
            ['key' => 'api_sync_token'], // Pencarian berdasarkan key ini
            ['value' => $request->api_token] // Nilai yang diupdate
        );

        return back()->with('success', 'Token Webservice Dapodik berhasil disimpan.');
    }
}
