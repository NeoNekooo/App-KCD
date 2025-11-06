<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('absensi:alfa')
    ->weekdays() // Hanya berjalan di hari kerja (Senin - Jumat)
    ->dailyAt('12:00') // Jalankan setiap hari pada jam 12 siang
    ->timezone('Asia/Jakarta'); // Sangat disarankan untuk menentukan zona waktu

Schedule::command('queue:work --stop-when-empty')
    ->everyMinute()
    ->withoutOverlapping();