@extends('layouts.app')

@section('title', 'Edit Sale Record')
@section('page-title', 'Edit Sale Record')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Sale Record</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('sales.update', $sale) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sale_date" class="form-label">Sale Date *</label>
                            <input type="date" class="form-control @error('sale_date') is-invalid @enderror" 
                                   id="sale_date" name="sale_date" 
                                   value="{{ old('sale_date', $sale->sale_date->toDateString()) }}" required>
                            @error('sale_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                                <option value="">Walk-in Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" 
                                        {{ (old('customer_id') ?? $sale->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ ucfirst($customer->customer_type) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="harvest_batch_id" class="form-label">Harvest Batch</label>
                            <select class="form-select @error('harvest_batch_id') is-invalid @enderror" id="harvest_batch_id" name="harvest_batch_id">
                                <option value="">-- Select Harvest Batch (optional) --</option>
                                @foreach($harvestBatches as $batch)
                                    @php
                                        // Show available qty and credit back the current sale if it belongs to this batch
                                        $available = $batch->available_quantity + (($sale->harvest_batch_id === $batch->id) ? $sale->quantity_kg : 0);
                                    @endphp
                                    <option value="{{ $batch->id }}" 
                                        {{ (old('harvest_batch_id') ?? $sale->harvest_batch_id) == $batch->id ? 'selected' : '' }}>
                                        Batch #{{ $batch->id }} - {{ $batch->harvest_date->format('M d, Y') }} 
                                        ({{ $batch->variety ?: 'Mixed' }}) - {{ number_format($available, 2) }}kg available
                                    </option>
                                @endforeach
                            </select>
                            @error('harvest_batch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="quantity_kg" class="form-label">Quantity (kg) *</label>
                            <input type="number" step="0.01" min="0" class="form-control @error('quantity_kg') is-invalid @enderror" 
                                   id="quantity_kg" name="quantity_kg" value="{{ old('quantity_kg', $sale->quantity_kg) }}" required>
                            @error('quantity_kg')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="price_per_kg" class="form-label">Price per kg *</label>
                            <input type="number" step="0.01" min="0" class="form-control @error('price_per_kg') is-invalid @enderror" 
                                   id="price_per_kg" name="price_per_kg" value="{{ old('price_per_kg', $sale->price_per_kg) }}" required>
                            @error('price_per_kg')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="total_amount" class="form-label">Total Amount</label>
                            <input type="number" step="0.01" class="form-control" id="total_amount" 
                                   value="{{ old('total_amount', $sale->total_amount) }}" readonly>
                            <small class="text-muted">Calculated automatically</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="variety" class="form-label">Chili Variety</label>
                            <input type="text" class="form-control @error('variety') is-invalid @enderror" 
                                   id="variety" name="variety" value="{{ old('variety', $sale->variety) }}" 
                                   placeholder="e.g., Habanero, Scotch Bonnet, etc.">
                            @error('variety')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="payment_status" class="form-label">Payment Status *</label>
                            <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status" required>
                                <option value="pending" {{ (old('payment_status') ?? $sale->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ (old('payment_status') ?? $sale->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="partial" {{ (old('payment_status') ?? $sale->payment_status) == 'partial' ? 'selected' : '' }}>Partial</option>
                            </select>
                            @error('payment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Any additional notes about this sale...">{{ old('notes', $sale->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Sales
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Sale Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-calculate total amount
document.getElementById('quantity_kg').addEventListener('input', calculateTotal);
document.getElementById('price_per_kg').addEventListener('input', calculateTotal);

function calculateTotal() {
    const quantity = parseFloat(document.getElementById('quantity_kg').value) || 0;
    const price = parseFloat(document.getElementById('price_per_kg').value) || 0;
    const total = quantity * price;
    document.getElementById('total_amount').value = total.toFixed(2);
}

// Calculate on page load
calculateTotal();
</script>
@endsection
