<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResellSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'resell_id',
        'sale_date',
        'sale_quantity_kg',
        'sale_price_per_kg',
        'total_sale_amount',
        'customer_name',
        'customer_contact',
        'customer_id',
        'sale_notes',
        'profit_amount',
        'profit_margin_percentage',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'sale_quantity_kg' => 'decimal:2',
        'sale_price_per_kg' => 'decimal:2',
        'total_sale_amount' => 'decimal:2',
        'profit_amount' => 'decimal:2',
        'profit_margin_percentage' => 'decimal:2',
    ];

    // Automatically calculate values when saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($resellSale) {
            // Calculate total sale amount
            if ($resellSale->sale_quantity_kg && $resellSale->sale_price_per_kg) {
                $resellSale->total_sale_amount = $resellSale->sale_quantity_kg * $resellSale->sale_price_per_kg;
            }

            // Calculate profit if resell relationship exists or we have the purchase price
            if ($resellSale->resell || $resellSale->resell_id) {
                // Use eager loaded relationship if available, otherwise load it
                $resell = $resellSale->resell ?? \App\Models\Resell::find($resellSale->resell_id);
                
                if ($resell) {
                    $purchaseCostForSoldQuantity = $resellSale->sale_quantity_kg * $resell->purchase_price_per_kg;
                    $resellSale->profit_amount = $resellSale->total_sale_amount - $purchaseCostForSoldQuantity;
                    
                    // Calculate profit margin percentage
                    if ($purchaseCostForSoldQuantity > 0) {
                        $resellSale->profit_margin_percentage = ($resellSale->profit_amount / $purchaseCostForSoldQuantity) * 100;
                    }
                }
            }
        });
    }

    // Relationships
    public function resell()
    {
        return $this->belongsTo(Resell::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Accessor for profit per kg
    public function getProfitPerKgAttribute()
    {
        if ($this->resell && $this->sale_price_per_kg > 0) {
            return $this->sale_price_per_kg - $this->resell->purchase_price_per_kg;
        }
        return 0;
    }
}