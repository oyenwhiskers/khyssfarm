@extends('layouts.app')

@section('title', 'Resell Tracking')
@section('page-title', 'Resell Tracking')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h2><i class="fas fa-exchange-alt me-2"></i>Resell Tracking</h2>
            <p class="text-muted">Track chili purchases from suppliers and their resale performance</p>
        </div>
        <div class="col-lg-4 text-end">
            <a href="{{ route('resells.create') }}" class="btn btn-primary px-4 py-2">
                <i class="fas fa-plus me-2"></i>Record Purchase
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #dc3545, #c82333);">
                <div class="card-body py-4">
                    <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                    <h3 class="mb-2">RM {{ number_format($totalPurchases, 2) }}</h3>
                    <p class="mb-1 fw-bold">Total Purchases</p>
                    <small class="opacity-75">Investment Amount</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #28a745, #20c997);">
                <div class="card-body py-4">
                    <i class="fas fa-hand-holding-usd fa-2x mb-3"></i>
                    <h3 class="mb-2">RM {{ number_format($totalSales, 2) }}</h3>
                    <p class="mb-1 fw-bold">Total Sales</p>
                    <small class="opacity-75">Revenue Generated</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #007bff, #0056b3);">
                <div class="card-body py-4">
                    <i class="fas fa-chart-line fa-2x mb-3"></i>
                    <h3 class="mb-2">RM {{ number_format($totalProfit, 2) }}</h3>
                    <p class="mb-1 fw-bold">Total Profit</p>
                    <small class="opacity-75">Net Earnings</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #ffc107, #fd7e14);">
                <div class="card-body py-4">
                    <i class="fas fa-percentage fa-2x mb-3"></i>
                    <h3 class="mb-2">{{ number_format($averageMargin, 1) }}%</h3>
                    <p class="mb-1 fw-bold">Avg. Margin</p>
                    <small class="opacity-75">Profit Percentage</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light border-0 py-3">
            <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Records</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('resells.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="purchased" {{ request('status') == 'purchased' ? 'selected' : '' }}>Purchased</option>
                        <option value="partially_sold" {{ request('status') == 'partially_sold' ? 'selected' : '' }}>Partially Sold</option>
                        <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="supplier" class="form-label">Supplier</label>
                    <input type="text" name="supplier" id="supplier" class="form-control" value="{{ request('supplier') }}" placeholder="Search supplier...">
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-filter"></i>
                        </button>
                        <a href="{{ route('resells.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resells Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light border-0 py-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 text-dark">Resell Records</h4>
                    <p class="text-muted mb-0">Track your supplier purchases and resale performance</p>
                </div>
                <a href="{{ route('resells.create') }}" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-plus me-2"></i>New Purchase
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($resells->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <tr>
                                <th class="px-4 py-3 text-dark fw-semibold">Purchase Info</th>
                                <th class="px-3 py-3 text-dark fw-semibold">Supplier</th>
                                <th class="px-3 py-3 text-dark fw-semibold text-end">Purchase</th>
                                <th class="px-3 py-3 text-dark fw-semibold text-end">Sale</th>
                                <th class="px-3 py-3 text-dark fw-semibold text-center">Profit</th>
                                <th class="px-3 py-3 text-dark fw-semibold text-center">Status</th>
                                <th class="px-4 py-3 text-dark fw-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resells as $resell)
                                <tr class="align-middle">
                                    <td class="px-4 py-4">
                                        <div>
                                            <h6 class="mb-1 text-dark">{{ $resell->purchase_date->format('M d, Y') }}</h6>
                                            <small class="text-muted">
                                                {{ number_format($resell->purchase_quantity_kg, 2) }}kg
                                                @if($resell->variety)
                                                - {{ $resell->variety }}
                                                @endif
                                            </small>
                                            @if($resell->quality_grade)
                                                <br><span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1">{{ $resell->quality_grade }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-4">
                                        <div>
                                            <span class="text-dark fw-medium">{{ $resell->supplier_name }}</span>
                                            @if($resell->supplier_contact)
                                                <br><small class="text-muted">{{ $resell->supplier_contact }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-end">
                                        <div>
                                            <span class="text-danger fw-semibold">RM{{ number_format($resell->total_purchase_cost, 2) }}</span>
                                            <br><small class="text-muted">RM{{ number_format($resell->purchase_price_per_kg, 2) }}/kg</small>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-end">
                                        @if($resell->total_sale_amount)
                                            <div>
                                                <span class="text-success fw-semibold">RM{{ number_format($resell->total_sale_amount, 2) }}</span>
                                                <br><small class="text-muted">{{ $resell->resellSales->count() }} sale(s)</small>
                                                <br><small class="text-info">{{ number_format($resell->total_quantity_sold, 2) }}kg sold</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Not sold yet</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        @if($resell->total_profit)
                                            <div>
                                                <span class="badge bg-{{ $resell->total_profit > 0 ? 'success' : 'danger' }}-subtle text-{{ $resell->total_profit > 0 ? 'success' : 'danger' }} border border-{{ $resell->total_profit > 0 ? 'success' : 'danger' }}-subtle rounded-pill px-3 py-2">
                                                    RM{{ number_format($resell->total_profit, 2) }}
                                                </span>
                                                <br><small class="text-muted">{{ number_format($resell->average_profit_margin, 1) }}% avg</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <span class="badge bg-{{ $resell->status_color }}-subtle text-{{ $resell->status_color }} border border-{{ $resell->status_color }}-subtle rounded-pill px-3 py-2">
                                            {{ $resell->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('resells.show', $resell) }}" class="btn btn-outline-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($resell->status !== 'sold' && $resell->remaining_quantity > 0)
                                            <a href="{{ route('resells.record-sale', $resell) }}" class="btn btn-outline-success btn-sm" title="Record Sale">
                                                <i class="fas fa-dollar-sign"></i>
                                            </a>
                                            @endif
                                            <a href="{{ route('resells.edit', $resell) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('resells.destroy', $resell) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete" 
                                                        onclick="return confirm('Are you sure you want to delete this record?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="py-4">
                        <i class="fas fa-exchange-alt fa-4x text-primary mb-4 opacity-50"></i>
                        <h4 class="text-dark mb-2">No resell records found</h4>
                        <p class="text-muted mb-4">Start tracking your supplier purchases and resale performance</p>
                        <a href="{{ route('resells.create') }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-plus me-2"></i>Record Your First Purchase
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        @if($resells->hasPages())
            <div class="card-footer bg-light border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $resells->firstItem() }} to {{ $resells->lastItem() }} of {{ $resells->total() }} records
                    </div>
                    {{ $resells->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection