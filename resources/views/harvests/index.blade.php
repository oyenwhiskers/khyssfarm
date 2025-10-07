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
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Records</h6>
            </div>
            <div class="card-body">
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
    <div class="col-lg-4">
        <div class="card text-center bg-success text-white">
            <div class="card-body">
                <i class="fas fa-weight fa-2x mb-2"></i>
                <h4>{{ number_format($totalYield, 2) }} kg</h4>
                <p class="mb-0">Total Yield</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card text-center bg-info text-white">
            <div class="card-body">
                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                <h4>{{ number_format($averageYield, 2) }} kg</h4>
                <p class="mb-0">Average per Harvest</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card text-center bg-primary text-white">
            <div class="card-body">
                <i class="fas fa-calendar fa-2x mb-2"></i>
                <h4>{{ $harvests->total() }}</h4>
                <p class="mb-0">Total Records</p>
            </div>
        </div>
    </div>
</div>

<!-- Harvest Records Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Harvest Records</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Quantity (kg)</th>
                        <th>Variety</th>
                        <th>Location</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($harvests as $harvest)
                    <tr>
                        <td>{{ $harvest->harvest_date->format('M d, Y') }}</td>
                        <td><strong>{{ number_format($harvest->quantity_kg, 2) }} kg</strong></td>
                        <td>{{ $harvest->variety ?: 'Mixed' }}</td>
                        <td>{{ $harvest->field_location ?: '-' }}</td>
                        <td>{{ Str::limit($harvest->notes, 50) ?: '-' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('harvests.show', $harvest) }}" class="btn btn-outline-info btn-sm rounded-pill" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('harvests.edit', $harvest) }}" class="btn btn-outline-primary btn-sm rounded-pill" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('harvests.destroy', $harvest) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this harvest record?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No harvest records found. <a href="{{ route('harvests.create') }}">Add your first harvest record</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($harvests->hasPages())
        <div class="d-flex justify-content-center">
            {{ $harvests->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
