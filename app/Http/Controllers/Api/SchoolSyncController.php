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
        // 🔥 DOPING SERVER: Biar kuat nampung ribuan data Siswa tanpa Time-Out! 🔥
        set_time_limit(0); 
        ini_set('memory_limit', '-1');

        try {
            // 1. Ambil Data
            $input = $request->input('rows');

            // Cek apakah payload terlalu besar sampai gagal dibaca PHP
            if ($input === null && $request->getContent() !== '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ukuran data terlalu besar. Hubungi Admin KCD untuk menaikkan limit upload (post_max_size).'
                ], 413);
            }

            if (empty($input) || !is_array($input)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format data tidak valid atau kosong. Harapkan key "rows" berisi array data.'
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
            } elseif (isset($firstRow['peserta_didik_id']) && $tableName === 'siswas') {
                // Spesial fallback buat tabel siswa (jika ngirim peserta_didik_id)
                $primaryKey = 'peserta_didik_id'; 
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

            // 🔥 5. REKAM LOG HISTORY 🔥
            $this->recordSimpleLog($request, $firstRow, $processed, count($errors));

            $msg = "Berhasil memproses $processed data.";
            if (count($errors) > 0) {
                $msg .= " Gagal: " . count($errors) . " data.";
            }

            return response()->json([
                'success' => true, 
                'message' => $msg,
            ]);

        } catch (\Exception $e) {
            // 🔥 Tangkap error fatal biar nggak jadi 500 HTML Page 🔥
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔥 FUNGSI BARU: 1 SEKOLAH CUMA 1 BARIS DATA LOG
     */
    private function recordSimpleLog($request, $firstRow, $processedCount, $errorCount)
    {
        $npsn = $request->input('npsn') ?? ($firstRow['npsn'] ?? 'UNKNOWN');
        $namaSekolah = $request->input('nama_sekolah') ?? ($request->input('nama') ?? ($firstRow['nama_sekolah'] ?? ($firstRow['nama'] ?? 'Sekolah Tidak Diketahui')));

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

        $statusText = ($errorCount == 0) 
            ? "Masuk Semua ($processedCount)" 
            : "Masuk: $processedCount, Gagal: $errorCount";

        $exists = DB::table('sync_logs')->where('npsn', $npsn)->first();

        if ($exists) {
            DB::table('sync_logs')->where('id', $exists->id)->update([
                'nama_sekolah' => $namaSekolah, 
                'status'       => $statusText,
                'updated_at'   => now(), 
            ]);
        } else {
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
        } elseif (Str::contains($colName, ['keterangan', 'alamat', 'deskripsi', 'riwayat', 'catatan'])) {
            $blueprint->text($colName)->nullable(); // Pakai Text untuk data panjang
        } elseif (in_array($colName, ['created_at', 'updated_at'])) {
            // Skip
        } else {
            $blueprint->text($colName)->nullable(); // 🔥 UBAH JADI TEXT SEMENTARA BIAR DATA SISWA GAK KEPOTONG/ERROR 🔥
        }
    }
}