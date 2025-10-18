<?php

namespace App\Http\Controllers;

use App\Models\ResellSale;
use App\Models\Resell;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResellSaleController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResellSale $resellSale)
    {
        $customers = Customer::orderBy('name')->get();
        return view('resell-sales.edit', compact('resellSale', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ResellSale $resellSale)
    {
        // Custom validation to ensure either customer_id or customer_name is provided
        $request->validate([
            'sale_date' => 'required|date',
            'sale_quantity_kg' => 'required|numeric|min:0.01',
            'sale_price_per_kg' => 'required|numeric|min:0.01',
            'sale_notes' => 'nullable|string',
        ]);

        // Validate customer selection
        $customerId = $request->input('customer_id');
        $customerName = $request->input('customer_name');

        if (empty($customerId) && empty($customerName)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['customer_id' => 'Please select an existing customer or enter a new customer name.']);
        }

        if (!empty($customerId) && !empty($customerName)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['customer_name' => 'Please either select an existing customer OR enter a new customer name, not both.']);
        }

        if (!empty($customerId)) {
            $customer = Customer::find($customerId);
            if (!$customer) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['customer_id' => 'Selected customer not found.']);
            }
        }

        $validated = [
            'customer_id' => $customerId,
            'customer_name' => $customerName,
            'sale_date' => $request->input('sale_date'),
            'sale_quantity_kg' => $request->input('sale_quantity_kg'),
            'sale_price_per_kg' => $request->input('sale_price_per_kg'),
            'sale_notes' => $request->input('sale_notes'),
        ];

        // Handle customer creation if new customer name provided
        if (empty($validated['customer_id']) && !empty($validated['customer_name'])) {
            $customer = Customer::firstOrCreate(
                ['name' => $validated['customer_name']],
                ['source' => 'resell_sale']
            );
            $validated['customer_id'] = $customer->id;
        }

        // Check if the sale quantity doesn't exceed available quantity
        $resell = $resellSale->resell;
        $otherSalesTotal = $resell->resellSales()
            ->where('id', '!=', $resellSale->id)
            ->sum('sale_quantity_kg');
        
        $availableQuantity = $resell->purchase_quantity_kg - $otherSalesTotal;
        
        if ($validated['sale_quantity_kg'] > $availableQuantity) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['sale_quantity_kg' => "Sale quantity cannot exceed available quantity of {$availableQuantity} kg"]);
        }

        // Update the sale
        $resellSale->update($validated);

        // Update the parent resell status
        $resell->updateStatus();

        return redirect()->route('resells.show', $resell)
            ->with('success', 'Sale transaction updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResellSale $resellSale)
    {
        $resell = $resellSale->resell;
        
        // Use transaction for better performance and data consistency
        DB::transaction(function () use ($resellSale, $resell) {
            $resellSale->delete();
            
            // Update the parent resell status more efficiently
            $totalSold = $resell->resellSales()->sum('sale_quantity_kg');
            
            if ($totalSold >= $resell->purchase_quantity_kg) {
                $newStatus = 'sold';
            } elseif ($totalSold > 0) {
                $newStatus = 'partially_sold';
            } else {
                $newStatus = 'purchased';
            }
            
            // Direct update without triggering model events
            $resell->update(['status' => $newStatus]);
        });

        return redirect()->route('resells.show', $resell)
            ->with('success', 'Sale transaction deleted successfully!');
    }
}
