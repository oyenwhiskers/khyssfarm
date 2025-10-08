@extends('layouts.app')

@section('title', 'Customer Management')
@section('page-title', 'Customer Management')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <h2><i class="fas fa-users me-2"></i>Customer Database</h2>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Customer
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
                <form method="GET" action="{{ route('customers.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="date_from" class="form-label">From Date (Registration)</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="date_to" class="form-label">To Date (Registration)</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 me-2">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Customer Statistics -->
<div class="row mb-4">
    <div class="col-lg-3">
        <div class="card text-center bg-primary text-white">
            <div class="card-body">
                <i class="fas fa-users fa-2x mb-2"></i>
                <h4>{{ $customers->total() }}</h4>
                <p class="mb-0">Total Customers</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card text-center bg-success text-white">
            <div class="card-body">
                <i class="fas fa-store fa-2x mb-2"></i>
                <h4>{{ $customers->where('customer_type', 'retailer')->count() }}</h4>
                <p class="mb-0">Retailers</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card text-center bg-info text-white">
            <div class="card-body">
                <i class="fas fa-warehouse fa-2x mb-2"></i>
                <h4>{{ $customers->where('customer_type', 'wholesaler')->count() }}</h4>
                <p class="mb-0">Wholesalers</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card text-center bg-warning text-white">
            <div class="card-body">
                <i class="fas fa-user fa-2x mb-2"></i>
                <h4>{{ $customers->where('customer_type', 'individual')->count() }}</h4>
                <p class="mb-0">Individuals</p>
            </div>
        </div>
    </div>
</div>

<!-- Customers Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Customers</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Source</th>
                        <th>Total Purchases</th>
                        <th>Total Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>
                            <strong>{{ $customer->name }}</strong>
                            @if($customer->email)
                                <br><small class="text-muted">{{ $customer->email }}</small>
                            @endif
                        </td>
                        <td>
                            @switch($customer->customer_type)
                                @case('retailer')
                                    <span class="badge bg-success">Retailer</span>
                                    @break
                                @case('wholesaler')
                                    <span class="badge bg-info">Wholesaler</span>
                                    @break
                                @case('individual')
                                    <span class="badge bg-warning">Individual</span>
                                    @break
                            @endswitch
                        </td>
                        <td>{{ $customer->phone ?: '-' }}</td>
                        <td>{{ $customer->location ?: '-' }}</td>
                        <td>
                            @if($customer->source)
                                <span class="badge bg-primary">{{ \App\Models\Customer::getSourceOptions()[$customer->source] ?? $customer->source }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td><strong>RM{{ number_format($customer->sales_sum_total_amount ?? 0, 2) }}</strong></td>
                        <td>{{ number_format($customer->sales_sum_quantity_kg ?? 0, 2) }} kg</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-info btn-sm rounded-pill" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-primary btn-sm rounded-pill" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this customer?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No customers found. <a href="{{ route('customers.create') }}">Add your first customer</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($customers->hasPages())
        <div class="d-flex justify-content-center">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
