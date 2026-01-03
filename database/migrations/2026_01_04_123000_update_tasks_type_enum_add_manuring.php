<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE tasks MODIFY COLUMN type ENUM('harvesting','spraying','grass_cut','sanitation','maintenance','manuring','other') NOT NULL DEFAULT 'other'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE tasks MODIFY COLUMN type ENUM('harvesting','spraying','grass_cut','sanitation','maintenance','other') NOT NULL DEFAULT 'other'");
    }
};
