<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\AttendanceSession; // <-- Import model baru
use Illuminate\Http\Request;
use Carbon\Carbon; // <-- Import Carbon untuk manajemen waktu
use App\Models\AttendanceException;

class AttendanceController extends Controller
{
    /**
     * Menyimpan catatan absensi baru dari hasil scan QR Code.
     */
    /**
     * Menyimpan catatan absensi baru dari hasil scan QR Code.
     */
    public function store(Request $request)
    {
        // 1. Validasi request, pastikan ada 'nis' yang dikirim
        $request->validate([
            'nis' => 'required|string|exists:students,nis',
        ]);

        // 2. Cari sesi absensi yang aktif untuk hari ini
        $today = Carbon::today();
        $session = AttendanceSession::where('session_date', $today)->first();

        // Jika tidak ada sesi yang dibuat untuk hari ini, kembalikan error
        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal: Sesi absensi untuk hari ini belum dibuka oleh admin.',
            ], 404); // 404 Not Found
        }

        // 3. Cari siswa berdasarkan NIS
        $student = Student::where('nis', $request->nis)->first();

        // 4. Cek apakah siswa sudah absen hari ini
        $alreadyAttended = Attendance::where('student_id', $student->id)
                                      ->whereDate('created_at', $today)
                                      ->exists();

        if ($alreadyAttended) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal: ' . $student->name . ' sudah tercatat absen hari ini.',
                'data' => $student,
            ], 409); // 409 Conflict
        }
        
        // 5. Tentukan status berdasarkan pengecualian atau waktu
        $status = '';
        
        // 5a. Cek apakah siswa punya pengecualian untuk hari ini
        $hasException = AttendanceException::where('student_id', $student->id)
                                           ->whereDate('exception_date', $today)
                                           ->exists();
        
        if ($hasException) {
            // Jika punya pengecualian, statusnya langsung "Hadir" tidak peduli jam berapa
            $status = 'Hadir';
        } else {
            // Jika tidak ada pengecualian, jalankan logika waktu seperti biasa
            $now = Carbon::now();
            $cutoffTime = Carbon::parse($session->session_date . ' ' . $session->end_time);
            
            $status = $now->lte($cutoffTime) ? 'Hadir' : 'Terlambat';
        }

        // 6. Catat absensi baru DENGAN STATUS yang sudah ditentukan
        Attendance::create([
            'student_id' => $student->id,
            'status' => $status,
        ]);

        // 7. Kembalikan respons sukses dengan statusnya
        return response()->json([
            'success' => true,
            'message' => 'Berhasil: ' . $student->name . ' telah dicatat sebagai ' . $status,
            'data' => $student,
        ], 201); // 201 Created
    }
}