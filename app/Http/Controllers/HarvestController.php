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
        
        // Get all harvests
        $allHarvests = $query->latest('harvest_date')->get();
        
        $totalYield = $allHarvests->sum('quantity_kg');
        $averageYield = $allHarvests->count() > 0 ? $allHarvests->avg('quantity_kg') : 0;
        
        // Group harvests by month/year
        $harvestsByMonth = $allHarvests->groupBy(function($harvest) {
            return $harvest->harvest_date->format('Y-m');
        })->sortByDesc(function($group) {
            return $group->first()->harvest_date;
        });
        
        // Calculate monthly statistics
        $monthlyStats = [];
        $maxMonthlyYield = 0;
        
        foreach ($harvestsByMonth as $monthKey => $monthHarvests) {
            $monthYield = $monthHarvests->sum('quantity_kg');
            $monthRevenue = $monthHarvests->sum('total_revenue');
            $monthDate = $monthHarvests->first()->harvest_date;
            $daysInMonth = $monthDate->daysInMonth;
            $avgPerDay = $daysInMonth > 0 ? $monthYield / $daysInMonth : 0;
            
            $monthlyStats[$monthKey] = [
                'yield' => $monthYield,
                'revenue' => $monthRevenue,
                'avg_per_day' => $avgPerDay,
                'count' => $monthHarvests->count(),
            ];
            
            if ($monthYield > $maxMonthlyYield) {
                $maxMonthlyYield = $monthYield;
            }
        }
        
        // Prepare chart data for trend visualization
        $chartLabels = [];
        $yieldChartData = [];
        $revenueChartData = [];
        
        // Sort by month chronologically for charts
        $sortedMonths = $harvestsByMonth->sortBy(function($group) {
            return $group->first()->harvest_date;
        });
        
        foreach ($sortedMonths as $monthKey => $monthHarvests) {
            $monthDate = $monthHarvests->first()->harvest_date;
            $chartLabels[] = $monthDate->format('M Y');
            $yieldChartData[] = round($monthlyStats[$monthKey]['yield'], 2);
            $revenueChartData[] = round($monthlyStats[$monthKey]['revenue'], 2);
        }
        
        // Calculate summary statistics
        $highestYieldMonth = null;
        $highestYieldValue = 0;
        foreach ($monthlyStats as $monthKey => $stats) {
            if ($stats['yield'] > $highestYieldValue) {
                $highestYieldValue = $stats['yield'];
                $highestYieldMonth = $monthKey;
            }
        }
        
        $totalRevenue = $allHarvests->sum('total_revenue');
        
        // Determine view mode
        $viewMode = $request->get('view', 'grouped');
        
        return view('harvests.index', compact(
            'harvestsByMonth',
            'monthlyStats',
            'allHarvests',
            'totalYield',
            'averageYield',
            'viewMode',
            'maxMonthlyYield',
            'chartLabels',
            'yieldChartData',
            'revenueChartData',
            'highestYieldMonth',
            'highestYieldValue',
            'totalRevenue'
        ));
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
