@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Edit Purchase Record</h2>
                    <p class="text-muted mb-0">Update purchase details from supplier</p>
                </div>
                <a href="{{ route('resells.show', $resell) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Details
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-pencil-square text-primary me-2"></i>
                                Purchase Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('resells.update', $resell) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <!-- Supplier Information -->
                                    <div class="col-md-6 mb-3">
                                        <label for="supplier_name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('supplier_name') is-invalid @enderror" 
                                               id="supplier_name" 
                                               name="supplier_name" 
                                               value="{{ old('supplier_name', $resell->supplier_name) }}" 
                                               required>
                                        @error('supplier_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="supplier_contact" class="form-label">Supplier Contact</label>
                                        <input type="text" 
                                               class="form-control @error('supplier_contact') is-invalid @enderror" 
                                               id="supplier_contact" 
                                               name="supplier_contact" 
                                               value="{{ old('supplier_contact', $resell->supplier_contact) }}">
                                        @error('supplier_contact')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Purchase Details -->
                                    <div class="col-md-6 mb-3">
                                        <label for="purchase_date" class="form-label">Purchase Date <span class="text-danger">*</span></label>
                                        <input type="date" 
                                               class="form-control @error('purchase_date') is-invalid @enderror" 
                                               id="purchase_date" 
                                               name="purchase_date" 
                                               value="{{ old('purchase_date', $resell->purchase_date?->format('Y-m-d')) }}" 
                                               required>
                                        @error('purchase_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="purchase_quantity_kg" class="form-label">Quantity (kg) <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               class="form-control @error('purchase_quantity_kg') is-invalid @enderror" 
                                               id="purchase_quantity_kg" 
                                               name="purchase_quantity_kg" 
                                               value="{{ old('purchase_quantity_kg', $resell->purchase_quantity_kg) }}" 
                                               step="0.01" 
                                               min="0" 
                                               required>
                                        @error('purchase_quantity_kg')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="purchase_price_per_kg" class="form-label">Price per KG (RM) <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               class="form-control @error('purchase_price_per_kg') is-invalid @enderror" 
                                               id="purchase_price_per_kg" 
                                               name="purchase_price_per_kg" 
                                               value="{{ old('purchase_price_per_kg', $resell->purchase_price_per_kg) }}" 
                                               step="0.01" 
                                               min="0" 
                                               required>
                                        @error('purchase_price_per_kg')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="total_purchase_cost" class="form-label">Total Cost (RM)</label>
                                        <input type="number" 
                                               class="form-control bg-light" 
                                               id="total_purchase_cost" 
                                               name="total_purchase_cost" 
                                               value="{{ old('total_purchase_cost', $resell->total_purchase_cost) }}" 
                                               step="0.01" 
                                               readonly>
                                        <small class="text-muted">Auto-calculated: Quantity × Price per KG</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Product Details -->
                                    <div class="col-md-6 mb-3">
                                        <label for="variety" class="form-label">Variety</label>
                                        <select class="form-select @error('variety') is-invalid @enderror" 
                                                id="variety" 
                                                name="variety">
                                            <option value="">Select variety</option>
                                            <option value="Bara" {{ old('variety', $resell->variety) == 'Bara' ? 'selected' : '' }}>Bara</option>
                                            <option value="Kulai" {{ old('variety', $resell->variety) == 'Kulai' ? 'selected' : '' }}>Kulai</option>
                                            <option value="Cili Padi" {{ old('variety', $resell->variety) == 'Cili Padi' ? 'selected' : '' }}>Cili Padi</option>
                                            <option value="Mixed" {{ old('variety', $resell->variety) == 'Mixed' ? 'selected' : '' }}>Mixed</option>
                                        </select>
                                        @error('variety')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="quality_grade" class="form-label">Quality Grade</label>
                                        <select class="form-select @error('quality_grade') is-invalid @enderror" 
                                                id="quality_grade" 
                                                name="quality_grade">
                                            <option value="">Select grade</option>
                                            <option value="Premium" {{ old('quality_grade', $resell->quality_grade) == 'Premium' ? 'selected' : '' }}>Premium</option>
                                            <option value="Grade A" {{ old('quality_grade', $resell->quality_grade) == 'Grade A' ? 'selected' : '' }}>Grade A</option>
                                            <option value="Grade B" {{ old('quality_grade', $resell->quality_grade) == 'Grade B' ? 'selected' : '' }}>Grade B</option>
                                            <option value="Regular" {{ old('quality_grade', $resell->quality_grade) == 'Regular' ? 'selected' : '' }}>Regular</option>
                                        </select>
                                        @error('quality_grade')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="purchase_notes" class="form-label">Purchase Notes</label>
                                    <textarea class="form-control @error('purchase_notes') is-invalid @enderror" 
                                              id="purchase_notes" 
                                              name="purchase_notes" 
                                              rows="3" 
                                              placeholder="Any additional notes about this purchase...">{{ old('purchase_notes', $resell->purchase_notes) }}</textarea>
                                    @error('purchase_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg"></i> Update Purchase
                                    </button>
                                    <a href="{{ route('resells.show', $resell) }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Current Status Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-info-circle text-info me-2"></i>
                                Current Status
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <span class="badge bg-{{ $resell->status_color }} fs-6">
                                    {{ $resell->status_label }}
                                </span>
                            </div>
                            
                            <div class="small text-muted mb-2">
                                <strong>Purchase Quantity:</strong> {{ number_format($resell->purchase_quantity_kg, 2) }} kg
                            </div>
                            
                            @if($resell->total_quantity_sold > 0)
                                <div class="small text-muted mb-2">
                                    <strong>Sold:</strong> {{ number_format($resell->total_quantity_sold, 2) }} kg
                                </div>
                                <div class="small text-muted mb-2">
                                    <strong>Remaining:</strong> {{ number_format($resell->remaining_quantity, 2) }} kg
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Sales Summary -->
                    @if($resell->resellSales->count() > 0)
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-graph-up text-success me-2"></i>
                                    Sales Summary
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="small text-muted mb-2">
                                    <strong>Total Sales:</strong> {{ $resell->resellSales->count() }}
                                </div>
                                <div class="small text-muted mb-2">
                                    <strong>Total Revenue:</strong> RM{{ number_format($resell->total_sale_amount, 2) }}
                                </div>
                                <div class="small text-muted mb-2">
                                    <strong>Total Profit:</strong> RM{{ number_format($resell->total_profit, 2) }}
                                </div>
                                @if($resell->average_profit_margin > 0)
                                    <div class="small text-muted">
                                        <strong>Avg. Margin:</strong> {{ number_format($resell->average_profit_margin, 1) }}%
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('purchase_quantity_kg');
    const priceInput = document.getElementById('purchase_price_per_kg');
    const totalInput = document.getElementById('total_purchase_cost');

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