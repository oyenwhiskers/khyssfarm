<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'variety',
        'customer_type',
        'price_per_kg',
        'effective_from',
        'effective_to',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'price_per_kg' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public static function getCurrentPrice($variety = null, $customer_type = 'individual')
    {
        return static::where('customer_type', $customer_type)
            ->where('variety', $variety)
            ->where('is_active', true)
            ->where('effective_from', '<=', now())
            ->where(function ($query) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now());
            })
            ->latest('effective_from')
            ->first();
    }
}
