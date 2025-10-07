<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Harvest;

class HarvestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Harvest::query();
        
        // Apply date filters
        if ($request->filled('date_from')) {
            $query->whereDate('harvest_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('harvest_date', '<=', $request->date_to);
        }
        
        $harvests = $query->latest('harvest_date')->paginate(20);
        $totalYield = $query->sum('quantity_kg');
        $averageYield = $query->avg('quantity_kg');
        
        // Preserve filter parameters in pagination
        $harvests->appends($request->query());
        
        return view('harvests.index', compact('harvests', 'totalYield', 'averageYield'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('harvests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'harvest_date' => 'required|date',
            'quantity_kg' => 'required|numeric|min:0',
            'variety' => 'nullable|string|max:255',
            'field_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Harvest::create($validated);

        return redirect()->route('harvests.index')
            ->with('success', 'Harvest record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Harvest $harvest)
    {
        return view('harvests.show', compact('harvest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Harvest $harvest)
    {
        return view('harvests.edit', compact('harvest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Harvest $harvest)
    {
        $validated = $request->validate([
            'harvest_date' => 'required|date',
            'quantity_kg' => 'required|numeric|min:0',
            'variety' => 'nullable|string|max:255',
            'field_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $harvest->update($validated);

        return redirect()->route('harvests.index')
            ->with('success', 'Harvest record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Harvest $harvest)
    {
        $harvest->delete();

        return redirect()->route('harvests.index')
            ->with('success', 'Harvest record deleted successfully.');
    }
}
