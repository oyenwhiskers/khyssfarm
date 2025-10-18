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
        Schema::create('forecasts', function (Blueprint $table) {
            $table->id();
            $table->string('forecast_type'); // yield, revenue, costs, profit, etc.
            $table->string('category'); // farm_production, resell_business, combined, etc.
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('target_value', 15, 2);
            $table->decimal('projected_value', 15, 2);
            $table->decimal('actual_value', 15, 2)->nullable();
            $table->string('unit', 50); // kg, rm, percentage, units, customers
            $table->decimal('confidence_level', 5, 2)->default(75.00); // 0-100%
            $table->string('methodology'); // historical_trend, moving_average, etc.
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'archived'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['forecast_type', 'category']);
            $table->index(['period_start', 'period_end']);
            $table->index('status');
            $table->index('created_by');

            // Foreign key
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forecasts');
    }
};
