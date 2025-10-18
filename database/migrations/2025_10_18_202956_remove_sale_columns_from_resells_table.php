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
        Schema::table('resells', function (Blueprint $table) {
            $table->dropColumn([
                'sale_date',
                'sale_quantity_kg',
                'sale_price_per_kg',
                'total_sale_amount',
                'customer_name',
                'customer_contact',
                'sale_notes',
                'remaining_quantity_kg',
                'profit_amount',
                'profit_margin_percentage'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resells', function (Blueprint $table) {
            $table->date('sale_date')->nullable();
            $table->decimal('sale_quantity_kg', 10, 2)->nullable();
            $table->decimal('sale_price_per_kg', 10, 2)->nullable();
            $table->decimal('total_sale_amount', 10, 2)->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_contact')->nullable();
            $table->text('sale_notes')->nullable();
            $table->decimal('remaining_quantity_kg', 10, 2)->nullable();
            $table->decimal('profit_amount', 10, 2)->nullable();
            $table->decimal('profit_margin_percentage', 5, 2)->nullable();
        });
    }
};
