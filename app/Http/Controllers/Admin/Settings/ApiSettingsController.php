<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\User; // Digunakan untuk simulasi pengambilan token

class ApiSettingsController extends Controller
{
    // ID user khusus yang tokennya digunakan untuk Web Service.
    const SYSTEM_USER_ID = 1;
    const DOMAIN_KEY = 'app_domain';

    /**
     * Menampilkan halaman Pengaturan Web Service.
     */
    public function index()
    {
        // --- 1. Ambil Data Setting (Simulasi dari Database) ---
        $settings = $this->getSettings();

        // --- 2. Ambil Token Aktif (Simulasi dari System User) ---
        $systemUser = User::find(self::SYSTEM_USER_ID);

        // Cek apakah user ada DAN model User memiliki metode 'tokens()' (yang seharusnya disediakan oleh HasApiTokens)
        if ($systemUser && method_exists($systemUser, 'tokens')) {
            $currentToken = $systemUser->tokens()->latest()->first();
        } else {
            // Kasus jika model User belum menggunakan HasApiTokens atau user tidak ditemukan.
            $currentToken = null;
            // Tambahkan logging di sini jika perlu untuk debugging di server
            // \Log::warning("System user ID " . self::SYSTEM_USER_ID . " not found or User model is missing HasApiTokens trait.");
        }

        // Sensor token untuk keamanan
        $settings['current_api_token'] = $currentToken
            ? substr($currentToken->token, 0, 4) . '****************' . substr($currentToken->token, -4)
            : 'Belum ada token aktif. Silakan hubungi admin atau pastikan model User menggunakan HasApiTokens.';

        return view('admin.settings.api_config', compact('settings'));
    }

    // --- Fungsi Helper (Simulasi Database Setting) ---
    private function getSettings()
    {
        // Ganti bagian ini dengan query database Anda yang sebenarnya dari tabel Setting
        return [
            self::DOMAIN_KEY => 'https://aplikasisekolahku.com',
        ];
    }
}
