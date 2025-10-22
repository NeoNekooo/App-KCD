<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Berapa kali job dapat dicoba sebelum dianggap gagal.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Waktu (dalam detik) untuk menunggu sebelum mencoba kembali job yang gagal.
     *
     * @var int
     */
    public $backoff = 60; // Job akan dicoba lagi setelah 1 menit

    protected $nomorTujuan;
    protected $pesan;

    /**
     * Membuat instance job baru.
     *
     * @param string $nomorTujuan Nomor WhatsApp tujuan
     * @param string $pesan Isi pesan yang akan dikirim
     */
    public function __construct(string $nomorTujuan, string $pesan)
    {
        $this->nomorTujuan = $nomorTujuan;
        $this->pesan = $pesan;
    }

    /**
     * Mengeksekusi job.
     * Logika utama untuk mengirim pesan via Wablas ada di sini.
     */
    public function handle(): void
    {
        // Ambil konfigurasi dari file .env
        $apiToken = config('services.wablas.token');
        $apiDomain = config('services.wablas.domain');

        // Validasi dasar: jangan kirim jika token atau domain tidak ada
        if (!$apiToken || !$apiDomain) {
            Log::error('Wablas Error: WABLAS_API_TOKEN atau WABLAS_DOMAIN belum diatur di .env');
            return;
        }

        // Siapkan data untuk dikirim
        $payload = [
            'phone' => $this->formatPhoneNumber($this->nomorTujuan),
            'message' => $this->pesan,
        ];

        // Kirim request ke API Wablas menggunakan Laravel HTTP Client
        $response = Http::withHeaders([
            'Authorization' => $apiToken,
        ])->post("{$apiDomain}/api/send-message", $payload);


        // Catat ke log jika pengiriman gagal untuk memudahkan debugging
        if (!$response->successful() || (isset($response->json()['status']) && $response->json()['status'] === 'error')) {
            Log::error('Gagal mengirim notifikasi WA ke ' . $payload['phone'], [
                'response' => $response->body()
            ]);
             // Lemparkan exception agar job dianggap gagal dan bisa di-retry
            $response->throw();
        } else {
             Log::info('Sukses mengirim notifikasi WA ke ' . $payload['phone']);
        }
    }

    /**
     * Helper function untuk memformat nomor telepon ke format internasional (62).
     * Contoh: 0812... -> 62812...
     *
     * @param string $number
     * @return string
     */
    private function formatPhoneNumber(string $number): string
    {
        // Hapus karakter selain angka
        $cleaned = preg_replace('/[^0-9]/', '', $number);

        // Jika nomor diawali dengan '0', ganti dengan '62'
        if (substr($cleaned, 0, 1) == '0') {
            return '62' . substr($cleaned, 1);
        }

        // Jika sudah diawali dengan '62', langsung kembalikan
        if (substr($cleaned, 0, 2) == '62') {
            return $cleaned;
        }

        // Untuk kasus lain, asumsikan nomor valid dan tambahkan '62'
        return '62' . $cleaned;
    }
}