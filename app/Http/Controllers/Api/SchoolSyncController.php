<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

use App\Services\EncryptionService;
use App\Models\SyncLog;

class SchoolSyncController extends Controller
{
    public function handle(Request $request, $table)
    {
        set_time_limit(0); 
        ini_set('memory_limit', '-1');

        try {
            $input = $request->input('rows');
            if ($input === null && $request->getContent() !== '') {
                return response()->json(['success' => false, 'message' => 'Payload too large.'], 413);
            }
            if (empty($input) || !is_array($input)) {
                return response()->json(['success' => false, 'message' => 'Invalid data.'], 400);
            }

            // Gunakan nama tabel apa adanya dari pengirim (Sekolah)
            $tableName = $table; 
            $firstRow = (array) $input[0];
            $columns = array_keys($firstRow);

            // 🔥 Ambil identitas sekolah dari data yang dikirim buat log
            $npsn = $firstRow['npsn'] ?? null;
            $namaSekolah = $firstRow['nama'] ?? $firstRow['nama_sekolah'] ?? null;
            
            // Jika NPSN tidak ada di row, coba cari manual di database sekolahs via sekolah_id
            if (!$npsn && isset($firstRow['sekolah_id'])) {
                $sekolahData = DB::table('sekolahs')->where('sekolah_id', $firstRow['sekolah_id'])->first();
                if ($sekolahData) {
                    $npsn = $sekolahData->npsn;
                    $namaSekolah = $sekolahData->nama;
                }
            }

            // 1. AUTO-SCHEMA
            if (!Schema::hasTable($tableName)) {
                Schema::create($tableName, function ($blueprint) use ($columns, $tableName) {
                    $blueprint->id(); 
                    foreach ($columns as $col) {
                        if (in_array($col, ['id', 'created_at', 'updated_at'])) continue;
                        $this->defineColumn($blueprint, $col);
                    }
                    $blueprint->timestamps();
                });
            } else {
                $existingCols = Schema::getColumnListing($tableName);
                $newCols = array_diff($columns, $existingCols);
                if (!empty($newCols)) {
                    Schema::table($tableName, function ($blueprint) use ($newCols, $tableName) {
                        foreach ($newCols as $col) {
                            if (in_array($col, ['id', 'created_at', 'updated_at'])) continue;
                            $this->defineColumn($blueprint, $col);
                        }
                    });
                }
            }

            // 2. PRIMARY KEY DETECTION - Cari ID Unik (Dapodik) agar data antar sekolah tidak tabrakan
            $primaryKey = 'id';
            if (isset($firstRow['ptk_id'])) $primaryKey = 'ptk_id';
            elseif (isset($firstRow['peserta_didik_id'])) $primaryKey = 'peserta_didik_id';
            elseif (isset($firstRow['sekolah_id']) && $tableName === 'sekolahs') $primaryKey = 'sekolah_id';
            elseif (isset($firstRow[$table . '_id'])) $primaryKey = $table . '_id';

            $processed = 0;
            $errors = [];

            // 3. LOOP PENGOLAHAN DATA
            $instansiCache = []; // 🔥 Cache kilat agar tidak query berulang
            foreach ($input as $index => $row) {
                try {
                    $row = (array) $row;

                    // --- 🔥 OTOMATISASI INSTANSI_ID (ISOLASI REGIONAL) 🔥 ---
                    if (!isset($row['instansi_id'])) {
                        $sid = $row['sekolah_id'] ?? $row['id_sekolah'] ?? null;
                        $npsn = $row['npsn'] ?? null;

                        if ($sid) {
                            if (!isset($instansiCache[$sid])) {
                                $instansiCache[$sid] = DB::table('sekolahs')->where('sekolah_id', $sid)->value('instansi_id');
                            }
                            $row['instansi_id'] = $instansiCache[$sid];
                        } elseif ($npsn) {
                            if (!isset($instansiCache[$npsn])) {
                                $instansiCache[$npsn] = DB::table('sekolahs')->where('npsn', $npsn)->value('instansi_id');
                            }
                            $row['instansi_id'] = $instansiCache[$npsn];
                        }
                    }
                    
                    foreach ($row as $k => $v) {
                        if (is_array($v)) {
                            $row[$k] = json_encode($v);
                        }
                    }

                    if (!isset($row['updated_at'])) {
                        $row['updated_at'] = now();
                    }

                    $conditions = [];
                    // Gunakan Kombinasi Primary Key + sekolah_id untuk keamanan data
                    if ($primaryKey !== 'id' && isset($row[$primaryKey])) {
                         $conditions[$primaryKey] = $row[$primaryKey];
                    } else {
                         // Jika tidak ada ID unik Dapodik, gunakan ID lokal + sekolah_id
                         if (isset($row['id'])) $conditions['id_lokal_sekolah'] = $row['id']; // Simpan ID asli sekolah
                         if (isset($row['sekolah_id'])) $conditions['sekolah_id'] = $row['sekolah_id'];
                         
                         // Pastikan kolom id_lokal_sekolah ada di tabel
                         if (!Schema::hasColumn($tableName, 'id_lokal_sekolah') && isset($row['id'])) {
                             Schema::table($tableName, function($table) {
                                 $table->string('id_lokal_sekolah')->nullable()->index();
                             });
                         }
                         // Jangan mencoba mengupdate kolom 'id' auto-increment KCD dengan ID dari sekolah
                         unset($row['id']); 
                    }

                    if (empty($conditions)) {
                        DB::table($tableName)->insert($row);
                    } else {
                        DB::table($tableName)->updateOrInsert($conditions, $row);
                    }
                    
                    $processed++;
                } catch (\Exception $innerException) {
                    $errors[] = $innerException->getMessage();
                }
            }

            $msg = "Berhasil memproses $processed data.";
            if (count($errors) > 0) {
                $msg .= " Gagal: " . count($errors) . " data.";
            }

            // 🔥 CATAT LOG BERHASIL
            if ($npsn) {
                SyncLog::create([
                    'npsn'          => $npsn,
                    'nama_sekolah'  => $namaSekolah,
                    'status'        => "Berhasil ($processed)"
                ]);
            }

            return response()->json(['success' => true, 'message' => $msg]);

        } catch (\Exception $outerException) {
            // 🔥 CATAT LOG GAGAL
            if (isset($npsn) && $npsn) {
                SyncLog::create([
                    'npsn'          => $npsn,
                    'nama_sekolah'  => $namaSekolah ?? 'Unknown',
                    'status'        => "Gagal: " . substr($outerException->getMessage(), 0, 50)
                ]);
            }
            return response()->json(['success' => false, 'message' => $outerException->getMessage()], 500);
       }
    }

    private function defineColumn($blueprint, $colName)
    {
        if (Str::endsWith($colName, '_id')) {
            $blueprint->string($colName)->nullable()->index();
        } elseif (Str::contains($colName, ['tanggal', 'date'])) {
            $blueprint->date($colName)->nullable();
        } else {
            $blueprint->text($colName)->nullable();
        }
    }
}