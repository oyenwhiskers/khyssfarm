<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::with('marketingCampaign');
        
        // Apply date filters based on customer creation date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter by marketing campaign
        if ($request->filled('campaign')) {
            $query->where('marketing_campaign_id', $request->campaign);
        }
        
        // Filter by customer type (for tabs)
        $customerType = $request->get('type', 'all');
        if ($customerType !== 'all') {
            $query->where('customer_type', $customerType);
        }
        
        // Get counts for tab badges (apply date/campaign filters but not type filter)
        $baseQuery = Customer::query();
        if ($request->filled('date_from')) {
            $baseQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $baseQuery->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('campaign')) {
            $baseQuery->where('marketing_campaign_id', $request->campaign);
        }
        
        $totalCount = $baseQuery->count();
        $retailerCount = (clone $baseQuery)->where('customer_type', 'retailer')->count();
        $individualCount = (clone $baseQuery)->where('customer_type', 'individual')->count();
        $wholesalerCount = (clone $baseQuery)->where('customer_type', 'wholesaler')->count();
        
        // Calculate totals before pagination (for summary cards)
        // Get customer IDs for the filtered query
        $customerIds = $query->pluck('id');
        
        // Calculate total revenue from both sales types
        $farmSalesRevenue = \App\Models\Sale::whereIn('customer_id', $customerIds)
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        $resellSalesRevenue = \App\Models\ResellSale::whereIn('customer_id', $customerIds)
            ->sum('total_sale_amount');
        $totalRevenue = $farmSalesRevenue + $resellSalesRevenue;
        
        // Calculate total quantity from both sales types
        $farmSalesQuantity = \App\Models\Sale::whereIn('customer_id', $customerIds)
            ->where('payment_status', 'paid')
            ->sum('quantity_kg');
        $resellSalesQuantity = \App\Models\ResellSale::whereIn('customer_id', $customerIds)
            ->sum('sale_quantity_kg');
        $totalQuantity = $farmSalesQuantity + $resellSalesQuantity;
        
        $customerCount = $customerIds->count();
        
        $customers = $query->latest()->paginate(20);
        
        // Preserve filter parameters in pagination
        $customers->appends($request->query());
            
        return view('customers.index', compact(
            'customers', 
            'customerType',
            'totalCount',
            'retailerCount',
            'individualCount',
            'wholesalerCount',
            'totalRevenue',
            'totalQuantity',
            'customerCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'customer_type' => 'required|in:individual,retailer,wholesaler',
            'source' => 'nullable|string|max:50',
            'marketing_campaign_id' => 'nullable|exists:marketings,id',
            'notes' => 'nullable|string',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load(['sales' => function($query) {
            $query->latest('sale_date');
        }]);
        
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'customer_type' => 'required|in:individual,retailer,wholesaler',
            'source' => 'nullable|string|max:50',
            'marketing_campaign_id' => 'nullable|exists:marketings,id',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
