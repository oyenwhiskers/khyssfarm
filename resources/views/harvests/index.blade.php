@extends('layouts.app')

@section('title', 'Harvest Records')
@section('page-title', 'Harvest Records')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <h2><i class="fas fa-seedling me-2"></i>Harvest Records</h2>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('harvests.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>New Harvest
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
                <form method="GET" action="{{ route('harvests.index') }}" class="row g-3">
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
                        <a href="{{ route('harvests.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
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
    <div class="col-lg-4 mb-3">
        <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #28a745, #20c997);">
            <div class="card-body py-4">
                <i class="fas fa-weight fa-2x mb-3"></i>
                <h3 class="mb-2">{{ number_format($totalYield, 2) }} kg</h3>
                <p class="mb-1 fw-bold">Total Yield</p>
                <small class="opacity-75">All Harvests Combined</small>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-3">
        <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #17a2b8, #138496);">
            <div class="card-body py-4">
                <i class="fas fa-chart-bar fa-2x mb-3"></i>
                <h3 class="mb-2">{{ number_format($averageYield, 2) }} kg</h3>
                <p class="mb-1 fw-bold">Average per Harvest</p>
                <small class="opacity-75">Performance Metric</small>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-3">
        <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #007bff, #0056b3);">
            <div class="card-body py-4">
                <i class="fas fa-calendar fa-2x mb-3"></i>
                <h3 class="mb-2">{{ $harvests->total() }}</h3>
                <p class="mb-1 fw-bold">Total Records</p>
                <small class="opacity-75">Harvest Sessions</small>
            </div>
        </div>
    </div>
</div>

<!-- Harvest Records Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2 text-primary"></i>All Harvest Records
            </h5>
            <small class="text-muted">{{ $harvests->total() }} total records</small>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">Harvest Date</th>
                        <th class="py-3 text-center">Quantity</th>
                        <th class="py-3">Variety & Location</th>
                        <th class="py-3">Notes</th>
                        <th class="py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($harvests as $harvest)
                    <tr class="align-middle">
                        <td class="py-3">
                            <div class="fw-bold">{{ $harvest->harvest_date->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $harvest->harvest_date->format('l') }}</small>
                        </td>
                        <td class="py-3 text-center">
                            <div class="fw-bold fs-5 text-success">{{ number_format($harvest->quantity_kg, 2) }} kg</div>
                            @if($harvest->sales && $harvest->sales->count() > 0)
                                <small class="text-muted">{{ $harvest->sales->count() }} sales from this batch</small>
                            @else
                                <small class="text-muted">No sales yet</small>
                            @endif
                        </td>
                        <td class="py-3">
                            <div class="fw-bold">{{ $harvest->variety ?: 'Mixed Variety' }}</div>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $harvest->field_location ?: 'Location not specified' }}
                            </small>
                        </td>
                        <td class="py-3">
                            @if($harvest->notes)
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $harvest->notes }}">
                                    {{ Str::limit($harvest->notes, 50) }}
                                </div>
                            @else
                                <span class="text-muted">No notes</span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('harvests.show', $harvest) }}" class="btn btn-outline-info btn-sm" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('harvests.edit', $harvest) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($harvest->sales && $harvest->sales->count() > 0)
                                        <li>
                                            <a href="{{ route('sales.batch-detail', $harvest) }}" class="dropdown-item">
                                                <i class="fas fa-shopping-cart me-2"></i>View Sales ({{ $harvest->sales->count() }})
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @endif
                                        <li>
                                            <form action="{{ route('harvests.destroy', $harvest) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                        onclick="return confirm('Are you sure you want to delete this harvest record?')">
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
                            <i class="fas fa-seedling fa-3x mb-3 opacity-50"></i>
                            <div class="h5">No harvest records found</div>
                            <p>Start tracking your harvests by <a href="{{ route('harvests.create') }}" class="text-decoration-none">adding your first harvest record</a>.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($harvests->hasPages())
        <div class="d-flex justify-content-center mt-4 px-3 pb-3">
            {{ $harvests->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
