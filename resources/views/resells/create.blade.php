@extends('layouts.app')

@section('title', 'Record Purchase')
@section('page-title', 'Record New Purchase')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0 py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 text-dark">Record New Purchase</h4>
                            <p class="text-muted mb-0">Record chili purchase from supplier</p>
                        </div>
                        <a href="{{ route('resells.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('resells.store') }}" method="POST">
                        @csrf
                        
                        <!-- Supplier Information -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-truck me-2"></i>Supplier Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="supplier_name" class="form-label">Supplier Name *</label>
                                    <input type="text" class="form-control @error('supplier_name') is-invalid @enderror" 
                                           id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" 
                                           placeholder="Enter supplier name" required>
                                    @error('supplier_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="supplier_contact" class="form-label">Supplier Contact</label>
                                    <input type="text" class="form-control @error('supplier_contact') is-invalid @enderror" 
                                           id="supplier_contact" name="supplier_contact" value="{{ old('supplier_contact') }}" 
                                           placeholder="Phone or email">
                                    @error('supplier_contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Purchase Details -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-shopping-cart me-2"></i>Purchase Details</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="purchase_date" class="form-label">Purchase Date *</label>
                                    <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                           id="purchase_date" name="purchase_date" value="{{ old('purchase_date', today()->toDateString()) }}" required>
                                    @error('purchase_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="purchase_quantity_kg" class="form-label">Quantity (kg) *</label>
                                    <input type="number" step="0.01" min="0.01" class="form-control @error('purchase_quantity_kg') is-invalid @enderror" 
                                           id="purchase_quantity_kg" name="purchase_quantity_kg" value="{{ old('purchase_quantity_kg') }}" 
                                           placeholder="0.00" required>
                                    @error('purchase_quantity_kg')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="purchase_price_per_kg" class="form-label">Price per KG (RM) *</label>
                                    <input type="number" step="0.01" min="0.01" class="form-control @error('purchase_price_per_kg') is-invalid @enderror" 
                                           id="purchase_price_per_kg" name="purchase_price_per_kg" value="{{ old('purchase_price_per_kg') }}" 
                                           placeholder="0.00" required>
                                    @error('purchase_price_per_kg')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="total_cost" class="form-label">Total Cost (RM)</label>
                                    <input type="text" class="form-control bg-light" id="total_cost" readonly placeholder="Auto calculated">
                                    <small class="text-muted">Automatically calculated: Quantity × Price per KG</small>
                                </div>
                            </div>
                        </div>

                        <!-- Product Information -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-pepper-hot me-2"></i>Product Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="variety" class="form-label">Chili Variety</label>
                                    <input type="text" class="form-control @error('variety') is-invalid @enderror" 
                                           id="variety" name="variety" value="{{ old('variety') }}" 
                                           placeholder="e.g., Bird's eye, Thai chili">
                                    @error('variety')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="quality_grade" class="form-label">Quality Grade</label>
                                    <select class="form-select @error('quality_grade') is-invalid @enderror" id="quality_grade" name="quality_grade">
                                        <option value="">Select grade</option>
                                        <option value="Premium" {{ old('quality_grade') == 'Premium' ? 'selected' : '' }}>Premium</option>
                                        <option value="Grade A" {{ old('quality_grade') == 'Grade A' ? 'selected' : '' }}>Grade A</option>
                                        <option value="Grade B" {{ old('quality_grade') == 'Grade B' ? 'selected' : '' }}>Grade B</option>
                                        <option value="Regular" {{ old('quality_grade') == 'Regular' ? 'selected' : '' }}>Regular</option>
                                    </select>
                                    @error('quality_grade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="purchase_notes" class="form-label">Purchase Notes</label>
                                    <textarea class="form-control @error('purchase_notes') is-invalid @enderror" 
                                              id="purchase_notes" name="purchase_notes" rows="3" 
                                              placeholder="Any additional notes about this purchase...">{{ old('purchase_notes') }}</textarea>
                                    @error('purchase_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                            <a href="{{ route('resells.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Record Purchase
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('purchase_quantity_kg');
    const priceInput = document.getElementById('purchase_price_per_kg');
    const totalCostInput = document.getElementById('total_cost');
    
    function calculateTotal() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const total = quantity * price;
        
        if (total > 0) {
            totalCostInput.value = 'RM ' + total.toFixed(2);
        } else {
            totalCostInput.value = '';
        }
    }
    
    quantityInput.addEventListener('input', calculateTotal);
    priceInput.addEventListener('input', calculateTotal);
    
    // Calculate on page load if values exist
    calculateTotal();
});
</script>
@endsection