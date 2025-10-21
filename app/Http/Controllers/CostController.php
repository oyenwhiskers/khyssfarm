<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cost;

class CostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cost::query();
        
        // Apply date filters
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        
        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        $costs = $query->latest('date')->paginate(20);
        $totalCosts = $query->sum('amount');
        $costsByCategory = $query->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();
            
        // Get all categories for filter dropdown
        $categories = Cost::getCostCategories();
            
        // Preserve filter parameters in pagination
        $costs->appends($request->query());
            
        return view('costs.index', compact('costs', 'totalCosts', 'costsByCategory', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Cost::getCostCategories();
        return view('costs.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Cost::create($validated);

        return redirect()->route('costs.index')
            ->with('success', 'Cost record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cost $cost)
    {
        return view('costs.show', compact('cost'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cost $cost)
    {
        $categories = Cost::getCostCategories();
        return view('costs.edit', compact('cost', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cost $cost)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $cost->update($validated);

        return redirect()->route('costs.index')
            ->with('success', 'Cost record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cost $cost)
    {
        $cost->delete();

        return redirect()->route('costs.index')
            ->with('success', 'Cost record deleted successfully.');
    }
}
