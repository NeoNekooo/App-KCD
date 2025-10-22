<?php

namespace App\Events;

use App\Models\AbsensiSiswa; // Pastikan ini ada
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // Ini penting!
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AbsensiBerhasil implements ShouldBroadcast // Implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Ini adalah "paket" yang akan dibawa kurir, yaitu data absensi
    public $absensi;

    /**
     * Buat instance event baru.
     */
    public function __construct(AbsensiSiswa $absensi)
    {
        // Saat kurir dipanggil, kita titipkan "paket" datanya
        $this->absensi = $absensi;
    }

    /**
     * Tentukan channel broadcast. Ini adalah "alamat tujuan".
     */
    public function broadcastOn(): array
    {
        // Kita beri nama alamatnya 'aktivitas-absensi'
        return [
            new Channel('aktivitas-absensi'),
        ];
    }
}