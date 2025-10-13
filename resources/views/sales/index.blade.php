@extends('layouts.app')

@section('title', 'Sales Tracking')
@section('page-title', 'Sales Tracking')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <h2><i class="fas fa-cash-register me-2"></i>Sales Records</h2>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('sales.batches') }}" class="btn btn-outline-info me-2">
            <i class="fas fa-layer-group me-2"></i>View by Batches
        </a>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Sale
        </a>
    </div>
</div>

<!-- Date Filter Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter Records</h6>
            </div>
            <div class="card-body bg-white">
                <form method="GET" action="{{ route('sales.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="batch_id" class="form-label">Harvest Batch</label>
                        <select class="form-select" id="batch_id" name="batch_id">
                            <option value="">All Batches</option>
                            @foreach($harvestBatches as $batch)
                                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                    Batch #{{ $batch->id }} - {{ $batch->harvest_date->format('M d, Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 me-2">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-lg-3 mb-3">
        <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #28a745, #20c997);">
            <div class="card-body py-4">
                <i class="fas fa-dollar-sign fa-2x mb-3"></i>
                <h3 class="mb-2">RM{{ number_format($totalRevenue, 2) }}</h3>
                <p class="mb-1 fw-bold">Confirmed Revenue</p>
                <small class="opacity-75">Paid Sales Only</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-3">
        <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #ffc107, #fd7e14);">
            <div class="card-body py-4">
                <i class="fas fa-clock fa-2x mb-3"></i>
                <h3 class="mb-2">RM{{ number_format($pendingRevenue, 2) }}</h3>
                <p class="mb-1 fw-bold">Pending Revenue</p>
                <small class="opacity-75">Awaiting Payment</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-3">
        <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #17a2b8, #138496);">
            <div class="card-body py-4">
                <i class="fas fa-weight fa-2x mb-3"></i>
                <h3 class="mb-2">{{ number_format($totalQuantitySold, 2) }} kg</h3>
                <p class="mb-1 fw-bold">Quantity Sold</p>
                <small class="opacity-75">Paid Sales Only</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-3">
        <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #007bff, #0056b3);">
            <div class="card-body py-4">
                <i class="fas fa-chart-line fa-2x mb-3"></i>
                <h3 class="mb-2">RM{{ number_format($averagePrice, 2) }}</h3>
                <p class="mb-1 fw-bold">Average Price/kg</p>
                <small class="opacity-75">Paid Sales Only</small>
            </div>
        </div>
    </div>
</div>

<!-- Sales Records Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2 text-primary"></i>All Sales Records
            </h5>
            <small class="text-muted">{{ $sales->total() }} total records</small>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">Sale Date</th>
                        <th class="py-3">Customer</th>
                        <th class="py-3">Harvest Batch</th>
                        <th class="py-3 text-center">Quantity</th>
                        <th class="py-3 text-end">Total Amount</th>
                        <th class="py-3 text-center">Payment</th>
                        <th class="py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr class="align-middle">
                        <td class="py-3">
                            <div class="fw-bold">{{ $sale->sale_date->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $sale->sale_date->format('l') }}</small>
                        </td>
                        <td class="py-3">
                            @if($sale->customer)
                                <div class="fw-bold">{{ $sale->customer->name }}</div>
                                <small class="text-muted">{{ ucfirst($sale->customer->customer_type) }}</small>
                            @else
                                <div class="text-muted">Walk-in Customer</div>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($sale->harvestBatch)
                                <a href="{{ route('sales.batch-detail', $sale->harvestBatch) }}" class="text-decoration-none">
                                    <div class="fw-bold text-primary">Batch #{{ $sale->harvestBatch->id }}</div>
                                    <small class="text-muted">{{ $sale->harvestBatch->harvest_date->format('M d') }} - {{ $sale->harvestBatch->variety ?: 'Mixed' }}</small>
                                </a>
                            @else
                                <div class="text-muted">No batch</div>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            <div class="fw-bold text-success">{{ number_format($sale->quantity_kg, 2) }} kg</div>
                            <small class="text-muted">RM{{ number_format($sale->price_per_kg, 2) }}/kg</small>
                        </td>
                        <td class="py-3 text-end">
                            <div class="fw-bold fs-5 text-primary">RM{{ number_format($sale->total_amount, 2) }}</div>
                        </td>
                        <td class="py-3 text-center">
                            @switch($sale->payment_status)
                                @case('paid')
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check me-1"></i>Paid
                                    </span>
                                    @break
                                @case('pending')
                                    <span class="badge bg-warning px-3 py-2">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                    @break
                                @case('partial')
                                    <span class="badge bg-info px-3 py-2">
                                        <i class="fas fa-hourglass-half me-1"></i>Partial
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="py-3 text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-info btn-sm" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('sales.receipt', $sale) }}" class="dropdown-item" target="_blank">
                                                <i class="fas fa-print me-2"></i>Print Receipt
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('sales.destroy', $sale) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                        onclick="return confirm('Are you sure you want to delete this sale record?')">
                                                    <i class="fas fa-trash me-2"></i>Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-shopping-cart fa-3x mb-3 opacity-50"></i>
                            <div class="h5">No sales records found</div>
                            <p>Start tracking your sales by <a href="{{ route('sales.create') }}" class="text-decoration-none">recording your first sale</a>.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sales->hasPages())
        <div class="d-flex justify-content-center mt-4 px-3 pb-3">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
