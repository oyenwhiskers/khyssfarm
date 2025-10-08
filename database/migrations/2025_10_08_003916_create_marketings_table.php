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
        Schema::create('marketings', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_name');
            $table->enum('campaign_type', ['lead_generation', 'brand_awareness', 'sales_conversion', 'customer_retention', 'product_launch']);
            $table->enum('marketing_channel', ['facebook', 'instagram', 'tiktok', 'whatsapp', 'google_ads', 'flyers', 'radio', 'newspaper', 'word_of_mouth', 'event', 'other']);
            $table->decimal('budget_spent', 10, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            
            // Results based on campaign type
            $table->integer('leads_generated')->nullable(); // For lead generation
            $table->integer('impressions')->nullable(); // For brand awareness
            $table->decimal('sales_revenue', 10, 2)->nullable(); // For sales conversion
            $table->integer('customers_retained')->nullable(); // For customer retention
            $table->integer('product_units_sold')->nullable(); // For product launch
            
            // Additional metrics
            $table->integer('clicks')->nullable();
            $table->integer('conversions')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'completed', 'paused', 'cancelled'])->default('active');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketings');
    }
};
