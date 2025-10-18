<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Resell extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_name',
        'supplier_contact',
        'purchase_date',
        'purchase_quantity_kg',
        'purchase_price_per_kg',
        'total_purchase_cost',
        'variety',
        'quality_grade',
        'purchase_notes',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_quantity_kg' => 'decimal:2',
        'purchase_price_per_kg' => 'decimal:2',
        'total_purchase_cost' => 'decimal:2',
    ];

    // Automatically calculate values when saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($resell) {
            // Calculate total purchase cost
            if ($resell->purchase_quantity_kg && $resell->purchase_price_per_kg) {
                $resell->total_purchase_cost = $resell->purchase_quantity_kg * $resell->purchase_price_per_kg;
            }
        });
        
        static::saved(function ($resell) {
            // Update status based on sales after saving
            $resell->updateStatus();
        });
    }

    // Relationships
    public function resellSales()
    {
        return $this->hasMany(ResellSale::class);
    }

    // Update status based on sales
    public function updateStatus()
    {
        $totalSold = $this->resellSales()->sum('sale_quantity_kg');
        
        $newStatus = 'purchased';
        if ($totalSold >= $this->purchase_quantity_kg) {
            $newStatus = 'sold';
        } elseif ($totalSold > 0) {
            $newStatus = 'partially_sold';
        }
        
        // Only update if status actually changed to avoid unnecessary queries
        if ($this->status !== $newStatus) {
            $this->updateQuietly(['status' => $newStatus]);
        }
    }

    // Calculated attributes with caching
    public function getTotalQuantitySoldAttribute()
    {
        // Cache the result to avoid repeated sum queries
        return $this->resellSales->sum('sale_quantity_kg');
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->purchase_quantity_kg - $this->total_quantity_sold;
    }

    public function getTotalSaleAmountAttribute()
    {
        return $this->resellSales->sum('total_sale_amount');
    }

    public function getTotalProfitAttribute()
    {
        return $this->resellSales->sum('profit_amount');
    }

    public function getAverageProfitMarginAttribute()
    {
        return $this->resellSales->avg('profit_margin_percentage') ?? 0;
    }

    // Accessor for profit per kg (based on latest sale price)
    public function getProfitPerKgAttribute()
    {
        $latestSale = $this->resellSales()->latest()->first();
        if ($latestSale) {
            return $latestSale->sale_price_per_kg - $this->purchase_price_per_kg;
        }
        return 0;
    }

    // Accessor for total potential profit (if all quantity sold at latest price)
    public function getTotalPotentialProfitAttribute()
    {
        $latestSale = $this->resellSales()->latest()->first();
        if ($latestSale) {
            return ($latestSale->sale_price_per_kg - $this->purchase_price_per_kg) * $this->purchase_quantity_kg;
        }
        return 0;
    }

    // Status label accessor
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'purchased' => 'Purchased',
            'partially_sold' => 'Partially Sold',
            'sold' => 'Sold',
            'expired' => 'Expired',
            default => 'Unknown'
        };
    }

    // Status color accessor
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'purchased' => 'info',
            'partially_sold' => 'warning',
            'sold' => 'success',
            'expired' => 'danger',
            default => 'secondary'
        };
    }
}