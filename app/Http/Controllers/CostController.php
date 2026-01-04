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
        
        // Get total costs and category breakdown for stats
        $totalCosts = $query->sum('amount');
        $costsByCategory = (clone $query)->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();
        
        // Calculate percentages for categories
        $maxCategoryTotal = $costsByCategory->max('total') ?? 0;
        $costsByCategory = $costsByCategory->map(function($cat) use ($totalCosts, $maxCategoryTotal) {
            $cat->percentage = $totalCosts > 0 ? ($cat->total / $totalCosts * 100) : 0;
            $cat->progressPercent = $maxCategoryTotal > 0 ? ($cat->total / $maxCategoryTotal * 100) : 0;
            return $cat;
        });
        
        // Get all costs and group by month/year
        $allCosts = $query->latest('date')->get();
        
        // Group costs by month/year
        $costsByMonth = $allCosts->groupBy(function($cost) {
            return $cost->date->format('Y-m');
        })->sortByDesc(function($group) {
            return $group->first()->date;
        })->map(function($group) {
            return $group->sortByDesc('created_at');  // Latest recorded first
        });
        
        // Calculate monthly totals and daily averages
        $monthlyTotals = [];
        $monthlyAverages = [];
        $chartLabels = [];
        $chartData = [];
        $maxMonthlyTotal = 0;
        
        foreach ($costsByMonth as $monthKey => $monthlyCosts) {
            $total = $monthlyCosts->sum('amount');
            $firstCost = $monthlyCosts->first();
            $monthDate = $firstCost->date;
            $daysInMonth = $monthDate->daysInMonth;
            $average = $daysInMonth > 0 ? $total / $daysInMonth : 0;
            
            $monthlyTotals[$monthKey] = $total;
            $monthlyAverages[$monthKey] = $average;
            
            // For chart - add formatted labels and data
            $chartLabels[] = $monthDate->format('M Y');
            $chartData[] = round($total, 2);
            
            // Track max for progress bar calculation
            if ($total > $maxMonthlyTotal) {
                $maxMonthlyTotal = $total;
            }
        }
        
        // Get all categories for filter dropdown
        $categories = Cost::getCostCategories();
        
        // Determine view mode (grouped or list)
        $viewMode = $request->get('view', 'grouped');
            
        return view('costs.index', compact(
            'costsByMonth',
            'monthlyTotals',
            'monthlyAverages',
            'totalCosts',
            'costsByCategory',
            'categories',
            'allCosts',
            'chartLabels',
            'chartData',
            'viewMode',
            'maxMonthlyTotal'
        ));
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
