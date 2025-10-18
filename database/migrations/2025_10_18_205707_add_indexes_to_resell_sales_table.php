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
        Schema::table('resell_sales', function (Blueprint $table) {
            // Add indexes for better query performance
            $table->index(['resell_id', 'sale_quantity_kg'], 'resell_sales_resell_quantity_index');
            $table->index(['resell_id', 'sale_date'], 'resell_sales_resell_date_index');
            $table->index('sale_date', 'resell_sales_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resell_sales', function (Blueprint $table) {
            $table->dropIndex('resell_sales_resell_quantity_index');
            $table->dropIndex('resell_sales_resell_date_index');
            $table->dropIndex('resell_sales_date_index');
        });
    }
};
