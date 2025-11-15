<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Student; // <-- Import model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Import untuk file handling
use Illuminate\Support\Facades\Validator; // <-- Import untuk validasi

class StudentController extends Controller
{
    // READ ALL
    public function index()
    {
        $students = Student::latest()->paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'Daftar data siswa',
            'data' => $students
        ], 200);
    }

    // CREATE
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nis' => 'required|string|unique:students,nis|max:15',
            'class' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            // Simpan gambar ke storage/app/public/photos/students
            $photoPath = $request->file('photo')->store('photos/students', 'public');
        }

        $student = Student::create([
            'name' => $request->name,
            'nis' => $request->nis,
            'class' => $request->class,
            'photo' => $photoPath,
            // Isi kolom lain jika ada inputnya
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
        ]);

        return to_route('students.page')->with('message', 'Siswa berhasil ditambahkan!');
    }

    // READ ONE
    public function show(Student $student)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail data siswa',
            'data' => $student
        ], 200);
    }

    // UPDATE
    public function update(Request $request, Student $student)
{
    // 1. Validasi input
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        // Rule 'unique' harus mengabaikan NIS milik siswa yang sedang diedit
        'nis' => 'required|string|max:15|unique:students,nis,' . $student->id,
        'class' => 'required|string|max:255',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // 2. Siapkan data untuk diupdate
    $updateData = $request->except('photo');

    // 3. Cek apakah ada foto baru yang diupload
    if ($request->hasFile('photo')) {
        // Hapus foto lama jika ada
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        // Upload foto baru dan simpan path-nya
        $photoPath = $request->file('photo')->store('photos/students', 'public');
        $updateData['photo'] = $photoPath;
    }

    // 4. Update data siswa di database
    $student->update($updateData);

    // 5. Kembalikan respons redirect dengan pesan sukses
    return to_route('students.page')->with('message', 'Data siswa berhasil diperbarui!');
}
    
    // DELETE
    public function destroy(Student $student)
    {
        // Hapus foto dari storage jika ada
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        return to_route('students.page')->with('message', 'Siswa berhasil dihapus.');
    }

    public function sync(Request $request)
{
    // 1. Validasi data masuk (sudah bagus)
    $validator = Validator::make($request->all(), [
        '*.peserta_didik_id' => 'required|string',
        '*.nama' => 'required|string',
        '*.nisn' => 'nullable|string',
        '*.nama_rombel' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Data yang dikirim tidak valid.',
            'details' => $validator->errors()->first(),
        ], 422);
    }

    $dataFromDapodik = $request->all();
    $totalDiterima = count($dataFromDapodik);
    
    $createdCount = 0;
    $updatedCount = 0;
    $skippedCount = 0;

    foreach ($dataFromDapodik as $siswaDapodik) {
        if (empty($siswaDapodik['nisn'])) {
            $skippedCount++;
            continue;
        }
        
        // Data yang akan dimasukkan atau diperbarui
        $studentData = [
            'name'  => $siswaDapodik['nama'],
            'nis'   => $siswaDapodik['nisn'],
            'class' => $siswaDapodik['nama_rombel'],
            'peserta_didik_id' => $siswaDapodik['peserta_didik_id'],
        ];

        // Cari berdasarkan peserta_didik_id dulu, jika tidak ada, cari berdasarkan nis
        $student = Student::where('peserta_didik_id', $siswaDapodik['peserta_didik_id'])
                          ->orWhere('nis', $siswaDapodik['nisn'])
                          ->first();

        if ($student) {
            // Jika siswa ditemukan, update datanya
            $student->update($studentData);
            if ($student->wasChanged()) {
                $updatedCount++;
            }
        } else {
            // Jika tidak ditemukan sama sekali, buat data baru
            Student::create($studentData);
            $createdCount++;
        }
    }

    $detailsMessage = "Baru: {$createdCount}, Diperbarui: {$updatedCount}, Dilewati: {$skippedCount}.";

    return response()->json([
        'message' => 'Sinkronisasi selesai.',
        'details' => $detailsMessage,
    ]);

    
}
}