<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Price;
use App\Models\Harvest;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'harvestBatch']);
        
        // Apply date filters
        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        // Apply batch filter
        if ($request->filled('batch_id')) {
            $query->where('harvest_batch_id', $request->batch_id);
        }
        
        $sales = $query->latest('sale_date')->paginate(20);
        
        // Calculate revenue only from paid sales
        $paidSalesQuery = clone $query;
        $totalRevenue = $paidSalesQuery->where('payment_status', 'paid')->sum('total_amount');
        $totalQuantitySold = $paidSalesQuery->where('payment_status', 'paid')->sum('quantity_kg');
        $averagePrice = $totalQuantitySold > 0 ? $totalRevenue / $totalQuantitySold : 0;
        
        // Calculate pending revenue separately for display
        $pendingRevenueQuery = clone $query;
        $pendingRevenue = $pendingRevenueQuery->where('payment_status', 'pending')->sum('total_amount');
        
        // Get harvest batches for filter dropdown
        $harvestBatches = Harvest::orderBy('harvest_date', 'desc')->get();
        
        // Preserve filter parameters in pagination
        $sales->appends($request->query());
        
        return view('sales.index', compact('sales', 'totalRevenue', 'totalQuantitySold', 'averagePrice', 'pendingRevenue', 'harvestBatches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $harvestBatches = Harvest::with('sales')
                                ->where('quantity_kg', '>', 0)
                                ->orderBy('harvest_date', 'desc')
                                ->get()
                                ->filter(function($harvest) {
                                    return $harvest->available_quantity > 0;
                                });
        return view('sales.create', compact('customers', 'harvestBatches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'harvest_batch_id' => 'nullable|exists:harvests,id',
            'sale_date' => 'required|date',
            'quantity_kg' => 'required|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'variety' => 'nullable|string|max:255',
            'payment_status' => 'required|in:pending,paid,partial',
            'notes' => 'nullable|string',
        ]);

        // Validate batch availability if batch is selected
        if ($validated['harvest_batch_id']) {
            $harvestBatch = Harvest::find($validated['harvest_batch_id']);
            if ($harvestBatch && $harvestBatch->available_quantity < $validated['quantity_kg']) {
                return back()->withErrors([
                    'quantity_kg' => "Only {$harvestBatch->available_quantity} kg available in this batch. Current allocation would exceed harvest quantity."
                ])->withInput();
            }
        }

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
        $harvestBatches = Harvest::with('sales')
                                ->where('quantity_kg', '>', 0)
                                ->orderBy('harvest_date', 'desc')
                                ->get()
                                ->filter(function($harvest) use ($sale) {
                                    // Include current batch even if fully allocated, plus available batches
                                    return $harvest->id == $sale->harvest_batch_id || $harvest->available_quantity > 0;
                                });
        return view('sales.edit', compact('sale', 'customers', 'harvestBatches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'harvest_batch_id' => 'nullable|exists:harvests,id',
            'sale_date' => 'required|date',
            'quantity_kg' => 'required|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'variety' => 'nullable|string|max:255',
            'payment_status' => 'required|in:pending,paid,partial',
            'notes' => 'nullable|string',
        ]);

        // Validate batch availability if batch is selected
        if ($validated['harvest_batch_id']) {
            $harvestBatch = Harvest::find($validated['harvest_batch_id']);
            if ($harvestBatch) {
                // Calculate available quantity (excluding current sale quantity)
                $availableQuantity = $harvestBatch->available_quantity + $sale->quantity_kg;
                if ($availableQuantity < $validated['quantity_kg']) {
                    return back()->withErrors([
                        'quantity_kg' => "Only {$availableQuantity} kg available in this batch. Current allocation would exceed harvest quantity."
                    ])->withInput();
                }
            }
        }

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

    /**
     * Generate and display receipt for printing.
     */
    public function receipt(Sale $sale)
    {
        $sale->load('customer');
        return view('sales.receipt', compact('sale'));
    }

    /**
     * Display sales grouped by harvest batches.
     */
    public function batches(Request $request)
    {
        $query = Harvest::with(['sales' => function($query) {
            $query->with('customer');
        }]);

        // Apply date filters to harvest
        if ($request->filled('date_from')) {
            $query->whereDate('harvest_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('harvest_date', '<=', $request->date_to);
        }

        $harvestBatches = $query->orderBy('harvest_date', 'desc')->paginate(10);
        
        // Preserve filter parameters in pagination
        $harvestBatches->appends($request->query());

        return view('sales.batches', compact('harvestBatches'));
    }

    /**
     * Display detailed view of a specific harvest batch and its sales.
     */
    public function batchDetail(Harvest $harvest)
    {
        $harvest->load(['sales' => function($query) {
            $query->with('customer')->orderBy('sale_date', 'desc');
        }]);

        return view('sales.batch-detail', compact('harvest'));
    }
}
