@extends('layouts.app')

@section('title', 'Price Management')
@section('page-title', 'Price Management')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <h2><i class="fas fa-tags me-2"></i>Price Management</h2>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('prices.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>New Price
        </a>
    </div>
</div>

<!-- Active Prices -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Current Active Prices</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($activePrices as $price)
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-title">{{ $price->variety ?: 'All Varieties' }}</h6>
                                <h4 class="text-success">RM{{ number_format($price->price_per_kg, 2) }}/kg</h4>
                                <span class="badge bg-info">{{ ucfirst($price->customer_type) }}</span>
                                <br><small class="text-muted">From {{ $price->effective_from->format('M d, Y') }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <p class="text-muted text-center">No active prices set. <a href="{{ route('prices.create') }}">Create your first price</a>.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
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
                <form method="GET" action="{{ route('prices.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="date_from" class="form-label">From Date (Effective)</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="date_to" class="form-label">To Date (Effective)</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 me-2">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('prices.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- All Price Records -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Price Records</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Variety</th>
                        <th>Customer Type</th>
                        <th>Price/kg</th>
                        <th>Effective From</th>
                        <th>Effective To</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prices as $price)
                    <tr>
                        <td>{{ $price->variety ?: 'All Varieties' }}</td>
                        <td>
                            @switch($price->customer_type)
                                @case('individual')
                                    <span class="badge bg-warning">Individual</span>
                                    @break
                                @case('retailer')
                                    <span class="badge bg-success">Retailer</span>
                                    @break
                                @case('wholesaler')
                                    <span class="badge bg-info">Wholesaler</span>
                                    @break
                            @endswitch
                        </td>
                        <td><strong>RM{{ number_format($price->price_per_kg, 2) }}</strong></td>
                        <td>{{ $price->effective_from->format('M d, Y') }}</td>
                        <td>{{ $price->effective_to ? $price->effective_to->format('M d, Y') : 'Ongoing' }}</td>
                        <td>
                            @if($price->is_active && $price->effective_from <= now() && (!$price->effective_to || $price->effective_to >= now()))
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('prices.show', $price) }}" class="btn btn-outline-info btn-sm rounded-pill" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('prices.edit', $price) }}" class="btn btn-outline-primary btn-sm rounded-pill" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('prices.destroy', $price) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this price record?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No price records found. <a href="{{ route('prices.create') }}">Create your first price</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($prices->hasPages())
        <div class="d-flex justify-content-center">
            {{ $prices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
