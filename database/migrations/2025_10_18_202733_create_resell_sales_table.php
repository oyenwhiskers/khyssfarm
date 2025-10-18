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
        Schema::create('resell_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resell_id')->constrained()->onDelete('cascade');
            $table->date('sale_date');
            $table->decimal('sale_quantity_kg', 10, 2);
            $table->decimal('sale_price_per_kg', 10, 2);
            $table->decimal('total_sale_amount', 10, 2);
            $table->string('customer_name')->nullable();
            $table->string('customer_contact')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->text('sale_notes')->nullable();
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
        Schema::dropIfExists('resell_sales');
    }
};
