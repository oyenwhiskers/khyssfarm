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
        Schema::table('costs', function (Blueprint $table) {
            // Add task_id column if it doesn't exist
            if (!Schema::hasColumn('costs', 'task_id')) {
                $table->unsignedBigInteger('task_id')->nullable()->after('id');
                $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('costs', function (Blueprint $table) {
            // Drop foreign key and column
            if (Schema::hasColumn('costs', 'task_id')) {
                $table->dropForeign(['task_id']);
                $table->dropColumn('task_id');
            }
        });
    }
};
