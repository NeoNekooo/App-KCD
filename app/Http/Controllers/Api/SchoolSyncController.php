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

        foreach ($input as $index => $row) {
            try {
                $row = (array) $row;
                
                // Encode array/json
                foreach ($row as $k => $v) {
                    if (is_array($v)) $row[$k] = json_encode($v);
                }

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
                    if (!isset($row['created_at'])) $row['created_at'] = now();
                    DB::table($tableName)->insert($row);
                    $processed++;
                    continue;
                }

                // Coba UpdateOrInsert Normal
                try {
                    DB::table($tableName)->updateOrInsert($conditions, $row);
                    $processed++;
                } catch (\Exception $e) {
                    // HANDLING KHUSUS: Duplicate Entry
                    if (str_contains($e->getMessage(), 'Duplicate entry') && ($table === 'penggunas' || $table === 'pengguna')) {
                        if (isset($row['email']) && !empty($row['email'])) {
                            DB::table($tableName)->updateOrInsert(['email' => $row['email']], $row);
                            $processed++;
                        } else {
                            throw $e;
                        }
                    } else {
                        throw $e; 
                    }
                }

            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        // 🔥 FIX ERROR: REKAM LOG UNTUK SEMUA TABEL YANG MASUK (Bukan cuma sekolahs) 🔥
        // Hapus "if ($tableName === 'sekolahs')", biar setiap aplikasi sekolah ngirim data, KCD tau sekolah mana yg ngirim.
        $this->recordSimpleLog($request, $firstRow, $processed, count($errors));

        $msg = "Berhasil memproses $processed data.";
        if (count($errors) > 0) {
            $msg .= " Gagal: " . count($errors) . " data.";
        }

        return response()->json([
            'success' => true, 
            'message' => $msg,
        ]);
    }

    /**
     * 🔥 FUNGSI BARU: 1 SEKOLAH CUMA 1 BARIS DATA LOG
     */
    private function recordSimpleLog($request, $firstRow, $processedCount, $errorCount)
    {
        // Cari NPSN dan Nama Sekolah, kalau gak ada di request/firstRow, kita balikin default
        $npsn = $request->input('npsn') ?? ($firstRow['npsn'] ?? 'UNKNOWN');
        $namaSekolah = $request->input('nama_sekolah') ?? ($request->input('nama') ?? ($firstRow['nama_sekolah'] ?? ($firstRow['nama'] ?? 'Sekolah Tidak Diketahui')));

        // Biar nggak nge-log data sampah kalau bener-bener kosong
        if ($npsn === 'UNKNOWN') return;

        // Bikin tabel sync_logs super simpel kalau belum ada
        if (!Schema::hasTable('sync_logs')) {
            Schema::create('sync_logs', function ($blueprint) {
                $blueprint->id();
                $blueprint->string('npsn')->nullable()->index();
                $blueprint->string('nama_sekolah')->nullable();
                $blueprint->string('status')->nullable();
                $blueprint->timestamps(); 
            });
        }

        // Teks status super simpel
        $statusText = ($errorCount == 0) 
            ? "Berhasil ($processedCount data)" 
            : "Masuk: $processedCount, Gagal: $errorCount";

        // 🔥 Cek berdasarkan NPSN (Biar paten 1 sekolah 1 baris)
        $exists = DB::table('sync_logs')->where('npsn', $npsn)->first();

        if ($exists) {
            // Update barisnya aja, kolom updated_at otomatis jalan jadi patokan "Terakhir Sinkron"
            DB::table('sync_logs')->where('id', $exists->id)->update([
                'nama_sekolah' => $namaSekolah, // Update jaga-jaga kalo namanya berubah
                'status'       => $statusText,
                'updated_at'   => now(), 
            ]);
        } else {
            // Insert baru kalo sekolah ini blm pernah sync sama sekali
            DB::table('sync_logs')->insert([
                'npsn'         => $npsn,
                'nama_sekolah' => $namaSekolah,
                'status'       => $statusText,
                'created_at'   => now(),
                'updated_at'   => now(), 
            ]);
        }
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