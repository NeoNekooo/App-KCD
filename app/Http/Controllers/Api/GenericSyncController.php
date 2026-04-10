<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

use App\Services\EncryptionService;

class GenericSyncController extends Controller
{
    public function handleSync(Request $request, $entity)
    {
        try {
            $rawInput = $request->all();
            $dataFromDapodik = isset($rawInput['rows']) ? $rawInput['rows'] : $rawInput;

            if (empty($dataFromDapodik)) {
                return response()->json(['message' => 'Tidak ada data.'], 200);
            }

            if (isset($dataFromDapodik['rombongan_belajar_id']) || isset($dataFromDapodik[0]) === false) {
                $dataFromDapodik = [$dataFromDapodik];
            }

            $tableName = Str::plural(Str::snake($entity));
            $firstRow = (array) $dataFromDapodik[0];
            $dapodikColumns = array_keys($firstRow);

            // SCHEMA HANDLING
            if (!Schema::hasTable($tableName)) {
                Schema::create($tableName, function ($table) use ($dapodikColumns, $entity, $tableName) {
                    $table->id();
                    if (in_array($entity, ['siswa', 'siswas', 'gtk', 'gtks'])) {
                        $table->string('qr_token')->nullable()->unique();
                    }
                    foreach ($dapodikColumns as $column) {
                        if (EncryptionService::shouldEncrypt($tableName, $column)) {
                            $table->text($column)->nullable();
                        } else {
                            $this->defineColumnType($table, $column);
                        }
                    }
                    $table->timestamps();
                });
            } else {
                $existingCols = Schema::getColumnListing($tableName);
                $newCols = array_diff($dapodikColumns, $existingCols);
                if (!empty($newCols)) {
                    Schema::table($tableName, function ($table) use ($newCols, $tableName) {
                        foreach ($newCols as $column) {
                            if (EncryptionService::shouldEncrypt($tableName, $column)) {
                                $table->text($column)->nullable();
                            } else {
                                $this->defineColumnType($table, $column);
                            }
                        }
                    });
                }
            }

            $identifierColumn = $this->getIdentifierColumn($dapodikColumns, $entity);
            $processed = 0;
            $baseUrl = $request->getSchemeAndHttpHost();

            foreach ($dataFromDapodik as $row) {
                $row = (array) $row;

                // QR TOKEN LOGIC
                if (in_array($entity, ['siswa', 'siswas', 'gtk', 'gtks'])) {
                    $uniqueCode = $row['peserta_didik_id'] ?? ($row['ptk_id'] ?? ($row[$identifierColumn] ?? uniqid()));
                    $row['qr_token'] = $baseUrl . '/scan/' . $uniqueCode;
                }

                // ENCRYPTION & JSON LOGIC
                foreach ($row as $key => $value) {
                    if (EncryptionService::shouldEncrypt($tableName, $key)) {
                        $row[$key] = EncryptionService::encrypt($value);
                    } elseif (is_array($value)) {
                        $row[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
                    }
                }

                DB::table($tableName)->updateOrInsert(
                    [$identifierColumn => $row[$identifierColumn] ?? ($row['id'] ?? null)],
                    $row
                );
                $processed++;
            }

            return response()->json(['success' => true, 'message' => 'Selesai.', 'details' => $processed . ' data.'], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function getIdentifierColumn($columns, $entity)
    {
        $identifiers = ['peserta_didik_id', 'ptk_id', 'rombongan_belajar_id', 'pembelajaran_id', 'sekolah_id'];
        foreach ($identifiers as $id) {
            if (in_array($id, $columns)) return $id;
        }
        return $columns[0];
    }

    private function defineColumnType($table, $column)
    {
        if (Str::endsWith($column, '_id')) {
            $table->string($column, 191)->nullable()->index();
        } elseif (str_contains($column, 'tanggal')) {
            $table->date($column)->nullable();
        } else {
            $table->text($column)->nullable();
        }
    }
}
