@extends('layouts.app')

@section('title', 'Harvest Details')
@section('page-title', 'Harvest Details')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-leaf me-2"></i>Harvest Details</h5>
                <div>
                    <a href="{{ route('harvests.edit', $harvest) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <form action="{{ route('harvests.destroy', $harvest) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3" 
                                onclick="return confirm('Are you sure you want to delete this harvest record?')">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h6 class="text-muted">Basic Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Harvest Date:</strong></td>
                                <td>{{ $harvest->harvest_date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Chili Variety:</strong></td>
                                <td>
                                    <span class="badge bg-primary">{{ ucfirst($harvest->variety) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Quantity Harvested:</strong></td>
                                <td>
                                    <span class="fs-5 text-success fw-bold">{{ number_format($harvest->quantity_kg, 2) }} kg</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Field/Section:</strong></td>
                                <td>{{ $harvest->field_location ?: 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Additional Information -->
                    <div class="col-md-6">
                        <h6 class="text-muted">Additional Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Field Location:</strong></td>
                                <td>{{ $harvest->field_location ?: 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Record Status:</strong></td>
                                <td><span class="badge bg-success">Active</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Notes -->
                @if($harvest->notes)
                <div class="mt-4">
                    <h6 class="text-muted">Notes</h6>
                    <div class="alert alert-light">
                        {{ $harvest->notes }}
                    </div>
                </div>
                @endif
                
                <!-- Timestamps -->
                <div class="mt-4">
                    <h6 class="text-muted">Record Information</h6>
                    <small class="text-muted">
                        Created: {{ $harvest->created_at->format('F d, Y g:i A') }}<br>
                        Last Updated: {{ $harvest->updated_at->format('F d, Y g:i A') }}
                    </small>
                </div>
                
                <!-- Actions -->
                <div class="mt-4 text-center">
                    <a href="{{ route('harvests.index') }}" class="btn btn-secondary rounded-pill px-4 me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Harvests
                    </a>
                    <a href="{{ route('sales.create') }}" class="btn btn-success rounded-pill px-4">
                        <i class="fas fa-plus me-2"></i>Record New Sale
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
