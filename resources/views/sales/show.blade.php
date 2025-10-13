@extends('layouts.app')

@section('title', 'Sale Details')
@section('page-title', 'Sale Details')

@section('content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-receipt me-2 text-primary"></i>Sale Details</h2>
                <p class="text-muted mb-0">Sale #{{ $sale->id }} - {{ $sale->sale_date->format('F d, Y') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-success" target="_blank">
                    <i class="fas fa-print me-2"></i>Print Receipt
                </a>
                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                <form action="{{ route('sales.destroy', $sale) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Are you sure you want to delete this sale record?')">
                        <i class="fas fa-trash me-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- Sale Information Card -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Sale Information</h5>
            </div>
            <div class="card-body">
                <!-- Date and Basic Info -->
                <div class="row mb-4">
                    <div class="col-6">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="fas fa-calendar-alt fa-2x text-primary mb-2"></i>
                            <h5 class="mb-1">{{ $sale->sale_date->format('M d') }}</h5>
                            <small class="text-muted">{{ $sale->sale_date->format('Y') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="fas fa-hashtag fa-2x text-info mb-2"></i>
                            <h5 class="mb-1">Sale #{{ $sale->id }}</h5>
                            <small class="text-muted">Reference ID</small>
                        </div>
                    </div>
                </div>

                <!-- Quantity and Price -->
                <div class="row mb-4">
                    <div class="col-6">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-weight fa-2x text-success mb-2"></i>
                            <h4 class="mb-1 text-success">{{ number_format($sale->quantity_kg, 2) }}</h4>
                            <small class="text-muted">Kilograms</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-tag fa-2x text-warning mb-2"></i>
                            <h4 class="mb-1 text-warning">RM{{ number_format($sale->price_per_kg, 2) }}</h4>
                            <small class="text-muted">Per Kilogram</small>
                        </div>
                    </div>
                </div>

                <!-- Total Amount -->
                <div class="text-center p-4 bg-success text-white rounded mb-4">
                    <i class="fas fa-dollar-sign fa-3x mb-3"></i>
                    <h3 class="mb-2">RM{{ number_format($sale->total_amount, 2) }}</h3>
                    <p class="mb-0">Total Sale Amount</p>
                </div>

                <!-- Additional Info -->
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <strong class="text-muted">Variety:</strong><br>
                            <span class="badge bg-info fs-6 px-3 py-2">{{ $sale->variety ?: 'Mixed' }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <strong class="text-muted">Payment Status:</strong><br>
                            @switch($sale->payment_status)
                                @case('paid')
                                    <span class="badge bg-success fs-6 px-3 py-2">
                                        <i class="fas fa-check me-1"></i>Paid
                                    </span>
                                    @break
                                @case('pending')
                                    <span class="badge bg-warning fs-6 px-3 py-2">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                    @break
                                @case('partial')
                                    <span class="badge bg-info fs-6 px-3 py-2">
                                        <i class="fas fa-hourglass-half me-1"></i>Partial
                                    </span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                </div>

                <!-- Harvest Batch Info -->
                @if($sale->harvestBatch)
                <div class="alert alert-info">
                    <i class="fas fa-seedling me-2"></i>
                    <strong>Harvest Batch:</strong>
                    <a href="{{ route('sales.batch-detail', $sale->harvestBatch) }}" class="text-decoration-none ms-2">
                        Batch #{{ $sale->harvestBatch->id }} - {{ $sale->harvestBatch->harvest_date->format('M d, Y') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Customer Information Card -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
            </div>
            <div class="card-body">
                @if($sale->customer)
                    <!-- Customer Avatar/Icon -->
                    <div class="text-center mb-4">
                        <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x text-white"></i>
                        </div>
                        <h4 class="mt-3 mb-1">{{ $sale->customer->name }}</h4>
                        <span class="badge bg-secondary px-3 py-2">{{ ucfirst($sale->customer->customer_type) }}</span>
                    </div>

                    <!-- Customer Details -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-phone fa-lg text-success me-3"></i>
                                <div>
                                    <strong>Phone Number</strong><br>
                                    <span class="text-muted">{{ $sale->customer->phone ?: 'No phone number' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-envelope fa-lg text-primary me-3"></i>
                                <div>
                                    <strong>Email Address</strong><br>
                                    <span class="text-muted">{{ $sale->customer->email ?: 'No email address' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-map-marker-alt fa-lg text-danger me-3"></i>
                                <div>
                                    <strong>Location</strong><br>
                                    <span class="text-muted">{{ $sale->customer->location ?: 'No location specified' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Actions -->
                    <div class="text-center mt-4">
                        <a href="{{ route('customers.show', $sale->customer) }}" class="btn btn-outline-info">
                            <i class="fas fa-user me-2"></i>View Customer Details
                        </a>
                    </div>
                @else
                    <!-- Walk-in Customer -->
                    <div class="text-center py-5">
                        <i class="fas fa-walking fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Walk-in Customer</h4>
                        <p class="text-muted">This sale was made to a walk-in customer with no recorded details.</p>
                        <div class="bg-light rounded p-3 mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Consider registering frequent customers to track their purchase history.
                            </small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Additional Information Row -->
<div class="row">
    <!-- Notes Section -->
    @if($sale->notes)
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes</h5>
            </div>
            <div class="card-body">
                <div class="bg-light p-3 rounded">
                    <i class="fas fa-quote-left text-muted me-2"></i>
                    {{ $sale->notes }}
                    <i class="fas fa-quote-right text-muted ms-2"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Timestamps Section -->
    <div class="col-lg-{{ $sale->notes ? '4' : '12' }} mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Timestamps</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex align-items-center p-2 bg-light rounded">
                        <i class="fas fa-plus-circle text-success me-3"></i>
                        <div>
                            <strong>Created</strong><br>
                            <small class="text-muted">{{ $sale->created_at->format('F d, Y g:i A') }}</small>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex align-items-center p-2 bg-light rounded">
                        <i class="fas fa-edit text-primary me-3"></i>
                        <div>
                            <strong>Last Updated</strong><br>
                            <small class="text-muted">{{ $sale->updated_at->format('F d, Y g:i A') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Back Button -->
<div class="row">
    <div class="col-12 text-center">
        <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-lg px-5">
            <i class="fas fa-arrow-left me-2"></i>Back to Sales
        </a>
    </div>
</div>
@endsection
