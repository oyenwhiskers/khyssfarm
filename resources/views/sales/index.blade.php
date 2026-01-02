@extends('layouts.app')

@section('title', 'Sales Tracking')
@section('page-title', 'Sales Tracking')

@section('content')
<style>
    /* Payment Status Dashboard Styles */
    .payment-status-card {
        border-radius: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .payment-status-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .payment-status-value {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .payment-status-label {
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    
    .payment-status-detail {
        font-size: 0.85rem;
        opacity: 0.8;
        margin-top: 0.5rem;
    }
    
    .revenue-breakdown {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        margin-top: 1rem;
        font-size: 0.8rem;
    }
    
    .breakdown-item {
        padding: 0.5rem;
        background-color: rgba(255,255,255,0.1);
        border-radius: 4px;
        text-align: center;
    }
    
    /* Month Header Styles */
    .month-header-sales {
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }
    
    .month-header-sales:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .month-header-sales .btn-link {
        padding: 1.25rem !important;
        font-weight: 600;
    }
    
    .month-header-sales .btn-link:focus {
        box-shadow: none;
    }
    
    .transition-icon-sales {
        transition: transform 0.3s ease;
        display: inline-block;
        transform-origin: center;
    }
    
    .btn-link[aria-expanded="true"] .transition-icon-sales {
        transform: rotate(180deg);
    }
    
    .month-label-sales {
        font-size: 1.15rem;
        color: #1a1a1a;
        margin-right: 1rem;
    }
    
    .record-badge-sales {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        background: #e8f4f8;
        color: #0277bd;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .month-stats-sales {
        display: flex;
        gap: 2rem;
        align-items: center;
    }
    
    .stat-box-sales {
        text-align: right;
        min-width: 150px;
    }
    
    .stat-value-sales {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        line-height: 1.2;
    }
    
    .stat-label-sales {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #666;
    }
    
    .stat-revenue-sales {
        color: #3498db;
    }
    
    .stat-revenue-sales .stat-label-sales {
        color: #2874a6;
        font-weight: 700;
    }
    
    .stat-quantity-sales {
        color: #27ae60;
    }
    
    .stat-quantity-sales .stat-label-sales {
        color: #229954;
        font-weight: 700;
    }
    
    .stat-count-sales {
        color: #f39c12;
    }
    
    .stat-count-sales .stat-label-sales {
        color: #d68910;
        font-weight: 700;
    }
    
    .progress-bar-mini-sales {
        height: 3px;
        background-color: #e9ecef;
        border-radius: 2px;
        margin-top: 0.75rem;
        overflow: hidden;
    }
    
    .progress-bar-mini-inner-sales {
        height: 100%;
        background: linear-gradient(90deg, #3498db, #2874a6);
        border-radius: 2px;
    }
    
    .sales-records-container {
        background: linear-gradient(135deg, #fafbfc 0%, #f5f7fa 100%);
        border-left: 4px solid #3498db;
    }
    
    .sales-records-container .table {
        margin-bottom: 0;
        background-color: transparent;
    }
    
    .sales-records-container .table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .sales-records-container .table thead th {
        border: none;
        padding: 1rem 0.75rem !important;
        font-weight: 700;
        color: #495057;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .sales-records-container .table tbody tr {
        border-bottom: 1px solid #e9ecef;
        background-color: transparent;
        transition: all 0.2s ease;
    }
    
    .sales-records-container .table tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.05);
        box-shadow: inset 1px 0 0 #e9ecef, inset -1px 0 0 #e9ecef;
    }
    
    .sales-records-container .table tbody td {
        padding: 1rem 0.75rem !important;
        vertical-align: middle;
    }
</style>

<div class="row mb-4">
    <div class="col-lg-8">
        <h2><i class="fas fa-cash-register me-2"></i>Sales Records</h2>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('sales.batches') }}" class="btn btn-outline-info me-2">
            <i class="fas fa-layer-group me-2"></i>View by Batches
        </a>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Sale
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
                <form method="GET" action="{{ route('sales.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="batch_id" class="form-label">Harvest Batch</label>
                        <select class="form-select" id="batch_id" name="batch_id">
                            <option value="">All Batches</option>
                            @foreach($harvestBatches as $batch)
                                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                    Batch #{{ $batch->id }} - {{ $batch->harvest_date->format('M d, Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
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

<!-- Payment Status Dashboard -->
<div class="row mb-4">
    <div class="col-lg-3 mb-3">
        <div class="card payment-status-card text-white" style="background: linear-gradient(135deg, #27ae60, #229954);">
            <div class="card-body py-4">
                <div class="payment-status-value">RM{{ number_format($paidRevenue, 2) }}</div>
                <div class="payment-status-label">Paid</div>
                <div class="payment-status-detail">{{ $paidCount }} transaction{{ $paidCount !== 1 ? 's' : '' }}</div>
                @if($totalSalesAmount > 0)
                    <div class="payment-status-detail">{{ number_format(($paidRevenue / $totalSalesAmount * 100), 1) }}% of Total</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-3 mb-3">
        <div class="card payment-status-card text-white" style="background: linear-gradient(135deg, #f39c12, #d68910);">
            <div class="card-body py-4">
                <div class="payment-status-value">RM{{ number_format($pendingRevenue, 2) }}</div>
                <div class="payment-status-label">Pending</div>
                <div class="payment-status-detail">{{ $pendingCount }} transaction{{ $pendingCount !== 1 ? 's' : '' }}</div>
                @if($totalSalesAmount > 0)
                    <div class="payment-status-detail">{{ number_format(($pendingRevenue / $totalSalesAmount * 100), 1) }}% of Total</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-3 mb-3">
        <div class="card payment-status-card text-white" style="background: linear-gradient(135deg, #3498db, #2874a6);">
            <div class="card-body py-4">
                <div class="payment-status-value">RM{{ number_format($partialRevenue, 2) }}</div>
                <div class="payment-status-label">Partial</div>
                <div class="payment-status-detail">{{ $partialCount }} transaction{{ $partialCount !== 1 ? 's' : '' }}</div>
                @if($totalSalesAmount > 0)
                    <div class="payment-status-detail">{{ number_format(($partialRevenue / $totalSalesAmount * 100), 1) }}% of Total</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-3 mb-3">
        <div class="card payment-status-card text-white" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
            <div class="card-body py-4">
                <div class="payment-status-value">RM{{ number_format($totalSalesAmount, 2) }}</div>
                <div class="payment-status-label">Total Sales</div>
                <div class="payment-status-detail">{{ $allSales->count() }} transaction{{ $allSales->count() !== 1 ? 's' : '' }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Trend Chart -->
@if($allSales->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2" style="color: #3498db;"></i>Revenue Trend
                </h6>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

@endif

<!-- View Toggle -->
<!-- View Toggle -->
<div class="row mb-3">
    <div class="col-12">
        <div class="btn-group" role="group">
            <a href="{{ route('sales.index', array_merge(request()->query(), ['view' => 'grouped'])) }}" 
               class="btn {{ $viewMode === 'grouped' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="fas fa-bars me-2"></i>Grouped by Month
            </a>
            <a href="{{ route('sales.index', array_merge(request()->query(), ['view' => 'list'])) }}" 
               class="btn {{ $viewMode === 'list' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="fas fa-list me-2"></i>List View
            </a>
        </div>
    </div>
</div>

<!-- Sales Records Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            @if($viewMode === 'grouped')
                <h5 class="mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>Sales Records by Month
                </h5>
            @else
                <h5 class="mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>All Sales Records
                </h5>
            @endif
            <small class="text-muted">{{ $allSales->count() }} total records</small>
        </div>
    </div>
    <div class="card-body p-0">
        @if($allSales->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="fas fa-shopping-cart fa-3x mb-3 opacity-50"></i>
                <div class="h5">No sales records found</div>
                <p>Start tracking your sales by <a href="{{ route('sales.create') }}" class="text-decoration-none">recording your first sale</a>.</p>
            </div>
        @elseif($viewMode === 'grouped')
            <!-- GROUPED VIEW -->
            @foreach($salesByMonth as $monthKey => $monthSales)
                @php
                    $firstSale = $monthSales->first();
                    $monthDate = $firstSale->sale_date;
                    $monthLabel = $monthDate->format('F Y');
                    $monthYear = $monthDate->format('Y-m');
                    $monthRevenue = $monthlyStats[$monthKey]['revenue'];
                    $monthQuantity = $monthlyStats[$monthKey]['quantity'];
                    $monthCount = $monthlyStats[$monthKey]['count'];
                    $collapseId = 'month_sales_' . str_replace('-', '_', $monthYear);
                @endphp
                
                <!-- Month Header with Toggle -->
                <div class="border-bottom month-header-sales">
                    <button class="btn btn-link w-100 text-start p-0 text-decoration-none collapsed" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#{{ $collapseId }}"
                            aria-expanded="false"
                            style="color: inherit;">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <!-- Left Side: Month Info -->
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-chevron-down me-1 transition-icon-sales" style="color: #666; font-size: 0.9rem;"></i>
                                <div>
                                    <div class="month-label-sales">
                                        {{ $monthLabel }}
                                    </div>
                                    <span class="record-badge-sales">
                                        <i class="fas fa-exchange-alt" style="font-size: 0.75rem;"></i>
                                        {{ $monthCount }} {{ $monthCount === 1 ? 'transaction' : 'transactions' }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Right Side: Statistics -->
                            <div class="month-stats-sales">
                                <div class="stat-box-sales">
                                    <div class="stat-value-sales stat-quantity-sales">
                                        {{ number_format($monthQuantity, 2) }} kg
                                    </div>
                                    <div class="stat-label-sales stat-quantity-sales">
                                        <i class="fas fa-weight"></i> Quantity
                                    </div>
                                </div>
                                <div class="stat-box-sales">
                                    <div class="stat-value-sales stat-count-sales">
                                        RM{{ number_format($monthRevenue / $monthCount, 2) }}
                                    </div>
                                    <div class="stat-label-sales stat-count-sales">
                                        <i class="fas fa-calculator"></i> Avg
                                    </div>
                                </div>
                                <div class="stat-box-sales">
                                    <div class="stat-value-sales stat-revenue-sales">
                                        RM{{ number_format($monthRevenue, 2) }}
                                    </div>
                                    <div class="stat-label-sales stat-revenue-sales">
                                        <i class="fas fa-money-bill"></i> Revenue
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="progress-bar-mini-sales">
                            <div class="progress-bar-mini-inner-sales" style="width: {{ $maxMonthlyRevenue > 0 ? ($monthRevenue / $maxMonthlyRevenue * 100) : 0 }}%"></div>
                        </div>
                    </button>
                    
                    <!-- Sales Records (Collapsible) -->
                    <div class="collapse sales-records-container" id="{{ $collapseId }}">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Sale Date</th>
                                        <th>Customer</th>
                                        <th>Harvest Batch</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Total Amount</th>
                                        <th class="text-center">Payment</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthSales as $sale)
                                    <tr class="align-middle">
                                        <td class="py-3">
                                            <div class="fw-bold">{{ $sale->sale_date->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $sale->sale_date->format('l') }}</small>
                                        </td>
                                        <td class="py-3">
                                            @if($sale->customer)
                                                <div class="fw-bold">{{ $sale->customer->name }}</div>
                                                <small class="text-muted">{{ ucfirst($sale->customer->customer_type) }}</small>
                                            @else
                                                <div class="text-muted">Walk-in Customer</div>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if($sale->harvestBatch)
                                                <a href="{{ route('sales.batch-detail', $sale->harvestBatch) }}" class="text-decoration-none">
                                                    <div class="fw-bold text-primary">Batch #{{ $sale->harvestBatch->id }}</div>
                                                    <small class="text-muted">{{ $sale->harvestBatch->harvest_date->format('M d') }} - {{ $sale->harvestBatch->variety ?: 'Mixed' }}</small>
                                                </a>
                                            @else
                                                <div class="text-muted">No batch</div>
                                            @endif
                                        </td>
                                        <td class="py-3 text-center">
                                            <div class="fw-bold text-success">{{ number_format($sale->quantity_kg, 2) }} kg</div>
                                            <small class="text-muted">RM{{ number_format($sale->price_per_kg, 2) }}/kg</small>
                                        </td>
                                        <td class="py-3 text-end">
                                            <div class="fw-bold fs-5 text-primary">RM{{ number_format($sale->total_amount, 2) }}</div>
                                        </td>
                                        <td class="py-3 text-center">
                                            @switch($sale->payment_status)
                                                @case('paid')
                                                    <span class="badge bg-success px-3 py-2">
                                                        <i class="fas fa-check me-1"></i>Paid
                                                    </span>
                                                    @break
                                                @case('pending')
                                                    <span class="badge bg-warning px-3 py-2">
                                                        <i class="fas fa-clock me-1"></i>Pending
                                                    </span>
                                                    @break
                                                @case('partial')
                                                    <span class="badge bg-info px-3 py-2">
                                                        <i class="fas fa-hourglass-half me-1"></i>Partial
                                                    </span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td class="py-3 text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-info btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="{{ route('sales.receipt', $sale) }}" class="dropdown-item" target="_blank">
                                                                <i class="fas fa-print me-2"></i>Print Receipt
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('sales.destroy', $sale) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger"
                                                                        onclick="return confirm('Are you sure you want to delete this sale record?')">
                                                                    <i class="fas fa-trash me-2"></i>Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- LIST VIEW -->
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3">Sale Date</th>
                            <th class="py-3">Customer</th>
                            <th class="py-3">Harvest Batch</th>
                            <th class="py-3 text-center">Quantity</th>
                            <th class="py-3 text-end">Total Amount</th>
                            <th class="py-3 text-center">Payment</th>
                            <th class="py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesPaginated as $sale)
                        <tr class="align-middle">
                            <td class="py-3">
                                <div class="fw-bold">{{ $sale->sale_date->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $sale->sale_date->format('l') }}</small>
                            </td>
                            <td class="py-3">
                                @if($sale->customer)
                                    <div class="fw-bold">{{ $sale->customer->name }}</div>
                                    <small class="text-muted">{{ ucfirst($sale->customer->customer_type) }}</small>
                                @else
                                    <div class="text-muted">Walk-in Customer</div>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($sale->harvestBatch)
                                    <a href="{{ route('sales.batch-detail', $sale->harvestBatch) }}" class="text-decoration-none">
                                        <div class="fw-bold text-primary">Batch #{{ $sale->harvestBatch->id }}</div>
                                        <small class="text-muted">{{ $sale->harvestBatch->harvest_date->format('M d') }} - {{ $sale->harvestBatch->variety ?: 'Mixed' }}</small>
                                    </a>
                                @else
                                    <div class="text-muted">No batch</div>
                                @endif
                            </td>
                            <td class="py-3 text-center">
                                <div class="fw-bold text-success">{{ number_format($sale->quantity_kg, 2) }} kg</div>
                                <small class="text-muted">RM{{ number_format($sale->price_per_kg, 2) }}/kg</small>
                            </td>
                            <td class="py-3 text-end">
                                <div class="fw-bold fs-5 text-primary">RM{{ number_format($sale->total_amount, 2) }}</div>
                            </td>
                            <td class="py-3 text-center">
                                @switch($sale->payment_status)
                                    @case('paid')
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check me-1"></i>Paid
                                        </span>
                                        @break
                                    @case('pending')
                                        <span class="badge bg-warning px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                        @break
                                    @case('partial')
                                        <span class="badge bg-info px-3 py-2">
                                            <i class="fas fa-hourglass-half me-1"></i>Partial
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="py-3 text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-info btn-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sales.edit', $sale) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ route('sales.receipt', $sale) }}" class="dropdown-item" target="_blank">
                                                    <i class="fas fa-print me-2"></i>Print Receipt
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('sales.destroy', $sale) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"
                                                            onclick="return confirm('Are you sure you want to delete this sale record?')">
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
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-shopping-cart fa-3x mb-3 opacity-50"></i>
                                <div class="h5">No sales records found</div>
                                <p>Start tracking your sales by <a href="{{ route('sales.create') }}" class="text-decoration-none">recording your first sale</a>.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($salesPaginated->hasPages())
            <div class="d-flex justify-content-center mt-4 px-3 pb-3">
                {{ $salesPaginated->links() }}
            </div>
            @endif
        @endif
    </div>
</div>

<!-- Chart.js Script for Revenue Trend -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    @if($allSales->count() > 0)
    // Revenue Trend Chart
    const revenueChartCanvas = document.getElementById('revenueChart');
    if (revenueChartCanvas) {
        const revenueChartCtx = revenueChartCanvas.getContext('2d');
        const revenueChart = new Chart(revenueChartCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Monthly Revenue (RM)',
                    data: {!! json_encode($revenueChartData) !!},
                    backgroundColor: [
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(52, 152, 219, 0.7)'
                    ],
                    borderColor: '#3498db',
                    borderWidth: 1,
                    borderRadius: 5,
                    hoverBackgroundColor: '#2874a6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '600'
                            },
                            color: '#666'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#999',
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            color: '#999',
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    @endif
</script>
@endsection
