<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Harvest extends Model
{
    use HasFactory;

    protected $fillable = [
        'harvest_date',
        'quantity_kg',
        'variety',
        'notes',
        'field_location',
    ];

    protected $casts = [
        'harvest_date' => 'date',
        'quantity_kg' => 'decimal:2',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'harvest_batch_id');
    }

    public function getBatchNameAttribute()
    {
        return "Batch #{$this->id} - " . $this->harvest_date->format('M d, Y') . 
               ($this->variety ? " ({$this->variety})" : '');
    }

    public function getTotalQuantitySoldAttribute()
    {
        return (float) $this->sales()->where('payment_status', 'paid')->sum('quantity_kg');
    }

    public function getTotalQuantityAllocatedAttribute()
    {
        // Include all sales (paid + pending) to show allocated quantity
        return (float) $this->sales()->sum('quantity_kg');
    }

    public function getRemainingQuantityAttribute()
    {
        return round((float) $this->quantity_kg - $this->total_quantity_sold, 2);
    }

    public function getAvailableQuantityAttribute()
    {
        // Available = harvested - allocated (including pending sales)
        $available = (float) $this->quantity_kg - $this->total_quantity_allocated;

        // Round to two decimals to avoid tiny floating point differences
        return round(max($available, 0), 2);
    }

    public function getTotalRevenueAttribute()
    {
        return $this->sales()->where('payment_status', 'paid')->sum('total_amount');
    }

    public function getBatchStatusAttribute()
    {
        $soldPercentage = $this->quantity_kg > 0 ? ($this->total_quantity_sold / $this->quantity_kg) * 100 : 0;
        $allocatedPercentage = $this->quantity_kg > 0 ? ($this->total_quantity_allocated / $this->quantity_kg) * 100 : 0;
        
        if ($soldPercentage >= 100) {
            return 'completed'; // All sold and paid
        } elseif ($allocatedPercentage >= 100) {
            return 'allocated'; // All allocated but not all paid
        } elseif ($soldPercentage >= 75) {
            return 'nearly_complete'; // 75%+ sold
        } elseif ($soldPercentage > 0 || $allocatedPercentage > 0) {
            return 'partial'; // Some sales made
        } else {
            return 'available'; // No sales yet
        }
    }

    public function getBatchStatusLabelAttribute()
    {
        switch ($this->batch_status) {
            case 'completed':
                return 'Completed';
            case 'allocated':
                return 'Fully Allocated';
            case 'nearly_complete':
                return 'Nearly Complete';
            case 'partial':
                return 'Partially Sold';
            case 'available':
            default:
                return 'Available';
        }
    }

    public function getBatchStatusColorAttribute()
    {
        switch ($this->batch_status) {
            case 'completed':
                return 'success';
            case 'allocated':
                return 'info';
            case 'nearly_complete':
                return 'warning';
            case 'partial':
                return 'primary';
            case 'available':
            default:
                return 'secondary';
        }
    }

    public function getFulfillmentPercentageAttribute()
    {
        return $this->quantity_kg > 0 ? round(($this->total_quantity_sold / $this->quantity_kg) * 100, 1) : 0;
    }

    public function getAllocationPercentageAttribute()
    {
        return $this->quantity_kg > 0 ? round(($this->total_quantity_allocated / $this->quantity_kg) * 100, 1) : 0;
    }
}
