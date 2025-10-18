<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('forecasts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot restore a dropped table, this is irreversible
        throw new Exception('Cannot restore dropped forecasts table');
    }
};
