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
        'source',
        'notes',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getTotalPurchasesAttribute()
    {
        return $this->sales()->where('payment_status', 'paid')->sum('total_amount');
    }

    public function getTotalQuantityAttribute()
    {
        return $this->sales()->where('payment_status', 'paid')->sum('quantity_kg');
    }

    public function getPendingPaymentsAttribute()
    {
        return $this->sales()->where('payment_status', 'pending')->sum('total_amount');
    }

    public function getPartialPaymentsAttribute()
    {
        return $this->sales()->where('payment_status', 'partial')->sum('total_amount');
    }

    public static function getSourceOptions()
    {
        return [
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'whatsapp' => 'WhatsApp',
            'recommendation' => 'Recommendation',
            'repeat_customer' => 'Repeat Customer',
            'walk_in' => 'Walk-in',
            'online_search' => 'Online Search',
            'marketplace' => 'Online Marketplace',
            'other' => 'Other',
        ];
    }
}
