<?php

namespace App\Http\Controllers;

use App\Models\Marketing;
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
        
        $campaigns = $query->latest('start_date')->get();
        
        // Calculate summary metrics
        $totalBudget = $campaigns->sum('budget_spent');
        $totalLeads = $campaigns->sum('leads_generated');
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
            'leads_generated' => 'nullable|integer|min:0',
            'impressions' => 'nullable|integer|min:0',
            'sales_revenue' => 'nullable|numeric|min:0',
            'customers_retained' => 'nullable|integer|min:0',
            'product_units_sold' => 'nullable|integer|min:0',
            'clicks' => 'nullable|integer|min:0',
            'conversions' => 'nullable|integer|min:0',
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
            'leads_generated' => 'nullable|integer|min:0',
            'impressions' => 'nullable|integer|min:0',
            'sales_revenue' => 'nullable|numeric|min:0',
            'customers_retained' => 'nullable|integer|min:0',
            'product_units_sold' => 'nullable|integer|min:0',
            'clicks' => 'nullable|integer|min:0',
            'conversions' => 'nullable|integer|min:0',
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
