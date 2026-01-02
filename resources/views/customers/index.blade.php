@extends('layouts.app')

@section('title', 'Customer Management')
@section('page-title', 'Customer Management')

@section('content')
<style>
    .customer-type-tabs {
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 0;
    }
    
    .customer-type-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        font-weight: 600;
        padding: 1rem 1.5rem;
        transition: all 0.2s ease;
        position: relative;
    }
    
    .customer-type-tabs .nav-link:hover {
        color: #495057;
        background-color: #f8f9fa;
        border-bottom-color: #adb5bd;
    }
    
    .customer-type-tabs .nav-link.active {
        color: #007bff;
        background-color: transparent;
        border-bottom-color: #007bff;
    }
    
    .tab-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        margin-left: 0.5rem;
    }
    
    .tab-badge-all {
        background-color: #e7f3ff;
        color: #0066cc;
    }
    
    .tab-badge-retailer {
        background-color: #d4edda;
        color: #155724;
    }
    
    .tab-badge-individual {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .tab-badge-wholesaler {
        background-color: #d1ecf1;
        color: #0c5460;
    }
</style>

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
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter Records</h6>
            </div>
            <div class="card-body bg-white">
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

<!-- Customer Type Tabs -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <ul class="nav nav-tabs customer-type-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{ $customerType === 'all' ? 'active' : '' }}" 
                           href="{{ route('customers.index', array_merge(request()->except('type'), ['type' => 'all'])) }}">
                            <i class="fas fa-users me-2"></i>All Customers
                            <span class="tab-badge tab-badge-all">{{ $totalCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $customerType === 'retailer' ? 'active' : '' }}" 
                           href="{{ route('customers.index', array_merge(request()->except('type'), ['type' => 'retailer'])) }}">
                            <i class="fas fa-store me-2"></i>Retailer
                            <span class="tab-badge tab-badge-retailer">{{ $retailerCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $customerType === 'individual' ? 'active' : '' }}" 
                           href="{{ route('customers.index', array_merge(request()->except('type'), ['type' => 'individual'])) }}">
                            <i class="fas fa-user me-2"></i>Individual
                            <span class="tab-badge tab-badge-individual">{{ $individualCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $customerType === 'wholesaler' ? 'active' : '' }}" 
                           href="{{ route('customers.index', array_merge(request()->except('type'), ['type' => 'wholesaler'])) }}">
                            <i class="fas fa-warehouse me-2"></i>Wholesaler
                            <span class="tab-badge tab-badge-wholesaler">{{ $wholesalerCount }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Customer Statistics -->
<div class="row mb-4">
    <div class="col-lg-3 mb-3">
        <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #007bff, #0056b3);">
            <div class="card-body py-4">
                <i class="fas fa-users fa-2x mb-3"></i>
                <h3 class="mb-2">{{ $customerCount }}</h3>
                <p class="mb-1 fw-bold">
                    @if($customerType === 'all')
                        Total Customers
                    @elseif($customerType === 'retailer')
                        Retailers
                    @elseif($customerType === 'individual')
                        Individuals
                    @else
                        Wholesalers
                    @endif
                </p>
                <small class="opacity-75">
                    @if($customerType === 'all')
                        All Customer Types
                    @else
                        {{ ucfirst($customerType) }} Type
                    @endif
                </small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-3">
        <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #28a745, #20c997);">
            <div class="card-body py-4">
                <i class="fas fa-dollar-sign fa-2x mb-3"></i>
                <h3 class="mb-2">RM{{ number_format($totalRevenue, 2) }}</h3>
                <p class="mb-1 fw-bold">Total Revenue</p>
                <small class="opacity-75">From Displayed Customers</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-3">
        <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #17a2b8, #138496);">
            <div class="card-body py-4">
                <i class="fas fa-chart-line fa-2x mb-3"></i>
                <h3 class="mb-2">RM{{ $customerCount > 0 ? number_format($totalRevenue / $customerCount, 2) : '0.00' }}</h3>
                <p class="mb-1 fw-bold">Avg Customer Value</p>
                <small class="opacity-75">Per Customer</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-3">
        <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #ffc107, #fd7e14);">
            <div class="card-body py-4">
                <i class="fas fa-weight fa-2x mb-3"></i>
                <h3 class="mb-2">{{ number_format($totalQuantity, 2) }} kg</h3>
                <p class="mb-1 fw-bold">Total Quantity</p>
                <small class="opacity-75">Purchased</small>
            </div>
        </div>
    </div>
</div>

<!-- Customers Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2 text-primary"></i>All Customers
            </h5>
            <small class="text-muted">{{ $customers->total() }} total customers</small>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">Customer</th>
                        <th class="py-3 text-center">Type</th>
                        <th class="py-3">Contact Info</th>
                        <th class="py-3 text-center">Purchase History</th>
                        <th class="py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr class="align-middle">
                        <td class="py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $customer->name }}</div>
                                    <small class="text-muted">
                                        @if($customer->email)
                                            <i class="fas fa-envelope me-1"></i>{{ $customer->email }}
                                        @else
                                            Customer ID: #{{ $customer->id }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 text-center">
                            @switch($customer->customer_type)
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
                                @case('individual')
                                    <span class="badge bg-warning px-3 py-2">
                                        <i class="fas fa-user me-1"></i>Individual
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="py-3">
                            <div class="mb-1">
                                <i class="fas fa-phone text-success me-2"></i>
                                <span class="fw-bold">{{ $customer->phone ?: 'No phone' }}</span>
                            </div>
                            <div>
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                <small class="text-muted">{{ $customer->location ?: 'No location' }}</small>
                            </div>
                            @if($customer->source)
                            <div class="mt-1">
                                <span class="badge bg-light text-dark">
                                    {{ \App\Models\Customer::getSourceOptions()[$customer->source] ?? $customer->source }}
                                </span>
                            </div>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            <div class="fw-bold fs-5 text-success">RM{{ number_format($customer->total_purchases, 2) }}</div>
                            <small class="text-muted">{{ number_format($customer->total_quantity, 2) }} kg purchased</small>
                        </td>
                        <td class="py-3 text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-info btn-sm" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($customer->sales && $customer->sales->count() > 0)
                                        <li>
                                            <a href="{{ route('sales.index', ['customer_id' => $customer->id]) }}" class="dropdown-item">
                                                <i class="fas fa-shopping-cart me-2"></i>View Sales History
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @endif
                                        <li>
                                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                        onclick="return confirm('Are you sure you want to delete this customer?')">
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
                            <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                            <div class="h5">No customers found</div>
                            <p>Build your customer database by <a href="{{ route('customers.create') }}" class="text-decoration-none">adding your first customer</a>.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($customers->hasPages())
        <div class="d-flex justify-content-center mt-4 px-3 pb-3">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
