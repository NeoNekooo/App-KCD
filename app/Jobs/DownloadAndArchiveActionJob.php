<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\DokumenLayanan;

class DownloadAndArchiveActionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $pengajuanId;
    protected $namaDokumen;

    /**
     * Create a new job instance.
     */
    public function __construct(string $url, int $pengajuanId, string $namaDokumen)
    {
        $this->url = $url;
        $this->pengajuanId = $pengajuanId;
        $this->namaDokumen = $namaDokumen;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // 1. Validasi URL (dasar)
            if (!filter_var($this->url, FILTER_VALIDATE_URL)) {
                Log::warning("URL tidak valid untuk di-job: {$this->url} untuk Pengajuan ID: {$this->pengajuanId}");
                return;
            }

            $response = Http::timeout(60)->get($this->url);

            // 2. Cek jika download gagal
            if (!$response->successful()) {
                Log::warning("Gagal download dari job: {$this->url} (Status: {$response->status()})");
                return;
            }

            // 3. Validasi Ukuran File (limit 10MB)
            $fileSize = strlen($response->body());
            if ($fileSize > 10 * 1024 * 1024) {
                Log::warning("Ukuran file melebihi 10MB dari job: {$this->url}");
                return;
            }

            // 4. Validasi Tipe File (MIME Type)
            $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $mimeType = $response->header('Content-Type');
            if (!in_array($mimeType, $allowedMimeTypes)) {
                Log::warning("Tipe file tidak diizinkan dari job: {$mimeType} dari URL: {$this->url}");
                return;
            }

            $mimeToExt = [
                'application/pdf' => 'pdf',
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'application/msword' => 'doc',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            ];
            $extension = $mimeToExt[$mimeType] ?? 'dat';

            // 5. Buat nama file & path unik
            $filename = Str::random(40) . '.' . $extension;
            $path = "arsip_dokumen/{$this->pengajuanId}/{$filename}";

            // 6. Simpan file ke storage lokal
            Storage::disk('public')->put($path, $response->body());

            // 7. Simpan record ke database
            DokumenLayanan::create([
                'pengajuan_sekolah_id' => $this->pengajuanId,
                'nama_dokumen'         => $this->namaDokumen,
                'path_dokumen'         => $path,
            ]);

        } catch (\Exception $e) {
            Log::error("Job Gagal: Gagal memproses arsip file dari URL {$this->url}. Error: " . $e->getMessage());
        }
    }
}
