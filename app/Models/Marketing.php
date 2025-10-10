<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Marketing extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_name',
        'campaign_type',
        'marketing_channel',
        'budget_spent',
        'start_date',
        'end_date',
        'description',
        'impressions',
        'clicks',
        'notes',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget_spent' => 'decimal:2',
        'sales_revenue' => 'decimal:2',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'marketing_campaign_id');
    }

    public static function getCampaignTypes()
    {
        return [
            'lead_generation' => 'Lead Generation',
            'brand_awareness' => 'Brand Awareness',
            'sales_conversion' => 'Sales Conversion',
            'customer_retention' => 'Customer Retention',
            'product_launch' => 'Product Launch',
        ];
    }

    public static function getMarketingChannels()
    {
        return [
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'whatsapp' => 'WhatsApp',
            'google_ads' => 'Google Ads',
            'flyers' => 'Flyers/Print',
            'radio' => 'Radio',
            'newspaper' => 'Newspaper',
            'word_of_mouth' => 'Word of Mouth',
            'event' => 'Events/Exhibitions',
            'other' => 'Other',
        ];
    }

    public static function getStatusOptions()
    {
        return [
            'active' => 'Active',
            'completed' => 'Completed',
            'paused' => 'Paused',
            'cancelled' => 'Cancelled',
        ];
    }

    // Calculate metrics based on customer data
    public function getLeadsGeneratedAttribute()
    {
        return $this->customers()->count();
    }

    public function getSalesRevenueAttribute()
    {
        return $this->customers()->with('sales')->get()->sum('total_purchases');
    }

    public function getConversionsAttribute()
    {
        return $this->customers()->whereHas('sales', function($query) {
            $query->where('payment_status', 'paid');
        })->count();
    }

    public function getCostPerLeadAttribute()
    {
        $leads = $this->leads_generated;
        return $leads > 0 ? $this->budget_spent / $leads : 0;
    }

    public function getRoiAttribute()
    {
        $revenue = $this->sales_revenue;
        return $this->budget_spent > 0 ? (($revenue - $this->budget_spent) / $this->budget_spent) * 100 : 0;
    }

    public function getConversionRateAttribute()
    {
        $leads = $this->leads_generated;
        $conversions = $this->conversions;
        return $leads > 0 ? ($conversions / $leads) * 100 : 0;
    }

    public function getDurationInDaysAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getDailyBudgetAttribute()
    {
        return $this->duration_in_days > 0 ? $this->budget_spent / $this->duration_in_days : 0;
    }

    public function getCustomerCategoriesAttribute()
    {
        return $this->customers()
            ->selectRaw('customer_type, COUNT(*) as count')
            ->groupBy('customer_type')
            ->pluck('count', 'customer_type')
            ->toArray();
    }
}
