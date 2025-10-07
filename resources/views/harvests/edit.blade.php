@extends('layouts.app')

@section('title', 'Edit Harvest Record')
@section('page-title', 'Edit Harvest Record')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Harvest Record</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('harvests.update', $harvest) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="harvest_date" class="form-label">Harvest Date *</label>
                            <input type="date" class="form-control @error('harvest_date') is-invalid @enderror" 
                                   id="harvest_date" name="harvest_date" 
                                   value="{{ old('harvest_date', $harvest->harvest_date->toDateString()) }}" required>
                            @error('harvest_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="quantity_kg" class="form-label">Quantity (kg) *</label>
                            <input type="number" step="0.01" min="0" class="form-control @error('quantity_kg') is-invalid @enderror" 
                                   id="quantity_kg" name="quantity_kg" value="{{ old('quantity_kg', $harvest->quantity_kg) }}" required>
                            @error('quantity_kg')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="variety" class="form-label">Chili Variety</label>
                            <input type="text" class="form-control @error('variety') is-invalid @enderror" 
                                   id="variety" name="variety" value="{{ old('variety', $harvest->variety) }}" 
                                   placeholder="e.g., Red Hot, Thai Chili, etc.">
                            @error('variety')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="field_location" class="form-label">Field Location</label>
                            <input type="text" class="form-control @error('field_location') is-invalid @enderror" 
                                   id="field_location" name="field_location" value="{{ old('field_location', $harvest->field_location) }}" 
                                   placeholder="e.g., North Field, Greenhouse A, etc.">
                            @error('field_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Any additional notes about this harvest...">{{ old('notes', $harvest->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('harvests.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Harvests
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Harvest Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
