<?php

namespace App\Http\Controllers;

use App\Models\Resell;
use Illuminate\Http\Request;

class ResellController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Resell::query();
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('supplier')) {
            $query->where('supplier_name', 'like', '%' . $request->supplier . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->date_to);
        }
        
        $resells = $query->latest('purchase_date')->paginate(15);
        
        // Calculate summary metrics
        $totalPurchases = Resell::sum('total_purchase_cost');
        $totalSales = \App\Models\ResellSale::sum('total_sale_amount');
        $totalProfit = \App\Models\ResellSale::sum('profit_amount');
        $averageMargin = \App\Models\ResellSale::avg('profit_margin_percentage') ?? 0;
        
        // Preserve filter parameters in pagination
        $resells->appends($request->query());
        
        return view('resells.index', compact(
            'resells', 
            'totalPurchases', 
            'totalSales', 
            'totalProfit', 
            'averageMargin'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('resells.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_contact' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_quantity_kg' => 'required|numeric|min:0.01',
            'purchase_price_per_kg' => 'required|numeric|min:0.01',
            'variety' => 'nullable|string|max:255',
            'quality_grade' => 'nullable|string|max:255',
            'purchase_notes' => 'nullable|string',
        ]);

        Resell::create($validated);

        return redirect()->route('resells.index')
            ->with('success', 'Purchase record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Resell $resell)
    {
        // Eager load relationships to prevent N+1 queries
        $resell->load(['resellSales' => function($query) {
            $query->with('customer:id,name,phone')->latest('sale_date');
        }]);
        
        return view('resells.show', compact('resell'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resell $resell)
    {
        return view('resells.edit', compact('resell'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resell $resell)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_contact' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_quantity_kg' => 'required|numeric|min:0.01',
            'purchase_price_per_kg' => 'required|numeric|min:0.01',
            'variety' => 'nullable|string|max:255',
            'quality_grade' => 'nullable|string|max:255',
            'purchase_notes' => 'nullable|string',
            'sale_date' => 'nullable|date',
            'sale_quantity_kg' => 'nullable|numeric|min:0',
            'sale_price_per_kg' => 'nullable|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'customer_contact' => 'nullable|string|max:255',
            'sale_notes' => 'nullable|string',
            'status' => 'required|in:purchased,partially_sold,sold,expired',
        ]);

        // Validate sale quantity doesn't exceed purchase quantity
        if ($validated['sale_quantity_kg'] && $validated['sale_quantity_kg'] > $validated['purchase_quantity_kg']) {
            return back()->withErrors([
                'sale_quantity_kg' => 'Sale quantity cannot exceed purchase quantity.'
            ])->withInput();
        }

        $resell->update($validated);

        return redirect()->route('resells.index')
            ->with('success', 'Resell record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resell $resell)
    {
        $resell->delete();

        return redirect()->route('resells.index')
            ->with('success', 'Resell record deleted successfully.');
    }

    /**
     * Show the form for recording a sale for this purchase.
     */
    public function recordSale(Resell $resell)
    {
        if ($resell->status === 'sold') {
            return redirect()->route('resells.show', $resell)
                ->with('error', 'This purchase has already been fully sold.');
        }

        $customers = \App\Models\Customer::orderBy('name')->get();

        return view('resells.record-sale', compact('resell', 'customers'));
    }

    /**
     * Store the sale information for this purchase.
     */
    public function storeSale(Request $request, Resell $resell)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date',
            'sale_quantity_kg' => 'required|numeric|min:0.01|max:' . $resell->remaining_quantity,
            'sale_price_per_kg' => 'required|numeric|min:0.01',
            'customer_type' => 'required|in:existing,new',
            'customer_id' => 'required_if:customer_type,existing|nullable|exists:customers,id',
            'customer_name' => 'required_if:customer_type,new|nullable|string|max:255',
            'customer_contact' => 'nullable|string|max:255',
            'sale_notes' => 'nullable|string',
        ]);

        // Handle customer selection
        if ($validated['customer_type'] === 'existing' && $validated['customer_id']) {
            $customer = \App\Models\Customer::find($validated['customer_id']);
            $customerName = $customer->name;
            $customerContact = $validated['customer_contact'] ?: $customer->contact;
            $customerId = $customer->id;
        } else {
            $customerName = $validated['customer_name'];
            $customerContact = $validated['customer_contact'];
            $customerId = null;
        }

        // Create the resell sale record
        \App\Models\ResellSale::create([
            'resell_id' => $resell->id,
            'sale_date' => $validated['sale_date'],
            'sale_quantity_kg' => $validated['sale_quantity_kg'],
            'sale_price_per_kg' => $validated['sale_price_per_kg'],
            'customer_name' => $customerName,
            'customer_contact' => $customerContact,
            'customer_id' => $customerId,
            'sale_notes' => $validated['sale_notes'],
        ]);

        // Update resell status
        $resell->updateStatus();

        return redirect()->route('resells.show', $resell)
            ->with('success', 'Sale recorded successfully.');
    }
}
