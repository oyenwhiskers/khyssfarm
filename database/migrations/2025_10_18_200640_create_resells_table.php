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
        Schema::create('resells', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->string('supplier_contact')->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_quantity_kg', 10, 2);
            $table->decimal('purchase_price_per_kg', 10, 2);
            $table->decimal('total_purchase_cost', 10, 2);
            $table->string('variety')->nullable();
            $table->string('quality_grade')->nullable();
            $table->text('purchase_notes')->nullable();
            
            // Resale information
            $table->date('sale_date')->nullable();
            $table->decimal('sale_quantity_kg', 10, 2)->nullable();
            $table->decimal('sale_price_per_kg', 10, 2)->nullable();
            $table->decimal('total_sale_amount', 10, 2)->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_contact')->nullable();
            $table->text('sale_notes')->nullable();
            
            // Status and tracking
            $table->enum('status', ['purchased', 'partially_sold', 'sold', 'expired'])->default('purchased');
            $table->decimal('remaining_quantity_kg', 10, 2)->nullable();
            $table->decimal('profit_amount', 10, 2)->nullable();
            $table->decimal('profit_margin_percentage', 5, 2)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resells');
    }
};
