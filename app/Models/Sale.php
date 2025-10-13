<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'harvest_batch_id',
        'sale_date',
        'quantity_kg',
        'price_per_kg',
        'total_amount',
        'variety',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'quantity_kg' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function harvestBatch()
    {
        return $this->belongsTo(Harvest::class, 'harvest_batch_id');
    }

    // Automatically calculate total amount when saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($sale) {
            $sale->total_amount = $sale->quantity_kg * $sale->price_per_kg;
        });
    }
}
