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

                $conditions = [];
                if (isset($row[$primaryKey])) {
                    $conditions[$primaryKey] = $row[$primaryKey];
                } elseif (isset($row['id'])) {
                     $conditions['id'] = $row['id'];
                }

                if (empty($conditions)) {
                    // Jika tidak ada ID, insert langsung
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