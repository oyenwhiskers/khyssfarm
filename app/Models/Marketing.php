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
        'leads_generated',
        'impressions',
        'sales_revenue',
        'customers_retained',
        'product_units_sold',
        'clicks',
        'conversions',
        'notes',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget_spent' => 'decimal:2',
        'sales_revenue' => 'decimal:2',
    ];

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

    // Calculate metrics based on campaign type
    public function getCostPerLeadAttribute()
    {
        return $this->leads_generated > 0 ? $this->budget_spent / $this->leads_generated : 0;
    }

    public function getCostPerImpressionAttribute()
    {
        return $this->impressions > 0 ? ($this->budget_spent / $this->impressions) * 1000 : 0; // CPM
    }

    public function getRoiAttribute()
    {
        return $this->budget_spent > 0 ? (($this->sales_revenue - $this->budget_spent) / $this->budget_spent) * 100 : 0;
    }

    public function getConversionRateAttribute()
    {
        return $this->clicks > 0 ? ($this->conversions / $this->clicks) * 100 : 0;
    }

    public function getDurationInDaysAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getDailyBudgetAttribute()
    {
        return $this->duration_in_days > 0 ? $this->budget_spent / $this->duration_in_days : 0;
    }
}
