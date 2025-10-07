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
}
