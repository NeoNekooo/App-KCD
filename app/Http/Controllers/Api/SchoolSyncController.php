<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

use App\Services\EncryptionService;

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

            $tableName = Str::plural($table); 
            $firstRow = (array) $input[0];
            $columns = array_keys($firstRow);

            // 1. AUTO-SCHEMA
            if (!Schema::hasTable($tableName)) {
                Schema::create($tableName, function ($blueprint) use ($columns, $tableName) {
                    $blueprint->id(); 
                    foreach ($columns as $col) {
                        if ($col === 'id') continue;
                        if (EncryptionService::shouldEncrypt($tableName, $col)) {
                            $blueprint->text($col)->nullable();
                        } else {
                            $this->defineColumn($blueprint, $col);
                        }
                    }
                    $blueprint->timestamps();
                });
            } else {
                $existingCols = Schema::getColumnListing($tableName);
                $newCols = array_diff($columns, $existingCols);
                if (!empty($newCols)) {
                    Schema::table($tableName, function ($blueprint) use ($newCols, $tableName) {
                        foreach ($newCols as $col) {
                            if (EncryptionService::shouldEncrypt($tableName, $col)) {
                                $blueprint->text($col)->nullable();
                            } else {
                                $this->defineColumn($blueprint, $col);
                            }
                        }
                    });
                }
            }

            // 2. PRIMARY KEY DETECTION
            $primaryKey = 'id';
            if (isset($firstRow[$table . '_id'])) $primaryKey = $table . '_id';
            elseif (isset($firstRow[Str::singular($table) . '_id'])) $primaryKey = Str::singular($table) . '_id';
            elseif (isset($firstRow['peserta_didik_id']) && $tableName === 'siswas') $primaryKey = 'peserta_didik_id'; 

            $processed = 0;
            $errors = [];

            // 3. LOOP PENGOLAHAN DATA
            foreach ($input as $index => $row) {
                try {
                    $row = (array) $row;
                    
                    // 🔥 KAMERA CCTV: Catat data yang baru sampe
                    \Illuminate\Support\Facades\Log::info("SYNC_INCOMING ($tableName): " . json_encode($row));

                    foreach ($row as $k => $v) {
                        if (EncryptionService::shouldEncrypt($tableName, $k)) {
                            $row[$k] = EncryptionService::encrypt($v);
                        } elseif (is_array($v)) {
                            $row[$k] = json_encode($v);
                        }
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

            return response()->json(['success' => true, 'message' => $msg]);

        } catch (\Exception $outerException) {
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