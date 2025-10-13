<?php

namespace App\Http\Controllers;

use App\Models\Marketing;
use App\Services\OpenAIService;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Marketing::query();
        
        // Apply date filters
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('start_date', '<=', $request->end_date);
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Apply campaign type filter
        if ($request->filled('campaign_type')) {
            $query->where('campaign_type', $request->campaign_type);
        }
        
        $campaigns = $query->latest('start_date')->paginate(10);
        
        // Calculate summary metrics from all campaigns (not just paginated ones)
        $allCampaigns = Marketing::all();
        $totalBudget = $allCampaigns->sum('budget_spent');
        $totalLeads = $allCampaigns->sum('leads_generated');
        $avgCostPerLead = $totalLeads > 0 ? $totalBudget / $totalLeads : 0;
        $activeCampaigns = Marketing::where('status', 'active')->count();
        
        return view('marketing.index', compact(
            'campaigns', 
            'totalBudget', 
            'totalLeads', 
            'avgCostPerLead', 
            'activeCampaigns'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('marketing.create');
    }
    
    /**
     * Generate channel recommendations for creating a new campaign
     */
    public function getChannelRecommendations(Request $request, OpenAIService $openAIService)
    {
        try {
            $campaignType = $request->get('campaign_type', 'lead_generation');
            $audienceType = $request->get('audience_type', 'mixed (individuals, retailers, wholesalers)');
            $budgetRange = $request->get('budget_range', '50-500');
            
            $channelRecommendations = $openAIService->getChannelRecommendations(
                $campaignType, 
                $audienceType, 
                $budgetRange
            );
            
            return response()->json([
                'success' => true,
                'recommendations' => $channelRecommendations
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate channel recommendations. Please try again later.'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'campaign_name' => 'required|string|max:255',
            'campaign_type' => 'required|in:lead_generation,brand_awareness,sales_conversion,customer_retention,product_launch',
            'marketing_channel' => 'required|in:facebook,instagram,tiktok,whatsapp,google_ads,flyers,radio,newspaper,word_of_mouth,event,other',
            'budget_spent' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'impressions' => 'nullable|integer|min:0',
            'clicks' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,completed,paused,cancelled',
        ]);

        Marketing::create($validated);

        return redirect()->route('marketing.index')
            ->with('success', 'Marketing campaign created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Marketing $marketing)
    {
        return view('marketing.show', compact('marketing'));
    }
    
    /**
     * Generate AI insights for a marketing campaign
     */
    public function generateInsights(Marketing $marketing, OpenAIService $openAIService)
    {
        try {
            // Pre-load customers with sales to avoid multiple queries
            $marketing->load(['customers.sales']);
            
            // Pre-calculate expensive metrics to avoid accessor queries
            $customers = $marketing->customers;
            $leadsGenerated = $customers->count();
            $conversions = $customers->filter(function($customer) {
                return $customer->sales->where('payment_status', 'paid')->count() > 0;
            })->count();
            $salesRevenue = $customers->sum('total_purchases');
            $costPerLead = $leadsGenerated > 0 ? $marketing->budget_spent / $leadsGenerated : 0;
            $roi = $marketing->budget_spent > 0 ? (($salesRevenue - $marketing->budget_spent) / $marketing->budget_spent) * 100 : 0;
            $conversionRate = $leadsGenerated > 0 ? ($conversions / $leadsGenerated) * 100 : 0;
            $customerCategories = $customers->groupBy('customer_type')->map->count()->toArray();
            
            // Set these values on the model to avoid accessor calls
            $marketing->setAppends([]);
            $marketing->setAttribute('leads_generated_calculated', $leadsGenerated);
            $marketing->setAttribute('conversions_calculated', $conversions);
            $marketing->setAttribute('sales_revenue_calculated', $salesRevenue);
            $marketing->setAttribute('cost_per_lead_calculated', $costPerLead);
            $marketing->setAttribute('roi_calculated', $roi);
            $marketing->setAttribute('conversion_rate_calculated', $conversionRate);
            $marketing->setAttribute('customer_categories_calculated', $customerCategories);
            
            // Get AI insights for the campaign
            $aiInsights = $openAIService->getMarketingInsights($marketing);
            
            return response()->json([
                'success' => $aiInsights['success'],
                'insights' => $aiInsights['insights']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate AI insights. Please try again later.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marketing $marketing)
    {
        return view('marketing.edit', compact('marketing'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marketing $marketing)
    {
        $validated = $request->validate([
            'campaign_name' => 'required|string|max:255',
            'campaign_type' => 'required|in:lead_generation,brand_awareness,sales_conversion,customer_retention,product_launch',
            'marketing_channel' => 'required|in:facebook,instagram,tiktok,whatsapp,google_ads,flyers,radio,newspaper,word_of_mouth,event,other',
            'budget_spent' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'impressions' => 'nullable|integer|min:0',
            'clicks' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,completed,paused,cancelled',
        ]);

        $marketing->update($validated);

        return redirect()->route('marketing.index')
            ->with('success', 'Marketing campaign updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marketing $marketing)
    {
        $marketing->delete();

        return redirect()->route('marketing.index')
            ->with('success', 'Marketing campaign deleted successfully.');
    }
}
