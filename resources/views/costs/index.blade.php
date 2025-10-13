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
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter Records</h6>
            </div>
            <div class="card-body bg-white">
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
    <div class="col-lg-4 mb-3">
        <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #dc3545, #c82333);">
            <div class="card-body py-4">
                <i class="fas fa-dollar-sign fa-2x mb-3"></i>
                <h3 class="mb-2">RM{{ number_format($totalCosts, 2) }}</h3>
                <p class="mb-1 fw-bold">Total Costs</p>
                <small class="opacity-75">All Expenses</small>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-3">
        <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #17a2b8, #138496);">
            <div class="card-body py-4">
                <i class="fas fa-list fa-2x mb-3"></i>
                <h3 class="mb-2">{{ $costsByCategory->count() }}</h3>
                <p class="mb-1 fw-bold">Categories</p>
                <small class="opacity-75">Cost Types</small>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-3">
        <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #007bff, #0056b3);">
            <div class="card-body py-4">
                <i class="fas fa-calendar fa-2x mb-3"></i>
                <h3 class="mb-2">{{ $costs->total() }}</h3>
                <p class="mb-1 fw-bold">Total Records</p>
                <small class="opacity-75">Expense Entries</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Cost Records Table -->
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2 text-primary"></i>All Cost Records
                    </h5>
                    <small class="text-muted">{{ $costs->total() }} total records</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3">Date & Category</th>
                                <th class="py-3">Description</th>
                                <th class="py-3 text-end">Amount</th>
                                <th class="py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($costs as $cost)
                            <tr class="align-middle">
                                <td class="py-3">
                                    <div class="fw-bold">{{ $cost->date->format('M d, Y') }}</div>
                                    <span class="badge bg-secondary px-2 py-1">
                                        @switch($cost->category)
                                            @case('seeds')
                                                <i class="fas fa-seedling me-1"></i>
                                            @break
                                            @case('fertilizer')
                                                <i class="fas fa-tint me-1"></i>
                                            @break
                                            @case('equipment')
                                                <i class="fas fa-tools me-1"></i>
                                            @break
                                            @case('labor')
                                                <i class="fas fa-users me-1"></i>
                                            @break
                                            @case('maintenance')
                                                <i class="fas fa-wrench me-1"></i>
                                            @break
                                            @default
                                                <i class="fas fa-receipt me-1"></i>
                                        @endswitch
                                        {{ ucfirst($cost->category) }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold">{{ Str::limit($cost->description, 40) }}</div>
                                    @if($cost->supplier)
                                        <small class="text-muted">
                                            <i class="fas fa-store me-1"></i>{{ $cost->supplier }}
                                        </small>
                                    @endif
                                </td>
                                <td class="py-3 text-end">
                                    <div class="fw-bold fs-5 text-danger">RM{{ number_format($cost->amount, 2) }}</div>
                                </td>
                                <td class="py-3 text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('costs.show', $cost) }}" class="btn btn-outline-info btn-sm" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('costs.edit', $cost) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form action="{{ route('costs.destroy', $cost) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger"
                                                                onclick="return confirm('Are you sure you want to delete this cost record?')">
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
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="fas fa-receipt fa-3x mb-3 opacity-50"></i>
                                    <div class="h5">No cost records found</div>
                                    <p>Start tracking your expenses by <a href="{{ route('costs.create') }}" class="text-decoration-none">adding your first cost record</a>.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($costs->hasPages())
                <div class="d-flex justify-content-center mt-4 px-3 pb-3">
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
