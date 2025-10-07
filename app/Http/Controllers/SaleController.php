<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Price;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sale::with('customer');
        
        // Apply date filters
        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }
        
        $sales = $query->latest('sale_date')->paginate(20);
        $totalRevenue = $query->sum('total_amount');
        $totalQuantitySold = $query->sum('quantity_kg');
        $averagePrice = $totalQuantitySold > 0 ? $totalRevenue / $totalQuantitySold : 0;
        
        // Preserve filter parameters in pagination
        $sales->appends($request->query());
        
        return view('sales.index', compact('sales', 'totalRevenue', 'totalQuantitySold', 'averagePrice'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('sales.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'sale_date' => 'required|date',
            'quantity_kg' => 'required|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'variety' => 'nullable|string|max:255',
            'payment_status' => 'required|in:pending,paid,partial',
            'notes' => 'nullable|string',
        ]);

        // Calculate total amount
        $validated['total_amount'] = $validated['quantity_kg'] * $validated['price_per_kg'];

        Sale::create($validated);

        return redirect()->route('sales.index')
            ->with('success', 'Sale record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        $customers = Customer::orderBy('name')->get();
        return view('sales.edit', compact('sale', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'sale_date' => 'required|date',
            'quantity_kg' => 'required|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'variety' => 'nullable|string|max:255',
            'payment_status' => 'required|in:pending,paid,partial',
            'notes' => 'nullable|string',
        ]);

        // Calculate total amount
        $validated['total_amount'] = $validated['quantity_kg'] * $validated['price_per_kg'];

        $sale->update($validated);

        return redirect()->route('sales.index')
            ->with('success', 'Sale record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Sale record deleted successfully.');
    }
}
