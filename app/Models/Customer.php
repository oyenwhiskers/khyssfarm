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
        'marketing_campaign_id',
        'notes'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function resellSales()
    {
        return $this->hasMany(ResellSale::class);
    }

    public function marketingCampaign()
    {
        return $this->belongsTo(Marketing::class, 'marketing_campaign_id');
    }

    public function getTotalPurchasesAttribute()
    {
        $farmSales = $this->sales()->where('payment_status', 'paid')->sum('total_amount');
        $resellSales = $this->resellSales()->sum('total_sale_amount');
        return $farmSales + $resellSales;
    }

    public function getTotalQuantityAttribute()
    {
        $farmQuantity = $this->sales()->where('payment_status', 'paid')->sum('quantity_kg');
        $resellQuantity = $this->resellSales()->sum('sale_quantity_kg');
        return $farmQuantity + $resellQuantity;
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
