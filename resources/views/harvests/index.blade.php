@extends('layouts.app')

@section('title', 'Harvest Records')
@section('page-title', 'Harvest Records')

@section('content')
<style>
    /* Harvest Module Styling */
    .harvest-table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .harvest-table thead th {
        border: none;
        padding: 1rem 0.75rem !important;
        font-weight: 700;
        color: #495057;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .harvest-table tbody tr {
        border-bottom: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }
    
    .harvest-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .harvest-table tbody td {
        padding: 1rem 0.75rem !important;
        vertical-align: middle;
    }
    
    /* Status Badge Styling */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-completed {
        background: #d4edda;
        color: #155724;
    }
    
    .status-allocated {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .status-nearly-complete {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-partial {
        background: #cce5ff;
        color: #004085;
    }
    
    .status-available {
        background: #e2e3e5;
        color: #383d41;
    }
    
    /* Progress Bar Styling */
    .fulfillment-container {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .fulfillment-bar {
        flex: 1;
        min-width: 100px;
    }
    
    .progress-bar-custom {
        height: 6px;
        background: #e9ecef;
        border-radius: 3px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #28a745, #20c997);
        border-radius: 3px;
        transition: width 0.4s ease;
    }
    
    .fulfillment-percent {
        font-weight: 700;
        color: #28a745;
        min-width: 45px;
        text-align: right;
        font-size: 0.9rem;
    }
    
    /* Quantity Display */
    .quantity-display {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .quantity-total {
        font-weight: 700;
        color: #28a745;
        font-size: 1.05rem;
    }
    
    .quantity-available {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    /* Revenue Display */
    .revenue-display {
        text-align: right;
    }
    
    .revenue-amount {
        font-weight: 700;
        color: #495057;
        font-size: 1rem;
        display: block;
    }
    
    .revenue-label {
        font-size: 0.75rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    /* Batch Date Info */
    .harvest-date-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .harvest-date-main {
        font-weight: 700;
        color: #212529;
    }
    
    .harvest-date-secondary {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    /* Action Buttons */
    .harvest-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .btn-action-small {
        padding: 0.4rem 0.7rem;
        font-size: 0.8rem;
    }
    
    /* Month Grouping Styling */
    .month-header-harvest {
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }
    
    .month-header-harvest:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .month-header-harvest .btn-link {
        padding: 1.25rem !important;
        font-weight: 600;
    }
    
    .month-header-harvest .btn-link:focus {
        box-shadow: none;
    }
    
    .month-label-harvest {
        font-size: 1.15rem;
        color: #1a1a1a;
        margin-right: 1rem;
    }
    
    .month-stats-harvest {
        display: flex;
        gap: 2rem;
        align-items: center;
    }
    
    .stat-box-harvest {
        text-align: right;
        min-width: 150px;
    }
    
    .stat-value-harvest {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        line-height: 1.2;
    }
    
    .stat-label-harvest {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }
    
    .stat-yield {
        color: #27ae60;
    }
    
    .stat-daily {
        color: #f39c12;
    }
    
    .stat-revenue {
        color: #3498db;
    }
    
    .stat-yield .stat-label-harvest {
        color: #229954;
        font-weight: 700;
    }
    
    .stat-daily .stat-label-harvest {
        color: #d68910;
        font-weight: 700;
    }
    
    .stat-revenue .stat-label-harvest {
        color: #2874a6;
        font-weight: 700;
    }
    
    .record-badge-harvest {
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
    
    .progress-bar-mini-harvest {
        height: 3px;
        background: linear-gradient(90deg, #28a745, #20c997);
        border-radius: 2px;
        margin-top: 0.75rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .progress-bar-mini-inner-harvest {
        height: 100%;
        background: inherit;
        border-radius: 2px;
        transition: width 0.4s ease;
    }
    
    .month-header-harvest:hover .progress-bar-mini-harvest {
        opacity: 0.6;
    }
    
    .month-header-harvest.show .progress-bar-mini-harvest {
        opacity: 0.8;
    }
    
    .harvest-records-container {
        background: linear-gradient(135deg, #fafbfc 0%, #f5f7fa 100%);
        border-left: 4px solid #28a745;
    }
    
    .transition-icon-harvest {
        transition: transform 0.3s ease;
        display: inline-block;
    }
    
    .btn-link[aria-expanded="true"] .transition-icon-harvest {
        transform: rotate(180deg);
    }
</style>
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
                <h3 class="mb-2">{{ $allHarvests->count() }}</h3>
                <p class="mb-1 fw-bold">Total Records</p>
                <small class="opacity-75">Harvest Sessions</small>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Overview -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-warehouse me-2 text-primary"></i>Inventory Status
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @php
                        $totalHarvested = $allHarvests->sum('quantity_kg');
                        $totalAllocated = $allHarvests->sum('total_quantity_allocated');
                        $totalAvailable = $allHarvests->sum('available_quantity');
                        $totalRevenue = $allHarvests->sum('total_revenue');
                    @endphp
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="fas fa-boxes fa-2x text-success mb-2"></i>
                            <div class="fw-bold fs-5 text-success">{{ number_format($totalHarvested, 2) }} kg</div>
                            <small class="text-muted">Total Harvested</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="fas fa-dolly fa-2x text-info mb-2"></i>
                            <div class="fw-bold fs-5 text-info">{{ number_format($totalAllocated, 2) }} kg</div>
                            <small class="text-muted">Allocated to Sales</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="fas fa-box-open fa-2x text-warning mb-2"></i>
                            <div class="fw-bold fs-5 text-warning">{{ number_format($totalAvailable, 2) }} kg</div>
                            <small class="text-muted">Available Stock</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                            <div class="fw-bold fs-5 text-success">RM{{ number_format($totalRevenue, 2) }}</div>
                            <small class="text-muted">Total Revenue</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Statistics Section -->
@if($allHarvests->count() > 0)
<div class="row mb-4">
    <!-- Yield Trend Chart -->
    <div class="col-lg-6 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0">
                    <i class="fas fa-chart-line me-2" style="color: #27ae60;"></i>Yield Trend
                </h6>
            </div>
            <div class="card-body">
                <canvas id="harvestYieldChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Revenue Trend Chart -->
    <div class="col-lg-6 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2" style="color: #3498db;"></i>Revenue Trend
                </h6>
            </div>
            <div class="card-body">
                <canvas id="harvestRevenueChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Statistics Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm h-100" style="border-left: 4px solid #27ae60;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-award fa-2x" style="color: #27ae60; opacity: 0.7;"></i>
                    <div class="ms-3">
                        <small class="text-muted d-block">Highest Yield Month</small>
                        <h6 class="mb-0" style="color: #27ae60; font-weight: 700;">
                            @if($highestYieldMonth)
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $highestYieldMonth)->format('F Y') }}
                            @else
                                No data
                            @endif
                        </h6>
                    </div>
                </div>
                <div class="text-center pt-2 border-top">
                    <h5 style="color: #27ae60; font-weight: 700; margin-bottom: 0;">{{ number_format($highestYieldValue, 2) }} kg</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card shadow-sm h-100" style="border-left: 4px solid #f39c12;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-balance-scale fa-2x" style="color: #f39c12; opacity: 0.7;"></i>
                    <div class="ms-3">
                        <small class="text-muted d-block">Average Monthly Yield</small>
                        <h6 class="mb-0" style="color: #f39c12; font-weight: 700;">
                            {{ number_format($harvestsByMonth->count() > 0 ? collect($monthlyStats)->avg('yield') : 0, 2) }} kg
                        </h6>
                    </div>
                </div>
                <div class="text-center pt-2 border-top">
                    <small class="text-muted">Per Month Average</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card shadow-sm h-100" style="border-left: 4px solid #3498db;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-coins fa-2x" style="color: #3498db; opacity: 0.7;"></i>
                    <div class="ms-3">
                        <small class="text-muted d-block">Average Revenue</small>
                        <h6 class="mb-0" style="color: #3498db; font-weight: 700;">
                            RM{{ number_format($harvestsByMonth->count() > 0 ? collect($monthlyStats)->avg('revenue') : 0, 2) }}
                        </h6>
                    </div>
                </div>
                <div class="text-center pt-2 border-top">
                    <small class="text-muted">Per Month Average</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- View Toggle -->
<div class="row mb-3">
    <div class="col-12">
        <div class="btn-group" role="group">
            <a href="{{ route('harvests.index', array_merge(request()->query(), ['view' => 'grouped'])) }}" 
               class="btn {{ $viewMode === 'grouped' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="fas fa-bars me-2"></i>Grouped by Month
            </a>
            <a href="{{ route('harvests.index', array_merge(request()->query(), ['view' => 'list'])) }}" 
               class="btn {{ $viewMode === 'list' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="fas fa-list me-2"></i>List View
            </a>
        </div>
    </div>
</div>

<!-- Harvest Records Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            @if($viewMode === 'grouped')
                <h5 class="mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>Harvest Records by Month
                </h5>
            @else
                <h5 class="mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>All Harvest Records
                </h5>
            @endif
            <small class="text-muted">{{ $allHarvests->count() }} total records</small>
        </div>
    </div>
    <div class="card-body p-0">
        @if($allHarvests->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="fas fa-seedling fa-3x mb-3 opacity-50"></i>
                <div class="h5">No harvest records found</div>
                <p>Start tracking your harvests by <a href="{{ route('harvests.create') }}" class="text-decoration-none">adding your first harvest record</a>.</p>
            </div>
        @elseif($viewMode === 'grouped')
            <!-- GROUPED VIEW -->
            @foreach($harvestsByMonth as $monthKey => $harvests)
                @php
                    $firstHarvest = $harvests->first();
                    $monthDate = $firstHarvest->harvest_date;
                    $monthLabel = $monthDate->format('F Y');
                    $monthYear = $monthDate->format('Y-m');
                    $monthYield = $monthlyStats[$monthKey]['yield'];
                    $monthRevenue = $monthlyStats[$monthKey]['revenue'];
                    $avgPerDay = $monthlyStats[$monthKey]['avg_per_day'];
                    $recordCount = $monthlyStats[$monthKey]['count'];
                    $collapseId = 'month_harvest_' . str_replace('-', '_', $monthYear);
                @endphp
                
                <!-- Month Header with Toggle -->
                <div class="border-bottom month-header-harvest">
                    <button class="btn btn-link w-100 text-start p-0 text-decoration-none collapsed" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#{{ $collapseId }}"
                            aria-expanded="false"
                            style="color: inherit;">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <!-- Left Side: Month Info -->
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-chevron-down me-1 transition-icon-harvest" style="color: #666; font-size: 0.9rem;"></i>
                                <div>
                                    <div class="month-label-harvest">
                                        {{ $monthLabel }}
                                    </div>
                                    <span class="record-badge-harvest">
                                        <i class="fas fa-leaf" style="font-size: 0.75rem;"></i>
                                        {{ $recordCount }} {{ $recordCount === 1 ? 'harvest' : 'harvests' }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Right Side: Statistics -->
                            <div class="month-stats-harvest">
                                <div class="stat-box-harvest">
                                    <div class="stat-value-harvest stat-yield">
                                        {{ number_format($monthYield, 2) }} kg
                                    </div>
                                    <div class="stat-label-harvest stat-yield">
                                        <i class="fas fa-weight"></i> Total Yield
                                    </div>
                                </div>
                                <div class="stat-box-harvest">
                                    <div class="stat-value-harvest stat-daily">
                                        {{ number_format($avgPerDay, 2) }} kg/day
                                    </div>
                                    <div class="stat-label-harvest stat-daily">
                                        <i class="fas fa-chart-line"></i> Avg Daily
                                    </div>
                                </div>
                                <div class="stat-box-harvest">
                                    <div class="stat-value-harvest stat-revenue">
                                        RM{{ number_format($monthRevenue, 2) }}
                                    </div>
                                    <div class="stat-label-harvest stat-revenue">
                                        <i class="fas fa-money-bill"></i> Revenue
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="progress-bar-mini-harvest">
                            <div class="progress-bar-mini-inner-harvest" style="width: {{ $maxMonthlyYield > 0 ? ($monthYield / $maxMonthlyYield * 100) : 0 }}%"></div>
                        </div>
                    </button>
                    
                    <!-- Month Records (Collapsible) -->
                    <div class="collapse harvest-records-container" id="{{ $collapseId }}">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 harvest-table mx-0">
                                <thead>
                                    <tr>
                                        <th>Date & Variety</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Available</th>
                                        <th class="text-center">Fulfillment</th>
                                        <th class="text-center">Revenue</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($harvests as $harvest)
                                    <tr class="align-middle">
                                        <!-- Date & Variety Info -->
                                        <td>
                                            <div class="harvest-date-info">
                                                <span class="harvest-date-main">{{ $harvest->harvest_date->format('M d, Y') }}</span>
                                                <span class="harvest-date-secondary">{{ $harvest->variety ?: 'Mixed Variety' }}</span>
                                                @if($harvest->field_location)
                                                    <span class="harvest-date-secondary">
                                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $harvest->field_location }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <!-- Total Quantity -->
                                        <td class="text-center">
                                            <div class="quantity-display">
                                                <span class="quantity-total">{{ number_format($harvest->quantity_kg, 2) }} kg</span>
                                            </div>
                                        </td>
                                        
                                        <!-- Available Quantity -->
                                        <td class="text-center">
                                            <div class="quantity-display">
                                                <span class="quantity-total">{{ number_format($harvest->available_quantity, 2) }} kg</span>
                                                <span class="quantity-available">{{ $harvest->total_quantity_allocated > 0 ? number_format($harvest->total_quantity_allocated, 2) . ' allocated' : 'no sales' }}</span>
                                            </div>
                                        </td>
                                        
                                        <!-- Fulfillment % with Progress Bar -->
                                        <td class="text-center">
                                            <div class="fulfillment-container">
                                                <div class="fulfillment-bar">
                                                    <div class="progress-bar-custom">
                                                        <div class="progress-fill" style="width: {{ $harvest->fulfillment_percentage }}%"></div>
                                                    </div>
                                                </div>
                                                <span class="fulfillment-percent">{{ number_format($harvest->fulfillment_percentage, 0) }}%</span>
                                            </div>
                                        </td>
                                        
                                        <!-- Revenue -->
                                        <td class="text-center">
                                            <div class="revenue-display">
                                                <span class="revenue-amount">RM{{ number_format($harvest->total_revenue, 2) }}</span>
                                                <span class="revenue-label">Revenue</span>
                                            </div>
                                        </td>
                                        
                                        <!-- Status Badge -->
                                        <td class="text-center">
                                            <span class="status-badge status-{{ strtolower(str_replace('_', '-', $harvest->batch_status)) }}">
                                                @switch($harvest->batch_status)
                                                    @case('completed')
                                                        <i class="fas fa-check-circle"></i> Completed
                                                    @break
                                                    @case('allocated')
                                                        <i class="fas fa-hourglass-half"></i> Allocated
                                                    @break
                                                    @case('nearly_complete')
                                                        <i class="fas fa-star-half-alt"></i> 75%+
                                                    @break
                                                    @case('partial')
                                                        <i class="fas fa-circle-notch"></i> Partial
                                                    @break
                                                    @case('available')
                                                        <i class="fas fa-inbox"></i> Available
                                                    @break
                                                @endswitch
                                            </span>
                                        </td>
                                        
                                        <!-- Actions -->
                                        <td class="text-center">
                                            <div class="harvest-actions">
                                                @if($harvest->available_quantity > 0)
                                                    <a href="{{ route('sales.create') }}?batch_id={{ $harvest->id }}" 
                                                       class="btn btn-sm btn-success btn-action-small" 
                                                       title="Add sale for this batch">
                                                        <i class="fas fa-plus me-1"></i>Sale
                                                    </a>
                                                @endif
                                                <a href="{{ route('harvests.show', $harvest) }}" 
                                                   class="btn btn-sm btn-outline-info btn-action-small" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('harvests.edit', $harvest) }}" 
                                                   class="btn btn-sm btn-outline-primary btn-action-small" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" 
                                                            data-bs-toggle="dropdown" title="More actions">
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
                <table class="table table-hover mb-0 harvest-table">
                    <thead>
                        <tr>
                            <th>Date & Variety</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Available</th>
                            <th class="text-center">Fulfillment</th>
                            <th class="text-center">Revenue</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allHarvests as $harvest)
                        <tr class="align-middle">
                            <!-- Date & Variety Info -->
                            <td>
                                <div class="harvest-date-info">
                                    <span class="harvest-date-main">{{ $harvest->harvest_date->format('M d, Y') }}</span>
                                    <span class="harvest-date-secondary">{{ $harvest->variety ?: 'Mixed Variety' }}</span>
                                    @if($harvest->field_location)
                                        <span class="harvest-date-secondary">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $harvest->field_location }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Total Quantity -->
                            <td class="text-center">
                                <div class="quantity-display">
                                    <span class="quantity-total">{{ number_format($harvest->quantity_kg, 2) }} kg</span>
                                </div>
                            </td>
                            
                            <!-- Available Quantity -->
                            <td class="text-center">
                                <div class="quantity-display">
                                    <span class="quantity-total">{{ number_format($harvest->available_quantity, 2) }} kg</span>
                                    <span class="quantity-available">{{ $harvest->total_quantity_allocated > 0 ? number_format($harvest->total_quantity_allocated, 2) . ' allocated' : 'no sales' }}</span>
                                </div>
                            </td>
                            
                            <!-- Fulfillment % with Progress Bar -->
                            <td class="text-center">
                                <div class="fulfillment-container">
                                    <div class="fulfillment-bar">
                                        <div class="progress-bar-custom">
                                            <div class="progress-fill" style="width: {{ $harvest->fulfillment_percentage }}%"></div>
                                        </div>
                                    </div>
                                    <span class="fulfillment-percent">{{ number_format($harvest->fulfillment_percentage, 0) }}%</span>
                                </div>
                            </td>
                            
                            <!-- Revenue -->
                            <td class="text-center">
                                <div class="revenue-display">
                                    <span class="revenue-amount">RM{{ number_format($harvest->total_revenue, 2) }}</span>
                                    <span class="revenue-label">Revenue</span>
                                </div>
                            </td>
                            
                            <!-- Status Badge -->
                            <td class="text-center">
                                <span class="status-badge status-{{ strtolower(str_replace('_', '-', $harvest->batch_status)) }}">
                                    @switch($harvest->batch_status)
                                        @case('completed')
                                            <i class="fas fa-check-circle"></i> Completed
                                        @break
                                        @case('allocated')
                                            <i class="fas fa-hourglass-half"></i> Allocated
                                        @break
                                        @case('nearly_complete')
                                            <i class="fas fa-star-half-alt"></i> 75%+
                                        @break
                                        @case('partial')
                                            <i class="fas fa-circle-notch"></i> Partial
                                        @break
                                        @case('available')
                                            <i class="fas fa-inbox"></i> Available
                                        @break
                                    @endswitch
                                </span>
                            </td>
                            
                            <!-- Actions -->
                            <td class="text-center">
                                <div class="harvest-actions">
                                    @if($harvest->available_quantity > 0)
                                        <a href="{{ route('sales.create') }}?batch_id={{ $harvest->id }}" 
                                           class="btn btn-sm btn-success btn-action-small" 
                                           title="Add sale for this batch">
                                            <i class="fas fa-plus me-1"></i>Sale
                                        </a>
                                    @endif
                                    <a href="{{ route('harvests.show', $harvest) }}" 
                                       class="btn btn-sm btn-outline-info btn-action-small" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('harvests.edit', $harvest) }}" 
                                       class="btn btn-sm btn-outline-primary btn-action-small" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" 
                                                data-bs-toggle="dropdown" title="More actions">
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Chart.js Script for Monthly Statistics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    @if($allHarvests->count() > 0)
    // Harvest Yield Trend Chart
    const yieldChartCanvas = document.getElementById('harvestYieldChart');
    if (yieldChartCanvas) {
        const yieldChartCtx = yieldChartCanvas.getContext('2d');
        const yieldChart = new Chart(yieldChartCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Monthly Yield (kg)',
                    data: {!! json_encode($yieldChartData) !!},
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.08)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#27ae60',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#229954'
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
                    },
                    filler: {
                        propagate: true
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

    // Harvest Revenue Trend Chart
    const revenueChartCanvas = document.getElementById('harvestRevenueChart');
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
