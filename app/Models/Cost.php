<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cost extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'category',
        'description',
        'amount',
        'supplier',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public static function getCostCategories()
    {
        return [
            'fertilizer' => 'Fertilizer',
            'labor' => 'Labor',
            'transport' => 'Transport',
            'seeds' => 'Seeds',
            'equipment' => 'Equipment',
            'pesticide' => 'Pesticide',
            'irrigation' => 'Irrigation',
            'packaging' => 'Packaging',
            'maintenance' => 'Maintenance',
            'bills' => 'Bills',
            'loan' => 'Loan',
            'resell' => 'Resell',
            'marketing' => 'Marketing',
            'short' => 'Short',
            'other' => 'Other',
        ];
    }
}
