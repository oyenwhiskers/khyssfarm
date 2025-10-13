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
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Current Active Prices</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($activePrices as $price)
                    <div class="col-md-4 mb-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="fas fa-pepper-hot fa-2x text-success"></i>
                                </div>
                                <h6 class="card-title fw-bold">{{ $price->variety ?: 'All Varieties' }}</h6>
                                <h3 class="text-success mb-2">RM{{ number_format($price->price_per_kg, 2) }}</h3>
                                <small class="text-muted">per kilogram</small>
                                <div class="mt-2">
                                    @switch($price->customer_type)
                                        @case('individual')
                                            <span class="badge bg-warning px-3 py-2">
                                                <i class="fas fa-user me-1"></i>Individual
                                            </span>
                                            @break
                                        @case('retailer')
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fas fa-store me-1"></i>Retailer
                                            </span>
                                            @break
                                        @case('wholesaler')
                                            <span class="badge bg-info px-3 py-2">
                                                <i class="fas fa-warehouse me-1"></i>Wholesaler
                                            </span>
                                            @break
                                    @endswitch
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-calendar me-1"></i>From {{ $price->effective_from->format('M d, Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-4">
                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No active prices set</h5>
                        <p class="text-muted">Set up your pricing structure to start selling.</p>
                        <a href="{{ route('prices.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Create Your First Price
                        </a>
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
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter Records</h6>
            </div>
            <div class="card-body bg-white">
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
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2 text-primary"></i>All Price Records
            </h5>
            <small class="text-muted">{{ $prices->total() }} total records</small>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">Variety & Customer</th>
                        <th class="py-3 text-center">Price</th>
                        <th class="py-3">Effective Period</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prices as $price)
                    <tr class="align-middle">
                        <td class="py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-pepper-hot text-white"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $price->variety ?: 'All Varieties' }}</div>
                                    @switch($price->customer_type)
                                        @case('individual')
                                            <span class="badge bg-warning px-2 py-1">
                                                <i class="fas fa-user me-1"></i>Individual
                                            </span>
                                            @break
                                        @case('retailer')
                                            <span class="badge bg-success px-2 py-1">
                                                <i class="fas fa-store me-1"></i>Retailer
                                            </span>
                                            @break
                                        @case('wholesaler')
                                            <span class="badge bg-info px-2 py-1">
                                                <i class="fas fa-warehouse me-1"></i>Wholesaler
                                            </span>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </td>
                        <td class="py-3 text-center">
                            <div class="fw-bold fs-4 text-success">RM{{ number_format($price->price_per_kg, 2) }}</div>
                            <small class="text-muted">per kg</small>
                        </td>
                        <td class="py-3">
                            <div class="mb-1">
                                <strong>From:</strong> {{ $price->effective_from->format('M d, Y') }}
                            </div>
                            <div>
                                <strong>To:</strong> 
                                <span class="text-muted">{{ $price->effective_to ? $price->effective_to->format('M d, Y') : 'Ongoing' }}</span>
                            </div>
                        </td>
                        <td class="py-3 text-center">
                            @if($price->is_active && $price->effective_from <= now() && (!$price->effective_to || $price->effective_to >= now()))
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>Active
                                </span>
                            @else
                                <span class="badge bg-secondary px-3 py-2">
                                    <i class="fas fa-pause-circle me-1"></i>Inactive
                                </span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('prices.show', $price) }}" class="btn btn-outline-info btn-sm" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('prices.edit', $price) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <form action="{{ route('prices.destroy', $price) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                        onclick="return confirm('Are you sure you want to delete this price record?')">
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
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fas fa-tags fa-3x mb-3 opacity-50"></i>
                            <div class="h5">No price records found</div>
                            <p>Set up your pricing structure by <a href="{{ route('prices.create') }}" class="text-decoration-none">creating your first price</a>.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($prices->hasPages())
        <div class="d-flex justify-content-center mt-4 px-3 pb-3">
            {{ $prices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
