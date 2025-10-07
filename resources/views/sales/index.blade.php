@extends('layouts.app')

@section('title', 'Sales Tracking')
@section('page-title', 'Sales Tracking')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <h2><i class="fas fa-cash-register me-2"></i>Sales Records</h2>
    </div>
    <div class="col-lg-4 text-end">
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
                <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Records</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('sales.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
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
    <div class="col-lg-4">
        <div class="card text-center bg-success text-white">
            <div class="card-body">
                <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                <h4>RM{{ number_format($totalRevenue, 2) }}</h4>
                <p class="mb-0">Total Revenue</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card text-center bg-info text-white">
            <div class="card-body">
                <i class="fas fa-weight fa-2x mb-2"></i>
                <h4>{{ number_format($totalQuantitySold, 2) }} kg</h4>
                <p class="mb-0">Total Quantity Sold</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card text-center bg-primary text-white">
            <div class="card-body">
                <i class="fas fa-chart-line fa-2x mb-2"></i>
                <h4>RM{{ number_format($averagePrice, 2) }}</h4>
                <p class="mb-0">Average Price/kg</p>
            </div>
        </div>
    </div>
</div>

<!-- Sales Records Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Sales Records</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Quantity (kg)</th>
                        <th>Price/kg</th>
                        <th>Total Amount</th>
                        <th>Variety</th>
                        <th>Payment Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                        <td>
                            @if($sale->customer)
                                <strong>{{ $sale->customer->name }}</strong>
                                <br><small class="text-muted">{{ ucfirst($sale->customer->customer_type) }}</small>
                            @else
                                <span class="text-muted">Walk-in Customer</span>
                            @endif
                        </td>
                        <td><strong>{{ number_format($sale->quantity_kg, 2) }} kg</strong></td>
                        <td>RM{{ number_format($sale->price_per_kg, 2) }}</td>
                        <td><strong>RM{{ number_format($sale->total_amount, 2) }}</strong></td>
                        <td>{{ $sale->variety ?: 'Mixed' }}</td>
                        <td>
                            @switch($sale->payment_status)
                                @case('paid')
                                    <span class="badge bg-success">Paid</span>
                                    @break
                                @case('pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @break
                                @case('partial')
                                    <span class="badge bg-info">Partial</span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-info btn-sm rounded-pill" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-outline-primary btn-sm rounded-pill" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sales.destroy', $sale) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this sale record?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No sales records found. <a href="{{ route('sales.create') }}">Record your first sale</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sales->hasPages())
        <div class="d-flex justify-content-center">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
