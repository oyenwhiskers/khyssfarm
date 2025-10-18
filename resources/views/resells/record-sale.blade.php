@extends('layouts.app')

@section('title', 'Record Sale - Resell #' . $resell->id)
@section('page-title', 'Record Sale')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Purchase Summary -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Purchase Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Supplier:</strong> {{ $resell->supplier_name }}<br>
                            <strong>Purchase Date:</strong> {{ $resell->purchase_date->format('M d, Y') }}<br>
                            <strong>Variety:</strong> {{ $resell->variety ?: 'Not specified' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Purchased Quantity:</strong> {{ number_format($resell->purchase_quantity_kg, 2) }} kg<br>
                            <strong>Purchase Price:</strong> RM{{ number_format($resell->purchase_price_per_kg, 2) }}/kg<br>
                            <strong>Available to Sell:</strong> <span class="text-success fw-bold">{{ number_format($resell->remaining_quantity, 2) }} kg</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sale Form -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0 py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 text-dark">Record Sale</h4>
                            <p class="text-muted mb-0">Record the sale details for this purchase</p>
                        </div>
                        <a href="{{ route('resells.show', $resell) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Details
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('resells.store-sale', $resell) }}" method="POST">
                        @csrf
                        
                        <!-- Sale Details -->
                        <div class="mb-4">
                            <h5 class="text-success mb-3"><i class="fas fa-dollar-sign me-2"></i>Sale Details</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sale_date" class="form-label">Sale Date *</label>
                                    <input type="date" class="form-control @error('sale_date') is-invalid @enderror" 
                                           id="sale_date" name="sale_date" value="{{ old('sale_date', today()->toDateString()) }}" required>
                                    @error('sale_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="sale_quantity_kg" class="form-label">Quantity to Sell (kg) *</label>
                                    <input type="number" step="0.01" min="0.01" max="{{ $resell->remaining_quantity }}" 
                                           class="form-control @error('sale_quantity_kg') is-invalid @enderror" 
                                           id="sale_quantity_kg" name="sale_quantity_kg" value="{{ old('sale_quantity_kg') }}" 
                                           placeholder="Max: {{ number_format($resell->remaining_quantity, 2) }}" required>
                                    @error('sale_quantity_kg')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Available: {{ number_format($resell->remaining_quantity, 2) }} kg</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="sale_price_per_kg" class="form-label">Sale Price per KG (RM) *</label>
                                    <input type="number" step="0.01" min="0.01" class="form-control @error('sale_price_per_kg') is-invalid @enderror" 
                                           id="sale_price_per_kg" name="sale_price_per_kg" value="{{ old('sale_price_per_kg') }}" 
                                           placeholder="0.00" required>
                                    @error('sale_price_per_kg')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Purchase price was: RM{{ number_format($resell->purchase_price_per_kg, 2) }}/kg</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="total_sale_amount" class="form-label">Total Sale Amount (RM)</label>
                                    <input type="text" class="form-control bg-light" id="total_sale_amount" readonly placeholder="Auto calculated">
                                    <small class="text-muted">Automatically calculated: Quantity × Price per KG</small>
                                </div>
                            </div>
                        </div>

                        <!-- Profit Preview -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-chart-line me-2"></i>Profit Preview</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="p-3 border rounded bg-light">
                                        <h6 class="text-muted mb-1">Profit per KG</h6>
                                        <h4 id="profit_per_kg" class="mb-0">RM 0.00</h4>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="p-3 border rounded bg-light">
                                        <h6 class="text-muted mb-1">Total Profit</h6>
                                        <h4 id="total_profit" class="mb-0">RM 0.00</h4>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="p-3 border rounded bg-light">
                                        <h6 class="text-muted mb-1">Profit Margin</h6>
                                        <h4 id="profit_margin" class="mb-0">0.0%</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Customer Information</h5>
                            
                            <!-- Customer Type Selection -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Customer Type *</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="customer_type" id="customer_existing" value="existing" {{ old('customer_type', 'existing') === 'existing' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="customer_existing">
                                                Existing Customer
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="customer_type" id="customer_new" value="new" {{ old('customer_type') === 'new' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="customer_new">
                                                New Customer
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Existing Customer Selection -->
                            <div id="existing_customer_section" class="row mb-3">
                                <div class="col-md-8">
                                    <label for="customer_id" class="form-label">Select Customer</label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                                        <option value="">-- Select an existing customer --</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" 
                                                    data-contact="{{ $customer->contact }}"
                                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ ucfirst($customer->customer_type) }})
                                                @if($customer->contact)
                                                    - {{ $customer->contact }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <a href="{{ route('customers.create') }}" class="btn btn-outline-primary" target="_blank">
                                            <i class="fas fa-plus me-1"></i>Add New Customer
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- New Customer Form -->
                            <div id="new_customer_section" class="row mb-3" style="display: none;">
                                <div class="col-md-8 mb-3">
                                    <label for="customer_name" class="form-label">Customer Name *</label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                           id="customer_name" name="customer_name" value="{{ old('customer_name') }}" 
                                           placeholder="Enter customer name">
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contact Information (for both types) -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_contact" class="form-label">Customer Contact</label>
                                    <input type="text" class="form-control @error('customer_contact') is-invalid @enderror" 
                                           id="customer_contact" name="customer_contact" value="{{ old('customer_contact') }}" 
                                           placeholder="Phone or email (optional)">
                                    @error('customer_contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted" id="contact_help">You can override or add contact information</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="sale_notes" class="form-label">Sale Notes</label>
                                    <textarea class="form-control @error('sale_notes') is-invalid @enderror" 
                                              id="sale_notes" name="sale_notes" rows="3" 
                                              placeholder="Any additional notes about this sale...">{{ old('sale_notes') }}</textarea>
                                    @error('sale_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                            <a href="{{ route('resells.show', $resell) }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save me-2"></i>Record Sale
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
    const quantityInput = document.getElementById('sale_quantity_kg');
    const priceInput = document.getElementById('sale_price_per_kg');
    const totalSaleInput = document.getElementById('total_sale_amount');
    const profitPerKgElement = document.getElementById('profit_per_kg');
    const totalProfitElement = document.getElementById('total_profit');
    const profitMarginElement = document.getElementById('profit_margin');
    
    // Customer type handling
    const customerTypeRadios = document.querySelectorAll('input[name="customer_type"]');
    const existingCustomerSection = document.getElementById('existing_customer_section');
    const newCustomerSection = document.getElementById('new_customer_section');
    const customerSelect = document.getElementById('customer_id');
    const customerContactInput = document.getElementById('customer_contact');
    const contactHelp = document.getElementById('contact_help');
    
    const purchasePrice = {{ $resell->purchase_price_per_kg }};
    
    // Handle customer type switching
    function toggleCustomerSections() {
        const selectedType = document.querySelector('input[name="customer_type"]:checked').value;
        
        if (selectedType === 'existing') {
            existingCustomerSection.style.display = 'block';
            newCustomerSection.style.display = 'none';
            contactHelp.textContent = 'You can override or add contact information';
            // Clear new customer name when switching to existing
            document.getElementById('customer_name').value = '';
        } else {
            existingCustomerSection.style.display = 'none';
            newCustomerSection.style.display = 'block';
            contactHelp.textContent = 'Enter contact information for the new customer';
            // Clear existing customer selection when switching to new
            customerSelect.value = '';
            customerContactInput.value = '';
        }
    }
    
    // Handle existing customer selection
    function handleCustomerSelection() {
        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
        if (selectedOption.value) {
            const contact = selectedOption.getAttribute('data-contact');
            if (contact) {
                customerContactInput.value = contact;
            } else {
                customerContactInput.value = '';
            }
        } else {
            customerContactInput.value = '';
        }
    }
    
    // Profit calculation functions
    function calculateAll() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const salePrice = parseFloat(priceInput.value) || 0;
        const totalSale = quantity * salePrice;
        const profitPerKg = salePrice - purchasePrice;
        const totalProfit = profitPerKg * quantity;
        const purchaseCost = quantity * purchasePrice;
        const profitMargin = purchaseCost > 0 ? (totalProfit / purchaseCost) * 100 : 0;
        
        // Update total sale amount
        if (totalSale > 0) {
            totalSaleInput.value = 'RM ' + totalSale.toFixed(2);
        } else {
            totalSaleInput.value = '';
        }
        
        // Update profit calculations
        profitPerKgElement.textContent = 'RM ' + profitPerKg.toFixed(2);
        profitPerKgElement.className = profitPerKg >= 0 ? 'mb-0 text-success' : 'mb-0 text-danger';
        
        totalProfitElement.textContent = 'RM ' + totalProfit.toFixed(2);
        totalProfitElement.className = totalProfit >= 0 ? 'mb-0 text-success' : 'mb-0 text-danger';
        
        profitMarginElement.textContent = profitMargin.toFixed(1) + '%';
        profitMarginElement.className = profitMargin >= 0 ? 'mb-0 text-success' : 'mb-0 text-danger';
    }
    
    // Event listeners
    customerTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleCustomerSections);
    });
    
    customerSelect.addEventListener('change', handleCustomerSelection);
    quantityInput.addEventListener('input', calculateAll);
    priceInput.addEventListener('input', calculateAll);
    
    // Initialize on page load
    toggleCustomerSections();
    calculateAll();
});
</script>
@endsection