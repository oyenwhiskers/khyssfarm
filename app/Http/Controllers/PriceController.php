<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Price;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Price::query();
        
        // Apply date filters
        if ($request->filled('date_from')) {
            $query->whereDate('effective_from', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where(function($q) use ($request) {
                $q->whereDate('effective_from', '<=', $request->date_to)
                  ->orWhere(function($sq) use ($request) {
                      $sq->whereNotNull('effective_to')
                         ->whereDate('effective_to', '<=', $request->date_to);
                  });
            });
        }
        
        $prices = $query->latest('effective_from')->paginate(20);
        $activePrices = Price::where('is_active', true)
            ->where('effective_from', '<=', now())
            ->where(function ($query) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now());
            })
            ->get();
            
        // Preserve filter parameters in pagination
        $prices->appends($request->query());
            
        return view('prices.index', compact('prices', 'activePrices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('prices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'variety' => 'nullable|string|max:255',
            'customer_type' => 'required|in:individual,retailer,wholesaler',
            'price_per_kg' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        Price::create($validated);

        return redirect()->route('prices.index')
            ->with('success', 'Price record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Price $price)
    {
        return view('prices.show', compact('price'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Price $price)
    {
        return view('prices.edit', compact('price'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Price $price)
    {
        $validated = $request->validate([
            'variety' => 'nullable|string|max:255',
            'customer_type' => 'required|in:individual,retailer,wholesaler',
            'price_per_kg' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $price->update($validated);

        return redirect()->route('prices.index')
            ->with('success', 'Price record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Price $price)
    {
        $price->delete();

        return redirect()->route('prices.index')
            ->with('success', 'Price record deleted successfully.');
    }
}
