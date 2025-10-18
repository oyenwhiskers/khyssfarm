@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Edit Sale Transaction</h2>
                    <p class="text-muted mb-0">Update sale details for {{ $resellSale->resell->supplier_name }} purchase</p>
                </div>
                <a href="{{ route('resells.show', $resellSale->resell) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Purchase Details
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-pencil-square text-primary me-2"></i>
                                Sale Transaction Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('resell-sales.update', $resellSale) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <!-- Customer Selection -->
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_id" class="form-label">Existing Customer</label>
                                        <select class="form-select @error('customer_id') is-invalid @enderror" 
                                                id="customer_id" 
                                                name="customer_id">
                                            <option value="">Select existing customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" 
                                                        {{ old('customer_id', $resellSale->customer_id) == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('customer_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="customer_name" class="form-label">Or New Customer Name</label>
                                        <input type="text" 
                                               class="form-control @error('customer_name') is-invalid @enderror" 
                                               id="customer_name" 
                                               name="customer_name" 
                                               value="{{ old('customer_name', $resellSale->customer_id ? '' : $resellSale->customer->name ?? '') }}" 
                                               placeholder="Enter new customer name">
                                        @error('customer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Leave blank if selecting existing customer above</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Sale Details -->
                                    <div class="col-md-6 mb-3">
                                        <label for="sale_date" class="form-label">Sale Date <span class="text-danger">*</span></label>
                                        <input type="date" 
                                               class="form-control @error('sale_date') is-invalid @enderror" 
                                               id="sale_date" 
                                               name="sale_date" 
                                               value="{{ old('sale_date', $resellSale->sale_date?->format('Y-m-d')) }}" 
                                               required>
                                        @error('sale_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="sale_quantity_kg" class="form-label">Quantity Sold (kg) <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               class="form-control @error('sale_quantity_kg') is-invalid @enderror" 
                                               id="sale_quantity_kg" 
                                               name="sale_quantity_kg" 
                                               value="{{ old('sale_quantity_kg', $resellSale->sale_quantity_kg) }}" 
                                               step="0.01" 
                                               min="0.01" 
                                               max="{{ $resellSale->resell->purchase_quantity_kg - $resellSale->resell->resellSales()->where('id', '!=', $resellSale->id)->sum('sale_quantity_kg') }}"
                                               required>
                                        @error('sale_quantity_kg')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">
                                            Available: {{ number_format($resellSale->resell->purchase_quantity_kg - $resellSale->resell->resellSales()->where('id', '!=', $resellSale->id)->sum('sale_quantity_kg'), 2) }} kg
                                        </small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="sale_price_per_kg" class="form-label">Sale Price per KG (RM) <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               class="form-control @error('sale_price_per_kg') is-invalid @enderror" 
                                               id="sale_price_per_kg" 
                                               name="sale_price_per_kg" 
                                               value="{{ old('sale_price_per_kg', $resellSale->sale_price_per_kg) }}" 
                                               step="0.01" 
                                               min="0.01" 
                                               required>
                                        @error('sale_price_per_kg')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="total_sale_amount" class="form-label">Total Sale Amount (RM)</label>
                                        <input type="number" 
                                               class="form-control bg-light" 
                                               id="total_sale_amount" 
                                               name="total_sale_amount" 
                                               value="{{ old('total_sale_amount', $resellSale->total_sale_amount) }}" 
                                               step="0.01" 
                                               readonly>
                                        <small class="text-muted">Auto-calculated: Quantity × Sale Price</small>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="sale_notes" class="form-label">Sale Notes</label>
                                    <textarea class="form-control @error('sale_notes') is-invalid @enderror" 
                                              id="sale_notes" 
                                              name="sale_notes" 
                                              rows="3" 
                                              placeholder="Any additional notes about this sale...">{{ old('sale_notes', $resellSale->sale_notes) }}</textarea>
                                    @error('sale_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg"></i> Update Sale
                                    </button>
                                    <a href="{{ route('resells.show', $resellSale->resell) }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                    <button type="button" class="btn btn-outline-danger ms-auto" data-bs-toggle="modal" data-bs-target="#deleteSaleModal">
                                        <i class="bi bi-trash"></i> Delete Sale
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Purchase Details Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-box text-info me-2"></i>
                                Purchase Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="small text-muted mb-2">
                                <strong>Supplier:</strong> {{ $resellSale->resell->supplier_name }}
                            </div>
                            <div class="small text-muted mb-2">
                                <strong>Purchase Date:</strong> {{ $resellSale->resell->purchase_date?->format('M d, Y') }}
                            </div>
                            <div class="small text-muted mb-2">
                                <strong>Total Purchased:</strong> {{ number_format($resellSale->resell->purchase_quantity_kg, 2) }} kg
                            </div>
                            <div class="small text-muted mb-2">
                                <strong>Purchase Price:</strong> RM{{ number_format($resellSale->resell->purchase_price_per_kg, 2) }}/kg
                            </div>
                            <div class="small text-muted">
                                <strong>Variety:</strong> {{ $resellSale->resell->variety ?? 'Not specified' }}
                            </div>
                        </div>
                    </div>

                    <!-- Profit Calculation -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-calculator text-success me-2"></i>
                                Profit Calculation
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="small text-muted mb-2">
                                <strong>Purchase Cost:</strong> RM{{ number_format($resellSale->resell->purchase_price_per_kg * $resellSale->sale_quantity_kg, 2) }}
                            </div>
                            <div class="small text-muted mb-2">
                                <strong>Sale Amount:</strong> RM{{ number_format($resellSale->total_sale_amount, 2) }}
                            </div>
                            <div class="small text-success mb-2">
                                <strong>Profit:</strong> RM{{ number_format($resellSale->profit_amount, 2) }}
                            </div>
                            <div class="small text-success">
                                <strong>Margin:</strong> {{ number_format($resellSale->profit_margin_percentage, 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteSaleModal" tabindex="-1" aria-labelledby="deleteSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSaleModalLabel">Delete Sale Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this sale transaction?</p>
                <div class="alert alert-warning">
                    <small><strong>Warning:</strong> This action cannot be undone. The sale will be permanently removed and the purchase status will be updated accordingly.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('resell-sales.destroy', $resellSale) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Sale</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customer_id');
    const customerNameInput = document.getElementById('customer_name');
    const quantityInput = document.getElementById('sale_quantity_kg');
    const priceInput = document.getElementById('sale_price_per_kg');
    const totalInput = document.getElementById('total_sale_amount');

    // Clear customer name when selecting existing customer
    customerSelect.addEventListener('change', function() {
        if (this.value) {
            customerNameInput.value = '';
        }
    });

    // Clear customer selection when typing new name
    customerNameInput.addEventListener('input', function() {
        if (this.value) {
            customerSelect.value = '';
        }
    });

    function calculateTotal() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const total = quantity * price;
        totalInput.value = total.toFixed(2);
    }

    quantityInput.addEventListener('input', calculateTotal);
    priceInput.addEventListener('input', calculateTotal);
});
</script>
@endpush
@endsection