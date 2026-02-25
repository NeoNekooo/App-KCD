<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SchoolSyncController extends Controller
{
    public function handle(Request $request, $table)
    {
        // 1. Ambil Data
        $input = $request->input('rows');

        if (empty($input) || !is_array($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Format data tidak valid. Harapkan key "rows" berisi array data.'
            ], 400);
        }

        // 2. Normalisasi Nama Tabel
        $tableName = Str::plural($table); 

        // 3. Auto-Migration (Buat tabel jika belum ada)
        $firstRow = (array) $input[0];
        $columns = array_keys($firstRow);

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function ($blueprint) use ($columns) {
                $blueprint->id(); 
                foreach ($columns as $col) {
                    if ($col === 'id') continue;
                    $this->defineColumn($blueprint, $col);
                }
                $blueprint->timestamps();
            });
        } else {
            $existingCols = Schema::getColumnListing($tableName);
            $newCols = array_diff($columns, $existingCols);
            if (!empty($newCols)) {
                Schema::table($tableName, function ($blueprint) use ($newCols) {
                    foreach ($newCols as $col) {
                        $this->defineColumn($blueprint, $col);
                    }
                });
            }
        }

        // 4. Tentukan Primary Key untuk UpdateOrInsert
        $primaryKey = 'id';
        if (isset($firstRow[$table . '_id'])) {
            $primaryKey = $table . '_id';
        } elseif (isset($firstRow[Str::singular($table) . '_id'])) {
            $primaryKey = Str::singular($table) . '_id';
        }

        $processed = 0;
        $errors = [];

        // Hapus Transaction block agar error satu baris tidak membatalkan semua
        // DB::beginTransaction(); 

        foreach ($input as $index => $row) {
            try {
                $row = (array) $row;
                
                // Encode array/json
                foreach ($row as $k => $v) {
                    if (is_array($v)) $row[$k] = json_encode($v);
                }

                // (Opsional tapi penting): Tambahkan updated_at ke tiap baris agar tahu kapan baris ini diupdate
                if (!isset($row['updated_at'])) {
                    $row['updated_at'] = now();
                }

                $conditions = [];
                if (isset($row[$primaryKey])) {
                    $conditions[$primaryKey] = $row[$primaryKey];
                } elseif (isset($row['id'])) {
                     $conditions['id'] = $row['id'];
                }

                if (empty($conditions)) {
                    // Jika tidak ada ID, insert langsung
                    if (!isset($row['created_at'])) $row['created_at'] = now(); // Set created_at
                    DB::table($tableName)->insert($row);
                    $processed++;
                    continue;
                }

                // Coba UpdateOrInsert Normal
                try {
                    DB::table($tableName)->updateOrInsert($conditions, $row);
                    $processed++;
                } catch (\Exception $e) {
                    // HANDLING KHUSUS: Duplicate Entry (biasanya email/username di tabel penggunas)
                    if (str_contains($e->getMessage(), 'Duplicate entry') && ($table === 'penggunas' || $table === 'pengguna')) {
                        // Coba cari berdasarkan email jika ada
                        if (isset($row['email']) && !empty($row['email'])) {
                            DB::table($tableName)->updateOrInsert(['email' => $row['email']], $row);
                            $processed++;
                        } else {
                            throw $e; // Lempar ulang jika tidak ada email
                        }
                    } else {
                        throw $e; // Lempar ulang error lain
                    }
                }

            } catch (\Exception $e) {
                // Catat error tapi jangan stop proses
                $errors[] = "Row #$index: " . $e->getMessage();
            }
        }

        // DB::commit();

        // 🔥 5. REKAM LOG HISTORY SINKRONISASI SEKOLAH 🔥
        $this->recordSyncHistory($request, $firstRow, $tableName, $processed, count($errors), $errors);

        // Jika ada error, kirim status 207 (Multi-Status) atau 200 dengan info error
        // Kita pakai 200 saja biar client tidak panic, tapi sertakan pesan error di message jika banyak
        
        $msg = "Berhasil memproses $processed data.";
        if (count($errors) > 0) {
            $msg .= " Gagal: " . count($errors) . " data. (Cth: " . Str::limit($errors[0], 100) . ")";
        }

        return response()->json([
            'success' => true, // Tetap true agar client menganggap batch ini selesai
            'message' => $msg,
            'details' => $errors
        ]);
    }

    /**
     * 🔥 FUNGSI BARU: Mencatat History Sinkronisasi Lengkap Dengan Info Sekolah
     */
    private function recordSyncHistory($request, $firstRow, $tableName, $processedCount, $errorCount, $errors)
    {
        // 1. Ekstrak Identitas Sekolah
        // Coba ambil dari JSON Payload root (request body), kalau gak ada, cari di dalam isi datanya (firstRow)
        $npsn = $request->input('npsn') ?? ($firstRow['npsn'] ?? null);
        $namaSekolah = $request->input('nama_sekolah') ?? ($firstRow['nama_sekolah'] ?? 'Sekolah Tidak Teridentifikasi');

        // 2. Buat tabel 'sync_logs' jika belum ada (Skema Baru untuk History)
        if (!Schema::hasTable('sync_logs')) {
            Schema::create('sync_logs', function ($blueprint) {
                $blueprint->id();
                $blueprint->string('npsn')->nullable()->index(); // NPSN Sekolah
                $blueprint->string('nama_sekolah')->nullable()->index(); // Nama Sekolah
                $blueprint->string('table_name'); // Tabel apa yang disync (misal: gurus, siswas)
                $blueprint->integer('total_processed')->default(0); // Berapa data yg sukses
                $blueprint->integer('total_failed')->default(0);    // Berapa yg gagal
                $blueprint->text('error_details')->nullable();      // Catatan error (kalo ada)
                $blueprint->string('ip_address')->nullable();       // IP Pengirim buat keamanan KCD
                $blueprint->timestamps(); // created_at otomatis jadi "Waktu Sync"
            });
        }

        // 3. Simpan SEBAGAI HISTORY (Insert)
        // Kita pakai insert, jadi kalau besok sekolah ini sync lagi, catatannya akan nambah, bukan nimpa yg kemarin.
        DB::table('sync_logs')->insert([
            'npsn'            => $npsn,
            'nama_sekolah'    => $namaSekolah,
            'table_name'      => $tableName,
            'total_processed' => $processedCount,
            'total_failed'    => $errorCount,
            // Simpan maksimal 5 pesan error pertama aja biar database gak penuh kalau errornya ribuan
            'error_details'   => $errorCount > 0 ? json_encode(array_slice($errors, 0, 5)) : null,
            'ip_address'      => $request->ip(),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    private function defineColumn($blueprint, $colName)
    {
        if (Str::endsWith($colName, '_id')) {
            $blueprint->string($colName)->nullable()->index();
        } elseif (Str::contains($colName, ['tanggal', 'date'])) {
            $blueprint->date($colName)->nullable();
        } elseif (Str::contains($colName, ['keterangan', 'alamat', 'deskripsi'])) {
            $blueprint->text($colName)->nullable();
        } elseif (in_array($colName, ['created_at', 'updated_at'])) {
            // Skip
        } else {
            $blueprint->string($colName)->nullable();
        }
    }
}