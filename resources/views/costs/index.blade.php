@extends('layouts.app')

@section('title', 'Cost Management')
@section('page-title', 'Cost Management')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <h2><i class="fas fa-receipt me-2"></i>Cost Records</h2>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('costs.create') }}" class="btn btn-warning">
            <i class="fas fa-plus me-2"></i>New Cost
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
                <form method="GET" action="{{ route('costs.index') }}" class="row g-3">
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
                        <a href="{{ route('costs.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
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
        <div class="card text-center bg-danger text-white">
            <div class="card-body">
                <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                <h4>RM{{ number_format($totalCosts, 2) }}</h4>
                <p class="mb-0">Total Costs</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card text-center bg-info text-white">
            <div class="card-body">
                <i class="fas fa-list fa-2x mb-2"></i>
                <h4>{{ $costsByCategory->count() }}</h4>
                <p class="mb-0">Categories</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card text-center bg-primary text-white">
            <div class="card-body">
                <i class="fas fa-calendar fa-2x mb-2"></i>
                <h4>{{ $costs->total() }}</h4>
                <p class="mb-0">Total Records</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Cost Records Table -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Cost Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Supplier</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($costs as $cost)
                            <tr>
                                <td>{{ $cost->date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($cost->category) }}</span>
                                </td>
                                <td>{{ Str::limit($cost->description, 30) }}</td>
                                <td><strong>RM{{ number_format($cost->amount, 2) }}</strong></td>
                                <td>{{ $cost->supplier ?: '-' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('costs.show', $cost) }}" class="btn btn-outline-info btn-sm rounded-pill" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('costs.edit', $cost) }}" class="btn btn-outline-primary btn-sm rounded-pill" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('costs.destroy', $cost) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this cost record?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No cost records found. <a href="{{ route('costs.create') }}">Add your first cost record</a>.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($costs->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $costs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Cost Categories -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Costs by Category</h5>
            </div>
            <div class="card-body">
                @forelse($costsByCategory as $category)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <strong>{{ ucfirst($category->category) }}</strong>
                    </div>
                    <div class="text-end">
                        <strong>RM{{ number_format($category->total, 2) }}</strong>
                    </div>
                </div>
                @empty
                <p class="text-muted">No cost data available.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
