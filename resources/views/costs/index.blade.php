@extends('layouts.app')

@section('title', 'Cost Management')
@section('page-title', 'Cost Management')

@section('content')
<style>
    .transition-icon {
        transition: transform 0.3s ease;
        display: inline-block;
    }
    
    .btn-link[aria-expanded="true"] .transition-icon {
        transform: rotate(180deg);
    }
    
    .month-header {
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }
    
    .month-header:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .month-header .btn-link {
        padding: 1.25rem !important;
        font-weight: 600;
    }
    
    .month-header .btn-link:focus {
        box-shadow: none;
    }
    
    .month-label {
        font-size: 1.15rem;
        color: #1a1a1a;
        margin-right: 1rem;
    }
    
    .month-stats {
        display: flex;
        gap: 2.5rem;
        align-items: center;
    }
    
    .stat-box {
        text-align: right;
        min-width: 150px;
    }
    
    .stat-value {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        line-height: 1.2;
    }
    
    .stat-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }
    
    .stat-total {
        color: #dc3545;
    }
    
    .stat-average {
        color: #00bcd4;
    }
    
    .stat-total .stat-label {
        color: #a52a2a;
    }
    
    .stat-average .stat-label {
        color: #0097a7;
    }
    
    .record-badge {
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
    
    .progress-bar-mini {
        height: 3px;
        background: linear-gradient(90deg, #dc3545, #ff6b6b);
        border-radius: 2px;
        margin-top: 0.75rem;
        opacity: 0;
        transition: opacity 0.3s ease;
        width: 100%;
    }
    
    .progress-bar-mini-inner {
        height: 100%;
        background: inherit;
        border-radius: 2px;
        transition: width 0.4s ease;
    }
    
    .month-header:hover .progress-bar-mini {
        opacity: 0.6;
    }
    
    .month-header.show .progress-bar-mini {
        opacity: 0.8;
    }
    
    /* Table Styling Enhancements */
    .cost-table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .cost-table thead th {
        border: none;
        padding: 1rem 0.75rem !important;
        font-weight: 700;
        color: #495057;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .cost-table tbody tr {
        border-bottom: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }
    
    .cost-table tbody tr:hover {
        background-color: #f8f9fa;
        box-shadow: inset 1px 0 0 #e9ecef, inset -1px 0 0 #e9ecef;
    }
    
    .cost-table tbody td {
        padding: 1rem 0.75rem !important;
        vertical-align: middle;
    }
    
    .cost-amount {
        font-weight: 700;
        font-size: 1.05rem;
    }
    
    .cost-category-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.65rem;
        background: #e8f1f5;
        color: #01579b;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.8rem;
    }
    
    .cost-date {
        font-weight: 600;
        color: #212529;
    }
    
    .cost-description {
        color: #495057;
        font-size: 0.95rem;
    }
    
    .cost-supplier {
        color: #6c757d;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }
    
    /* Month Group Styling */
    .month-group-container {
        background: #ffffff;
    }
    
    .month-records-container {
        background: linear-gradient(135deg, #fafbfc 0%, #f5f7fa 100%);
        border-left: 4px solid #dc3545;
    }
    
    .cost-table.month-records tbody tr {
        background-color: transparent;
    }
    
    .cost-table.month-records tbody tr:hover {
        background-color: rgba(220, 53, 69, 0.05);
    }
    
    /* Category List Styling */
    .category-list-container {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .category-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 8px;
        border-left: 4px solid #dee2e6;
        transition: all 0.3s ease;
    }
    
    .category-item:hover {
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .category-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        font-size: 1.2rem;
        color: white;
    }
    
    .category-details {
        flex: 1;
        min-width: 0;
    }
    
    .category-name {
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.35rem;
        font-size: 0.95rem;
    }
    
    .category-progress {
        height: 4px;
        background: #e9ecef;
        border-radius: 2px;
        overflow: hidden;
    }
    
    .category-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #28a745, #34ce57);
        border-radius: 2px;
        transition: width 0.4s ease;
    }
    
    .category-amount {
        text-align: right;
    }
    
    .category-amount-value {
        font-weight: 700;
        font-size: 1rem;
        color: #212529;
        display: block;
        margin-bottom: 0.25rem;
    }
    
    .category-amount-percent {
        font-size: 0.75rem;
        color: #6c757d;
        font-weight: 600;
    }
    
    /* Category Colors */
    .category-seeds { border-left-color: #28a745; }
    .category-seeds .category-icon { background: linear-gradient(135deg, #28a745, #20c997); }
    
    .category-fertilizer { border-left-color: #17a2b8; }
    .category-fertilizer .category-icon { background: linear-gradient(135deg, #17a2b8, #20c997); }
    
    .category-equipment { border-left-color: #ffc107; }
    .category-equipment .category-icon { background: linear-gradient(135deg, #ffc107, #ff9800); }
    
    .category-labor { border-left-color: #007bff; }
    .category-labor .category-icon { background: linear-gradient(135deg, #007bff, #0056b3); }
    
    .category-transport { border-left-color: #6f42c1; }
    .category-transport .category-icon { background: linear-gradient(135deg, #6f42c1, #734de9); }
    
    .category-maintenance { border-left-color: #e83e8c; }
    .category-maintenance .category-icon { background: linear-gradient(135deg, #e83e8c, #f55f9f); }
    
    .category-pesticide { border-left-color: #dc3545; }
    .category-pesticide .category-icon { background: linear-gradient(135deg, #dc3545, #ff6b6b); }
    
    .category-irrigation { border-left-color: #00bcd4; }
    .category-irrigation .category-icon { background: linear-gradient(135deg, #00bcd4, #26c6da); }
    
    .category-packaging { border-left-color: #9c27b0; }
    .category-packaging .category-icon { background: linear-gradient(135deg, #9c27b0, #b039d3); }
    
    .category-bills { border-left-color: #ff5722; }
    .category-bills .category-icon { background: linear-gradient(135deg, #ff5722, #ff7043); }
    
    .category-loan { border-left-color: #3f51b5; }
    .category-loan .category-icon { background: linear-gradient(135deg, #3f51b5, #5567d8); }
    
    .category-resell { border-left-color: #795548; }
    .category-resell .category-icon { background: linear-gradient(135deg, #795548, #a1887f); }
    
    .category-marketing { border-left-color: #673ab7; }
    .category-marketing .category-icon { background: linear-gradient(135deg, #673ab7, #7e57c2); }
    
    .category-short { border-left-color: #f44336; }
    .category-short .category-icon { background: linear-gradient(135deg, #f44336, #ef5350); }
    
    .category-other { border-left-color: #9e9e9e; }
    .category-other .category-icon { background: linear-gradient(135deg, #9e9e9e, #bdbdbd); }

</style>

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
                    <div class="col-md-3">
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
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
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
                <h3 class="mb-2">{{ $allCosts->count() }}</h3>
                <p class="mb-1 fw-bold">Total Records</p>
                <small class="opacity-75">Expense Entries</small>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Spending Chart -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2 text-primary"></i>Monthly Spending Trend
                </h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyCostChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- View Toggle -->
    <div class="col-12 mb-3">
        <div class="btn-group" role="group">
            <a href="{{ route('costs.index', array_merge(request()->query(), ['view' => 'grouped'])) }}" 
               class="btn {{ $viewMode === 'grouped' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="fas fa-bars me-2"></i>Grouped by Month
            </a>
            <a href="{{ route('costs.index', array_merge(request()->query(), ['view' => 'list'])) }}" 
               class="btn {{ $viewMode === 'list' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="fas fa-list me-2"></i>List View
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Cost Records Grouped by Month or List -->
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    @if($viewMode === 'grouped')
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2 text-primary"></i>Cost Records by Month
                        </h5>
                    @else
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2 text-primary"></i>All Cost Records
                        </h5>
                    @endif
                    <small class="text-muted">{{ $allCosts->count() }} total records</small>
                </div>
            </div>
            <div class="card-body p-0">
                @if($allCosts->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-receipt fa-3x mb-3 opacity-50"></i>
                        <div class="h5">No cost records found</div>
                        <p>Start tracking your expenses by <a href="{{ route('costs.create') }}" class="text-decoration-none">adding your first cost record</a>.</p>
                    </div>
                @elseif($viewMode === 'grouped')
                    <!-- GROUPED VIEW -->
                    @foreach($costsByMonth as $monthKey => $costs)
                        @php
                            $firstCostInMonth = $costs->first();
                            $monthDate = $firstCostInMonth->date;
                            $monthLabel = $monthDate->format('F Y');
                            $monthYear = $monthDate->format('Y-m');
                            $monthTotal = $monthlyTotals[$monthKey];
                            $monthAverage = $monthlyAverages[$monthKey];
                            $recordCount = $costs->count();
                            $daysInMonth = $monthDate->daysInMonth;
                            $collapseId = 'month_' . str_replace('-', '_', $monthYear);
                        @endphp
                        
                        <!-- Month Header with Toggle -->
                        <div class="border-bottom month-header month-group-container">
                            <button class="btn btn-link w-100 text-start p-0 text-decoration-none collapsed" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#{{ $collapseId }}"
                                    aria-expanded="false"
                                    style="color: inherit;">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <!-- Left Side: Month Info -->
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="fas fa-chevron-down me-1 transition-icon" style="color: #666; font-size: 0.9rem;"></i>
                                        <div>
                                            <div class="month-label">
                                                {{ $monthLabel }}
                                            </div>
                                            <span class="record-badge">
                                                <i class="fas fa-receipt" style="font-size: 0.75rem;"></i>
                                                {{ $recordCount }} {{ $recordCount === 1 ? 'record' : 'records' }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Side: Statistics -->
                                    <div class="month-stats">
                                        <div class="stat-box">
                                            <div class="stat-value stat-total">
                                                RM{{ number_format($monthTotal, 2) }}
                                            </div>
                                            <div class="stat-label stat-total">
                                                <i class="fas fa-dollar-sign"></i> Total
                                            </div>
                                        </div>
                                        <div class="stat-box">
                                            <div class="stat-value stat-average">
                                                RM{{ number_format($monthAverage, 2) }}
                                            </div>
                                            <div class="stat-label stat-average">
                                                <i class="fas fa-chart-line"></i> Daily Avg
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress-bar-mini">
                                    <div class="progress-bar-mini-inner" style="width: {{ $maxMonthlyTotal > 0 ? ($monthTotal / $maxMonthlyTotal * 100) : 0 }}%"></div>
                                </div>
                            </button>
                            
                            <!-- Month Records (Collapsible) -->
                            <div class="collapse month-records-container" id="{{ $collapseId }}">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 cost-table month-records mx-0">
                                        <thead>
                                            <tr>
                                                <th class="ps-3">Date & Category</th>
                                                <th>Description</th>
                                                <th class="text-end">Amount</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($costs as $cost)
                                            <tr class="align-middle">
                                                <td class="ps-3">
                                                    <div class="cost-date">{{ $cost->date->format('M d, Y') }}</div>
                                                    <span class="cost-category-badge">
                                                        @switch($cost->category)
                                                            @case('seeds')
                                                                <i class="fas fa-seedling"></i>
                                                            @break
                                                            @case('fertilizer')
                                                                <i class="fas fa-tint"></i>
                                                            @break
                                                            @case('equipment')
                                                                <i class="fas fa-tools"></i>
                                                            @break
                                                            @case('labor')
                                                                <i class="fas fa-users"></i>
                                                            @break
                                                            @case('maintenance')
                                                                <i class="fas fa-wrench"></i>
                                                            @break
                                                            @case('marketing')
                                                                <i class="fas fa-bullhorn"></i>
                                                            @break
                                                            @default
                                                                <i class="fas fa-receipt"></i>
                                                        @endswitch
                                                        {{ ucfirst($cost->category) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="cost-description fw-bold">{{ Str::limit($cost->description, 40) }}</div>
                                                    @if($cost->supplier)
                                                        <div class="cost-supplier">
                                                            <i class="fas fa-store"></i> {{ $cost->supplier }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="cost-amount text-danger">RM{{ number_format($cost->amount, 2) }}</div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('costs.show', $cost) }}" class="btn btn-outline-info" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('costs.edit', $cost) }}" class="btn btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
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
                        <table class="table table-hover mb-0 cost-table">
                            <thead>
                                <tr>
                                    <th>Date & Category</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allCosts as $cost)
                                <tr class="align-middle">
                                    <td>
                                        <div class="cost-date">{{ $cost->date->format('M d, Y') }}</div>
                                        <span class="cost-category-badge">
                                            @switch($cost->category)
                                                @case('seeds')
                                                    <i class="fas fa-seedling"></i>
                                                @break
                                                @case('fertilizer')
                                                    <i class="fas fa-tint"></i>
                                                @break
                                                @case('equipment')
                                                    <i class="fas fa-tools"></i>
                                                @break
                                                @case('labor')
                                                    <i class="fas fa-users"></i>
                                                @break
                                                @case('maintenance')
                                                    <i class="fas fa-wrench"></i>
                                                @break
                                                @case('marketing')
                                                    <i class="fas fa-bullhorn"></i>
                                                @break
                                                @default
                                                    <i class="fas fa-receipt"></i>
                                            @endswitch
                                            {{ ucfirst($cost->category) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="cost-description fw-bold">{{ Str::limit($cost->description, 40) }}</div>
                                        @if($cost->supplier)
                                            <div class="cost-supplier">
                                                <i class="fas fa-store"></i> {{ $cost->supplier }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="cost-amount text-danger">RM{{ number_format($cost->amount, 2) }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('costs.show', $cost) }}" class="btn btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('costs.edit', $cost) }}" class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Cost Categories -->
    <!-- Cost Categories Breakdown -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2 text-primary"></i>Costs by Category
                </h5>
            </div>
            <div class="card-body">
                <div class="category-list-container">
                    @forelse($costsByCategory as $category)
                        @php
                            $categoryClass = 'category-' . $category->category;
                            $categoryIcon = match($category->category) {
                                'seeds' => 'fas fa-seedling',
                                'fertilizer' => 'fas fa-tint',
                                'equipment' => 'fas fa-tools',
                                'labor' => 'fas fa-users',
                                'transport' => 'fas fa-truck',
                                'maintenance' => 'fas fa-wrench',
                                'pesticide' => 'fas fa-skull',
                                'irrigation' => 'fas fa-droplet',
                                'packaging' => 'fas fa-box',
                                'bills' => 'fas fa-file-invoice-dollar',
                                'loan' => 'fas fa-handshake',
                                'resell' => 'fas fa-tag',
                                'marketing' => 'fas fa-bullhorn',
                                'short' => 'fas fa-minus-circle',
                                default => 'fas fa-receipt',
                            };
                        @endphp
                        <div class="category-item {{ $categoryClass }}">
                            <div class="category-icon">
                                <i class="{{ $categoryIcon }}"></i>
                            </div>
                            <div class="category-details">
                                <div class="category-name">{{ ucfirst($category->category) }}</div>
                                <div class="category-progress">
                                    <div class="category-progress-bar" style="width: {{ $category->progressPercent }}%"></div>
                                </div>
                            </div>
                            <div class="category-amount">
                                <span class="category-amount-value">RM{{ number_format($category->total, 2) }}</span>
                                <span class="category-amount-percent">{{ number_format($category->percentage, 1) }}%</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">
                            <i class="fas fa-inbox"></i><br>
                            No cost data available
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyCostChart');
    if (ctx) {
        const chartLabels = {!! json_encode($chartLabels) !!};
        const chartData = {!! json_encode($chartData) !!};
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Monthly Total Costs (RM)',
                    data: chartData,
                    backgroundColor: [
                        'rgba(220, 53, 69, 0.6)',
                        'rgba(23, 162, 184, 0.6)',
                        'rgba(0, 123, 255, 0.6)',
                        'rgba(40, 167, 69, 0.6)',
                        'rgba(255, 193, 7, 0.6)',
                        'rgba(108, 117, 125, 0.6)',
                    ],
                    borderColor: [
                        'rgba(220, 53, 69, 1)',
                        'rgba(23, 162, 184, 1)',
                        'rgba(0, 123, 255, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(108, 117, 125, 1)',
                    ],
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'RM' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
