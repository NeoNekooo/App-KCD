<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beritas', function (Blueprint $table) {
            // Make gambar nullable
            $table->string('gambar')->nullable()->change();
            // Since ENUM changes can be tricky, we'll convert it to string
            // but we can also just use raw statement to redefine the enum
        });
        
        // Use raw SQL to safely update ENUM to allow 'publish'
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE beritas MODIFY COLUMN status ENUM('publish', 'published', 'draft') DEFAULT 'publish'");
        
        // Update any existing 'published' statuses to 'publish' to standardize it
        \Illuminate\Support\Facades\DB::statement("UPDATE beritas SET status = 'publish' WHERE status = 'published'");
    }

    public function down(): void
    {
        Schema::table('beritas', function (Blueprint $table) {
            $table->string('gambar')->nullable(false)->change();
        });
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE beritas MODIFY COLUMN status ENUM('published', 'draft') DEFAULT 'published'");
    }
};
