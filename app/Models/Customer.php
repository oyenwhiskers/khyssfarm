<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'location',
        'customer_type',
        'notes',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getTotalPurchasesAttribute()
    {
        return $this->sales()->sum('total_amount');
    }

    public function getTotalQuantityAttribute()
    {
        return $this->sales()->sum('quantity_kg');
    }
}
