@extends('layouts.app')

@section('title', 'New Price Record')
@section('page-title', 'Set New Price')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>New Price Record</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('prices.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="variety" class="form-label">Chili Variety</label>
                            <input type="text" class="form-control @error('variety') is-invalid @enderror" 
                                   id="variety" name="variety" value="{{ old('variety') }}" 
                                   placeholder="e.g., Habanero, Scotch Bonnet (leave empty for all varieties)">
                            @error('variety')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave blank to apply to all varieties</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="customer_type" class="form-label">Customer Type *</label>
                            <select class="form-select @error('customer_type') is-invalid @enderror" id="customer_type" name="customer_type" required>
                                <option value="">Select Customer Type</option>
                                <option value="individual" {{ old('customer_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="retailer" {{ old('customer_type') == 'retailer' ? 'selected' : '' }}>Retailer</option>
                                <option value="wholesaler" {{ old('customer_type') == 'wholesaler' ? 'selected' : '' }}>Wholesaler</option>
                            </select>
                            @error('customer_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price_per_kg" class="form-label">Price per kg *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control @error('price_per_kg') is-invalid @enderror" 
                                       id="price_per_kg" name="price_per_kg" value="{{ old('price_per_kg') }}" required>
                            </div>
                            @error('price_per_kg')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="effective_from" class="form-label">Effective From *</label>
                            <input type="date" class="form-control @error('effective_from') is-invalid @enderror" 
                                   id="effective_from" name="effective_from" 
                                   value="{{ old('effective_from', today()->toDateString()) }}" required>
                            @error('effective_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="effective_to" class="form-label">Effective To</label>
                            <input type="date" class="form-control @error('effective_to') is-invalid @enderror" 
                                   id="effective_to" name="effective_to" value="{{ old('effective_to') }}">
                            @error('effective_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave blank for ongoing price</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Any additional notes about this price...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Pricing Tip:</strong> Set different prices for different customer types to maximize profits. 
                        Wholesalers typically get lower prices for bulk purchases, while individuals pay premium prices.
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('prices.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Prices
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Save Price
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
