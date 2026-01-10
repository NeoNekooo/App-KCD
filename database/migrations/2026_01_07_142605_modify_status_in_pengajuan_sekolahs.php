<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE pengajuan_sekolahs MODIFY COLUMN status VARCHAR(100) DEFAULT 'Proses'");
    }

    public function down()
    {

    }
};