@extends('layouts.app')

@section('title', 'Sales by Harvest Batches')
@section('page-title', 'Sales by Harvest Batches')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <h2><i class="fas fa-layer-group me-2"></i>Sales by Harvest Batches</h2>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary me-2">
            <i class="fas fa-list me-2"></i>All Sales
        </a>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Sale
        </a>
    </div>
</div>

<!-- Date Filter Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Harvest Batches</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('sales.batches') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="date_from" class="form-label">Harvest From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="date_to" class="form-label">Harvest To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 me-2">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('sales.batches') }}" class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Harvest Batches -->
<div class="row">
    @forelse($harvestBatches as $batch)
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-seedling me-2"></i>Batch #{{ $batch->id }}
                </h5>
                <span class="badge bg-info">{{ $batch->sales->count() }} Sales</span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Harvest Date:</strong><br>
                        <span class="text-muted">{{ $batch->harvest_date->format('M d, Y') }}</span>
                    </div>
                    <div class="col-6">
                        <strong>Variety:</strong><br>
                        <span class="text-muted">{{ $batch->variety ?: 'Mixed' }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Harvested:</strong><br>
                        <span class="text-success">{{ number_format($batch->quantity_kg, 2) }} kg</span>
                    </div>
                    <div class="col-6">
                        <strong>Sold:</strong><br>
                        <span class="text-primary">{{ number_format($batch->total_quantity_sold, 2) }} kg</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Allocated:</strong><br>
                        <span class="text-info">{{ number_format($batch->total_quantity_allocated, 2) }} kg</span>
                    </div>
                    <div class="col-6">
                        <strong>Available:</strong><br>
                        <span class="text-{{ $batch->available_quantity > 0 ? 'success' : 'danger' }}">{{ number_format($batch->available_quantity, 2) }} kg</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Revenue:</strong><br>
                        <span class="text-success">RM{{ number_format($batch->total_revenue, 2) }}</span>
                    </div>
                    <div class="col-6">
                        <strong>Status:</strong><br>
                        <span class="badge bg-{{ $batch->batch_status_color }}">{{ $batch->batch_status_label }}</span>
                    </div>
                </div>

                @if($batch->sales->count() > 0)
                <div class="mb-3">
                    <strong>Recent Sales:</strong>
                    <div class="mt-2">
                        @foreach($batch->sales->take(3) as $sale)
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <small>
                                {{ $sale->sale_date->format('M d') }} - 
                                {{ $sale->customer ? $sale->customer->name : 'Walk-in' }}
                            </small>
                            <small class="text-muted">{{ number_format($sale->quantity_kg, 2) }}kg</small>
                        </div>
                        @endforeach
                        @if($batch->sales->count() > 3)
                        <small class="text-muted">+ {{ $batch->sales->count() - 3 }} more sales</small>
                        @endif
                    </div>
                </div>
                @endif

                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $batch->fulfillment_percentage }}%" 
                         aria-valuenow="{{ $batch->fulfillment_percentage }}" 
                         aria-valuemin="0" aria-valuemax="100"
                         title="Sold: {{ $batch->fulfillment_percentage }}%">
                    </div>
                    @if($batch->allocation_percentage > $batch->fulfillment_percentage)
                    <div class="progress-bar bg-info" role="progressbar" 
                         style="width: {{ $batch->allocation_percentage - $batch->fulfillment_percentage }}%" 
                         title="Allocated but not paid: {{ $batch->allocation_percentage - $batch->fulfillment_percentage }}%">
                    </div>
                    @endif
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">
                        <span class="text-success">■</span> {{ $batch->fulfillment_percentage }}% sold
                        @if($batch->allocation_percentage > $batch->fulfillment_percentage)
                        <span class="text-info ms-2">■</span> {{ number_format($batch->allocation_percentage - $batch->fulfillment_percentage, 1) }}% pending
                        @endif
                    </small>
                    <small class="text-muted">{{ $batch->allocation_percentage }}% allocated</small>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('sales.batch-detail', $batch) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye me-1"></i>View Details
                </a>
                @if($batch->available_quantity > 0)
                <a href="{{ route('sales.create', ['batch_id' => $batch->id]) }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-plus me-1"></i>Add Sale
                </a>
                @else
                <span class="btn btn-outline-secondary btn-sm disabled">
                    <i class="fas fa-check me-1"></i>Fully Allocated
                </span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No harvest batches found</h4>
                <p class="text-muted">Create some harvest records first to see batch sales tracking.</p>
                <a href="{{ route('harvests.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Harvest Record
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($harvestBatches->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $harvestBatches->links() }}
</div>
@endif
@endsection