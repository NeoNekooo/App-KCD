<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$columns = [
    'nisn', 'nik', 'no_kk', 'tanggal_lahir'
];

echo "Database Column Check for 'siswas' table:\n";
foreach ($columns as $column) {
    if (Schema::hasColumn('siswas', $column)) {
        $type = DB::getSchemaBuilder()->getColumnType('siswas', $column);
        echo "- {$column}: {$type}\n";
    } else {
        echo "- {$column}: NOT FOUND\n";
    }
}
