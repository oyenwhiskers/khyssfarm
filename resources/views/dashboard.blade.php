@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1 fw-bold">Farm Analytics Dashboard</h1>
                    <p class="text-muted mb-0">Comprehensive overview of your agricultural business</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-primary fs-6 px-3 py-2">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ now()->format('F d, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-5">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-success">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h2 class="fw-bold mb-1">RM{{ number_format($totalRevenue, 2) }}</h2>
                            <p class="mb-0 opacity-75">Total Revenue</p>
                        </div>
                        <div class="fs-1 opacity-75">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-danger">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h2 class="fw-bold mb-1">RM{{ number_format($totalAllCosts, 2) }}</h2>
                            <p class="mb-0 opacity-75">Total Costs</p>
                        </div>
                        <div class="fs-1 opacity-75">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-primary">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h2 class="fw-bold mb-1">RM{{ number_format($grandTotalProfit, 2) }}</h2>
                            <p class="mb-0 opacity-75">Net Profit</p>
                        </div>
                        <div class="fs-1 opacity-75">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #e91e63, #f06292);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h2 class="fw-bold mb-1">{{ number_format($totalRevenue > 0 ? ($grandTotalProfit / $totalRevenue) * 100 : 0, 1) }}%</h2>
                            <p class="mb-0 opacity-75">Profit Margin</p>
                        </div>
                        <div class="fs-1 opacity-75">
                            <i class="bi bi-percent"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Streams -->
    <div class="row">
        <!-- Farm Production -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white border-0 py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="bi bi-house-fill me-2"></i>
                                Farm Production
                            </h5>
                            <p class="mb-0 opacity-75 small">Your own agricultural produce</p>
                        </div>
                        <a href="{{ route('harvests.index') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Farm Yield -->
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="text-primary mb-2">
                                    <i class="bi bi-award-fill fs-2"></i>
                                </div>
                                <h3 class="fw-bold text-dark mb-1">{{ number_format($totalYield, 2) }}</h3>
                                <p class="text-muted mb-0 small">kg Harvested</p>
                            </div>
                        </div>
                        
                        <!-- Farm Revenue -->
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="text-success mb-2">
                                    <i class="bi bi-currency-dollar fs-2"></i>
                                </div>
                                <h3 class="fw-bold text-success mb-1">{{ number_format($farmRevenue, 2) }}</h3>
                                <p class="text-muted mb-0 small">RM Revenue</p>
                                @if($pendingRevenue > 0)
                                    <div class="mt-1">
                                        <span class="badge bg-warning text-dark small">
                                            +{{ number_format($pendingRevenue, 2) }} pending
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Farm Costs -->
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="text-danger mb-2">
                                    <i class="bi bi-receipt fs-2"></i>
                                </div>
                                <h3 class="fw-bold text-danger mb-1">{{ number_format($totalCosts, 2) }}</h3>
                                <p class="text-muted mb-0 small">RM Costs</p>
                            </div>
                        </div>
                        
                        <!-- Farm Profit -->
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="text-info mb-2">
                                    <i class="bi bi-graph-up fs-2"></i>
                                </div>
                                <h3 class="fw-bold text-info mb-1">{{ number_format($farmProfit, 2) }}</h3>
                                <p class="text-muted mb-0 small">RM Profit</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="border-top pt-3 mt-4">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('harvests.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> New Harvest
                            </a>
                            <a href="{{ route('sales.create') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-cash me-1"></i> Record Sale
                            </a>
                            <a href="{{ route('costs.create') }}" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-receipt me-1"></i> Add Cost
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resell Business -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white border-0 py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="bi bi-shop me-2"></i>
                                Resell Business
                            </h5>
                            <p class="mb-0 opacity-75 small">Trading purchased chilies</p>
                        </div>
                        <a href="{{ route('resells.index') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Resell Inventory -->
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="text-primary mb-2">
                                    <i class="bi bi-box-seam-fill fs-2"></i>
                                </div>
                                <h3 class="fw-bold text-dark mb-1">{{ number_format($resellYield, 2) }}</h3>
                                <p class="text-muted mb-0 small">kg Purchased</p>
                            </div>
                        </div>
                        
                        <!-- Resell Revenue -->
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="text-success mb-2">
                                    <i class="bi bi-cash-stack fs-2"></i>
                                </div>
                                <h3 class="fw-bold text-success mb-1">{{ number_format($resellRevenue, 2) }}</h3>
                                <p class="text-muted mb-0 small">RM Revenue</p>
                            </div>
                        </div>
                        
                        <!-- Purchase Costs -->
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="text-danger mb-2">
                                    <i class="bi bi-cart-check-fill fs-2"></i>
                                </div>
                                <h3 class="fw-bold text-danger mb-1">{{ number_format($resellPurchaseCosts, 2) }}</h3>
                                <p class="text-muted mb-0 small">RM Purchase Cost</p>
                            </div>
                        </div>
                        
                        <!-- Resell Profit -->
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="text-info mb-2">
                                    <i class="bi bi-arrow-up-circle-fill fs-2"></i>
                                </div>
                                <h3 class="fw-bold text-info mb-1">{{ number_format($resellProfit, 2) }}</h3>
                                <p class="text-muted mb-0 small">RM Profit</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="border-top pt-3 mt-4">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('resells.create') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> New Purchase
                            </a>
                            <a href="{{ route('resells.index') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-eye me-1"></i> View Inventory
                            </a>
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-people me-1"></i> Customers
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Outstanding Payments Alert -->
    @if($pendingRevenue > 0)
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-exclamation-triangle-fill fs-3 text-warning"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">Outstanding Payments</h6>
                        <p class="mb-0">You have <strong>RM{{ number_format($pendingRevenue, 2) }}</strong> in pending payments that need attention.</p>
                    </div>
                    <div>
                        <a href="{{ route('sales.index', ['payment_status' => 'pending']) }}" class="btn btn-warning">
                            <i class="bi bi-eye me-1"></i> View Pending Sales
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Monthly Trends Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col">
                            <h5 class="card-title mb-0 fw-bold text-dark">
                                <i class="bi bi-graph-up-arrow text-primary me-2"></i>Monthly Trends
                            </h5>
                            <p class="text-muted mb-0 small">6-month performance overview</p>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-calendar3 me-1"></i>Last 6 Months
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Last 3 Months</a></li>
                                    <li><a class="dropdown-item active" href="#">Last 6 Months</a></li>
                                    <li><a class="dropdown-item" href="#">Last 12 Months</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div style="height: 400px; position: relative;">
                                <canvas id="monthlyTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Trend Indicators -->
                    <div class="row mt-4">
                        <div class="col-md-3 col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div class="rounded-circle p-2 me-2" style="width: 12px; height: 12px; background-color: rgb(251, 191, 36) !important;"></div>
                                    <span class="fw-bold small" style="color: rgb(251, 191, 36);">Yield</span>
                                </div>
                                <span class="text-muted small">Last 6 months average</span>
                                <h6 class="mb-0 fw-bold">{{ number_format($monthlyYield->avg('value'), 0) }} kg</h6>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div class="rounded-circle p-2 me-2" style="width: 12px; height: 12px; background-color: rgb(34, 197, 94) !important;"></div>
                                    <span class="fw-bold small" style="color: rgb(34, 197, 94);">Revenue</span>
                                </div>
                                <span class="text-muted small">Last 6 months average</span>
                                <h6 class="mb-0 fw-bold">RM{{ number_format($monthlyRevenue->avg('value'), 0) }}</h6>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div class="rounded-circle p-2 me-2" style="width: 12px; height: 12px; background-color: rgb(239, 68, 68) !important;"></div>
                                    <span class="fw-bold small" style="color: rgb(239, 68, 68);">Costs</span>
                                </div>
                                <span class="text-muted small">Last 6 months average</span>
                                <h6 class="mb-0 fw-bold">RM{{ number_format($monthlyCosts->avg('value'), 0) }}</h6>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div class="rounded-circle p-2 me-2" style="width: 12px; height: 12px; background-color: rgb(59, 130, 246) !important;"></div>
                                    <span class="fw-bold small" style="color: rgb(59, 130, 246);">Net Profit</span>
                                </div>
                                <span class="text-muted small">Last 6 months average</span>
                                @php
                                    $avgProfit = $monthlyRevenue->avg('value') - $monthlyCosts->avg('value');
                                @endphp
                                <h6 class="mb-0 fw-bold">
                                    RM{{ number_format($avgProfit, 0) }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Analytics Tabs Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs border-0 mb-4" id="analyticsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold text-primary border-0 border-bottom border-primary" 
                                    id="yield-tab" data-bs-toggle="tab" data-bs-target="#yield" type="button" 
                                    role="tab" aria-controls="yield" aria-selected="true">
                                <i class="bi bi-bar-chart-line me-2"></i>Yield Analytics
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-muted border-0" 
                                    id="cost-tab" data-bs-toggle="tab" data-bs-target="#cost" type="button" 
                                    role="tab" aria-controls="cost" aria-selected="false">
                                <i class="bi bi-wallet2 me-2"></i>Cost Analytics
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-muted border-0" 
                                    id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" type="button" 
                                    role="tab" aria-controls="customer" aria-selected="false">
                                <i class="bi bi-people me-2"></i>Customer Analytics
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-muted border-0" 
                                    id="trends-tab" data-bs-toggle="tab" data-bs-target="#trends" type="button" 
                                    role="tab" aria-controls="trends" aria-selected="false">
                                <i class="bi bi-graph-up me-2"></i>Market Trends
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content" id="analyticsTabContent">
                        <!-- Yield Analytics Tab -->
                        <div class="tab-pane fade show active" id="yield" role="tabpanel" aria-labelledby="yield-tab">
                            <div class="row">
                                <!-- Monthly Yield Trends -->
                                <div class="col-lg-8 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Monthly Yield Trends</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="monthlyYieldChart" height="100"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Variety Distribution -->
                                <div class="col-lg-4 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Variety Distribution</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="varietyChart" height="150"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Harvests -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Harvests</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Variety</th>
                                                            <th>Quantity</th>
                                                            <th>Quality</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($recentHarvests as $harvest)
                                                        <tr>
                                                            <td>{{ $harvest->harvest_date->format('M d') }}</td>
                                                            <td>{{ $harvest->variety ?? 'Mixed' }}</td>
                                                            <td>{{ number_format($harvest->quantity_kg, 1) }} kg</td>
                                                            <td>
                                                                <span class="badge bg-{{ $harvest->quality_grade == 'Premium' ? 'success' : ($harvest->quality_grade == 'Grade A' ? 'primary' : 'secondary') }}">
                                                                    {{ $harvest->quality_grade ?? 'Standard' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Harvest Trends -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0"><i class="bi bi-calendar-date me-2"></i>Harvest Trends</h6>
                                        </div>
                                        <div class="card-body">
                                            @if($yieldAnalytics['dailyHarvestTrends']->count() > 0)
                                                <canvas id="weeklyYieldChart" height="150"></canvas>
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="bi bi-calendar-x text-muted" style="font-size: 2rem;"></i>
                                                    <p class="text-muted mt-2 mb-0">No harvest data in the last 30 days</p>
                                                    <small class="text-muted">Add harvest records to see trends</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cost Analytics Tab -->
                        <div class="tab-pane fade" id="cost" role="tabpanel" aria-labelledby="cost-tab">
                            <div class="row">
                                <!-- Monthly Cost Trends -->
                                <div class="col-lg-8 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-danger text-white">
                                            <h6 class="mb-0"><i class="bi bi-graph-down me-2"></i>Monthly Cost vs Revenue</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="costRevenueChart" height="100"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cost Breakdown -->
                                <div class="col-lg-4 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-secondary text-white">
                                            <h6 class="mb-0"><i class="bi bi-pie-chart-fill me-2"></i>Cost Categories</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="costBreakdownChart" height="150"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Expenses -->
                                <div class="col-lg-12 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-dark text-white">
                                            <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Recent Expenses</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Category</th>
                                                            <th>Description</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($recentCosts as $cost)
                                                        <tr>
                                                            <td>{{ $cost->date->format('M d') }}</td>
                                                            <td>
                                                                <span class="badge bg-secondary">{{ $cost->category }}</span>
                                                            </td>
                                                            <td>{{ $cost->description }}</td>
                                                            <td class="text-end fw-bold text-danger">RM{{ number_format($cost->amount, 2) }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Analytics Tab -->
                        <div class="tab-pane fade" id="customer" role="tabpanel" aria-labelledby="customer-tab">
                            <div class="row">
                                <!-- Customer Growth -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="bi bi-person-plus me-2"></i>Customer Growth</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="customerGrowthChart" height="120"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer Types -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="bi bi-people-fill me-2"></i>Customer Types</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="customerTypeChart" height="120"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Top Customers -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0"><i class="bi bi-trophy me-2"></i>Top Customers</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($topCustomers as $customer)
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <strong>{{ $customer->name }}</strong>
                                                    <small class="text-muted d-block">{{ $customer->location ?? 'Unknown location' }}</small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold text-success">RM{{ number_format($customer->total_revenue ?? 0, 2) }}</div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Geographic Distribution -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Geographic Distribution</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="locationChart" height="120"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Market Trends Tab -->
                        <div class="tab-pane fade" id="trends" role="tabpanel" aria-labelledby="trends-tab">
                            <div class="row">
                                <!-- Market Channel Distribution -->
                                <div class="col-lg-8 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Market Channel Distribution</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="channelDistributionChart" height="100"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Performance Metrics -->
                                <div class="col-lg-4 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Channel Performance</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <small class="text-muted">Total Customers</small>
                                                <div class="fw-bold">{{ $customerAnalytics['totalCustomers'] ?? 0 }}</div>
                                            </div>
                                            <div class="mb-3">
                                                <small class="text-muted">Overall Conversion Rate</small>
                                                <div class="fw-bold">{{ $customerAnalytics['retentionRate'] ?? 0 }}%</div>
                                            </div>
                                            <div class="mb-3">
                                                <small class="text-muted">Best Performing Channel</small>
                                                @php
                                                    $bestChannel = $customerAnalytics['sourceDistribution']->sortByDesc('conversion_rate')->first();
                                                @endphp
                                                <div class="fw-bold text-success">
                                                    {{ $bestChannel->label ?? 'N/A' }}
                                                    @if($bestChannel)
                                                        ({{ $bestChannel->conversion_rate }}%)
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <small class="text-muted">Channel Breakdown</small>
                                                <div class="mt-2">
                                                    @foreach($customerAnalytics['sourceDistribution'] as $source)
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="badge bg-secondary">{{ $source->label }}</span>
                                                        <small class="text-muted">
                                                            {{ $source->total_customers ?? $source->count ?? 0 }} customers 
                                                            ({{ $source->conversion_rate ?? 0 }}% convert)
                                                        </small>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Sales Activity -->
                                <div class="col-lg-12 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Sales Activity</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Customer</th>
                                                            <th>Quantity</th>
                                                            <th>Total</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($recentSales as $sale)
                                                        <tr>
                                                            <td>{{ $sale->sale_date->format('M d') }}</td>
                                                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                                            <td>{{ number_format($sale->quantity_kg, 1) }} kg</td>
                                                            <td class="fw-bold text-success">RM{{ number_format($sale->total_amount, 2) }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $sale->payment_status == 'paid' ? 'success' : ($sale->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                                                    {{ ucfirst($sale->payment_status) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Chart.js Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
// Chart configurations
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
        }
    },
    scales: {
        y: {
            beginAtZero: true
        }
    }
};

// Monthly Yield Chart
const monthlyYieldCtx = document.getElementById('monthlyYieldChart').getContext('2d');
new Chart(monthlyYieldCtx, {
    type: 'line',
    data: {
        labels: [
            '{{ $monthlyYield[0]['month'] ?? '' }}',
            '{{ $monthlyYield[1]['month'] ?? '' }}',
            '{{ $monthlyYield[2]['month'] ?? '' }}',
            '{{ $monthlyYield[3]['month'] ?? '' }}',
            '{{ $monthlyYield[4]['month'] ?? '' }}',
            '{{ $monthlyYield[5]['month'] ?? '' }}'
        ],
        datasets: [{
            label: 'Yield (kg)',
            data: [
                {{ $monthlyYield[0]['value'] ?? 0 }},
                {{ $monthlyYield[1]['value'] ?? 0 }},
                {{ $monthlyYield[2]['value'] ?? 0 }},
                {{ $monthlyYield[3]['value'] ?? 0 }},
                {{ $monthlyYield[4]['value'] ?? 0 }},
                {{ $monthlyYield[5]['value'] ?? 0 }}
            ],
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.3
        }]
    },
    options: chartOptions
});

// Variety Distribution Chart
const varietyCtx = document.getElementById('varietyChart').getContext('2d');
new Chart(varietyCtx, {
    type: 'doughnut',
    data: {
        labels: [
            @foreach($harvestByVariety as $variety)
            '{{ $variety->variety }}',
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($harvestByVariety as $variety)
                {{ $variety->total }},
                @endforeach
            ],
            backgroundColor: [
                'rgb(34, 197, 94)',
                'rgb(59, 130, 246)',
                'rgb(251, 191, 36)',
                'rgb(239, 68, 68)',
                'rgb(168, 85, 247)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Harvest Trends Chart
@if($yieldAnalytics['dailyHarvestTrends']->count() > 0)
const weeklyYieldCtx = document.getElementById('weeklyYieldChart').getContext('2d');
new Chart(weeklyYieldCtx, {
    type: 'line',
    data: {
        labels: [
            @foreach($yieldAnalytics['dailyHarvestTrends'] as $harvest)
            '{{ $harvest['date'] }}',
            @endforeach
        ],
        datasets: [{
            label: 'Daily Harvest (kg)',
            data: [
                @foreach($yieldAnalytics['dailyHarvestTrends'] as $harvest)
                {{ $harvest['yield'] }},
                @endforeach
            ],
            borderColor: 'rgb(251, 191, 36)',
            backgroundColor: 'rgba(251, 191, 36, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: 'rgb(251, 191, 36)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,0.1)'
                },
                ticks: {
                    callback: function(value) {
                        return value + ' kg';
                    }
                }
            },
            x: {
                grid: {
                    color: 'rgba(0,0,0,0.1)'
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: 'rgb(251, 191, 36)',
                borderWidth: 1,
                callbacks: {
                    title: function(context) {
                        const index = context[0].dataIndex;
                        const harvestData = [
                            @foreach($yieldAnalytics['dailyHarvestTrends'] as $harvest)
                            {
                                date: '{{ $harvest['date'] }}',
                                fullDate: '{{ $harvest['full_date'] }}',
                                dayName: '{{ $harvest['day_name'] }}'
                            },
                            @endforeach
                        ];
                        const harvest = harvestData[index];
                        return `${harvest.dayName}, ${harvest.date}`;
                    },
                    label: function(context) {
                        return `Harvest: ${context.parsed.y} kg`;
                    }
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});
@endif

// Cost vs Revenue Chart
const costRevenueCtx = document.getElementById('costRevenueChart').getContext('2d');
new Chart(costRevenueCtx, {
    type: 'line',
    data: {
        labels: [
            '{{ $monthlyRevenue[0]['month'] ?? '' }}',
            '{{ $monthlyRevenue[1]['month'] ?? '' }}',
            '{{ $monthlyRevenue[2]['month'] ?? '' }}',
            '{{ $monthlyRevenue[3]['month'] ?? '' }}',
            '{{ $monthlyRevenue[4]['month'] ?? '' }}',
            '{{ $monthlyRevenue[5]['month'] ?? '' }}'
        ],
        datasets: [{
            label: 'Revenue (RM)',
            data: [
                {{ $monthlyRevenue[0]['value'] ?? 0 }},
                {{ $monthlyRevenue[1]['value'] ?? 0 }},
                {{ $monthlyRevenue[2]['value'] ?? 0 }},
                {{ $monthlyRevenue[3]['value'] ?? 0 }},
                {{ $monthlyRevenue[4]['value'] ?? 0 }},
                {{ $monthlyRevenue[5]['value'] ?? 0 }}
            ],
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.3
        }, {
            label: 'Costs (RM)',
            data: [
                {{ $monthlyCosts[0]['value'] ?? 0 }},
                {{ $monthlyCosts[1]['value'] ?? 0 }},
                {{ $monthlyCosts[2]['value'] ?? 0 }},
                {{ $monthlyCosts[3]['value'] ?? 0 }},
                {{ $monthlyCosts[4]['value'] ?? 0 }},
                {{ $monthlyCosts[5]['value'] ?? 0 }}
            ],
            borderColor: 'rgb(239, 68, 68)',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.3
        }]
    },
    options: chartOptions
});

// Customer Growth Chart
const customerGrowthCtx = document.getElementById('customerGrowthChart').getContext('2d');
new Chart(customerGrowthCtx, {
    type: 'line',
    data: {
        labels: [
            @foreach($customerAnalytics['monthlyAcquisition'] as $month)
                '{{ $month['month'] }}'{{ !$loop->last ? ',' : '' }}
            @endforeach
        ],
        datasets: [
            {
                label: 'New Customers',
                data: [
                    @foreach($customerAnalytics['monthlyAcquisition'] as $month)
                        {{ $month['new_customers'] }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3,
                fill: false
            },
            {
                label: 'Customers with Purchases',
                data: [
                    @foreach($customerAnalytics['monthlyAcquisition'] as $month)
                        {{ $month['customers_with_purchases'] }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.3,
                fill: false
            }
        ]
    },
    options: {
        ...chartOptions,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    padding: 10,
                    usePointStyle: true,
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});

// Customer Type Chart
const customerTypeCtx = document.getElementById('customerTypeChart').getContext('2d');
new Chart(customerTypeCtx, {
    type: 'pie',
    data: {
        labels: [
            @foreach($customerAnalytics['typeDistribution'] as $type)
            '{{ ucfirst($type->customer_type) }}',
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($customerAnalytics['typeDistribution'] as $type)
                {{ $type->count }},
                @endforeach
            ],
            backgroundColor: [
                'rgb(34, 197, 94)',
                'rgb(59, 130, 246)',
                'rgb(251, 191, 36)'
            ]
        }]
    },
    plugins: [ChartDataLabels],
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    generateLabels: function(chart) {
                        const data = chart.data;
                        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                        
                        return data.labels.map((label, index) => {
                            const value = data.datasets[0].data[index];
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            
                            return {
                                text: `${label}: ${percentage}%`,
                                fillStyle: data.datasets[0].backgroundColor[index],
                                strokeStyle: data.datasets[0].backgroundColor[index],
                                lineWidth: 0,
                                hidden: false,
                                index: index
                            };
                        });
                    }
                }
            },
            datalabels: {
                display: true,
                color: 'white',
                font: {
                    weight: 'bold',
                    size: 14
                },
                formatter: function(value, context) {
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                    return percentage >= 5 ? percentage + '%' : ''; // Only show if >= 5%
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? Math.round((context.raw / total) * 100) : 0;
                        return context.label + ': ' + context.raw + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Location Distribution Chart
const locationCtx = document.getElementById('locationChart').getContext('2d');
new Chart(locationCtx, {
    type: 'bar',
    data: {
        labels: [
            @foreach($customerAnalytics['locationStats'] as $location)
            '{{ $location->location }}',
            @endforeach
        ],
        datasets: [{
            label: 'Customers',
            data: [
                @foreach($customerAnalytics['locationStats'] as $location)
                {{ $location->customer_count }},
                @endforeach
            ],
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1
        }]
    },
    options: chartOptions
});

// Market Channel Distribution Chart
const channelDistributionCtx = document.getElementById('channelDistributionChart').getContext('2d');
new Chart(channelDistributionCtx, {
    type: 'bar',
    data: {
        labels: [
            @foreach($customerAnalytics['sourceDistribution'] as $source)
                '{{ $source->label }}'{{ !$loop->last ? ',' : '' }}
            @endforeach
        ],
        datasets: [
            {
                label: 'Total Customers',
                data: [
                    @foreach($customerAnalytics['sourceDistribution'] as $source)
                        {{ $source->total_customers ?? $source->count ?? 0 }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1,
                yAxisID: 'y'
            },
            {
                label: 'Customers with Purchases',
                data: [
                    @foreach($customerAnalytics['sourceDistribution'] as $source)
                        {{ $source->customers_with_purchases ?? 0 }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1,
                yAxisID: 'y'
            },
            {
                label: 'Conversion Rate (%)',
                data: [
                    @foreach($customerAnalytics['sourceDistribution'] as $source)
                        {{ $source->conversion_rate ?? 0 }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                type: 'line',
                borderColor: 'rgb(251, 191, 36)',
                backgroundColor: 'rgba(251, 191, 36, 0.1)',
                borderWidth: 3,
                pointBackgroundColor: 'rgb(251, 191, 36)',
                pointBorderColor: 'rgb(251, 191, 36)',
                pointRadius: 5,
                pointHoverRadius: 7,
                yAxisID: 'y1',
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    padding: 15,
                    usePointStyle: true
                }
            },
            tooltip: {
                callbacks: {
                    title: function(context) {
                        return 'Channel: ' + context[0].label;
                    },
                    label: function(context) {
                        if (context.dataset.label === 'Conversion Rate (%)') {
                            return context.dataset.label + ': ' + context.parsed.y + '%';
                        }
                        return context.dataset.label + ': ' + context.parsed.y + ' customers';
                    }
                }
            }
        },
        scales: {
            x: {
                display: true,
                grid: {
                    display: false
                },
                ticks: {
                    maxRotation: 45,
                    minRotation: 0
                }
            },
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Number of Customers'
                },
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Conversion Rate (%)'
                },
                beginAtZero: true,
                max: 100,
                grid: {
                    drawOnChartArea: false,
                },
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});

// Cost Breakdown Chart
const costBreakdownCtx = document.getElementById('costBreakdownChart').getContext('2d');
new Chart(costBreakdownCtx, {
    type: 'doughnut',
    data: {
        labels: [
            @foreach($costAnalytics['categoryBreakdown'] as $category)
            '{{ ucfirst($category->category) }}',
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($costAnalytics['categoryBreakdown'] as $category)
                {{ $category->total }},
                @endforeach
            ],
            backgroundColor: [
                'rgb(239, 68, 68)',    // Red
                'rgb(251, 191, 36)',   // Yellow
                'rgb(34, 197, 94)',    // Green
                'rgb(59, 130, 246)',   // Blue
                'rgb(168, 85, 247)',   // Purple
                'rgb(236, 72, 153)',   // Pink
                'rgb(14, 165, 233)',   // Sky
                'rgb(139, 69, 19)',    // Brown
                'rgb(245, 101, 101)',  // Light Red
                'rgb(34, 211, 238)',   // Cyan
                'rgb(252, 165, 165)',  // Light Pink
                'rgb(167, 243, 208)',  // Light Green
                'rgb(196, 181, 253)',  // Light Purple
                'rgb(254, 240, 138)',  // Light Yellow
                'rgb(156, 163, 175)'   // Gray
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label;
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: RM${value.toLocaleString()} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// Monthly Trends Chart
const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
new Chart(monthlyTrendsCtx, {
    type: 'line',
    data: {
        labels: [
            @foreach($monthlyYield as $month)
            '{{ $month['month'] }}',
            @endforeach
        ],
        datasets: [
            {
                label: 'Yield (kg)',
                data: [
                    @foreach($monthlyYield as $month)
                    {{ $month['value'] }},
                    @endforeach
                ],
                borderColor: 'rgb(251, 191, 36)',
                backgroundColor: 'rgba(251, 191, 36, 0.1)',
                tension: 0.3,
                fill: false,
                yAxisID: 'y'
            },
            {
                label: 'Revenue (RM)',
                data: [
                    @foreach($monthlyRevenue as $month)
                    {{ $month['value'] }},
                    @endforeach
                ],
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.3,
                fill: false,
                yAxisID: 'y1'
            },
            {
                label: 'Costs (RM)',
                data: [
                    @foreach($monthlyCosts as $month)
                    {{ $month['value'] }},
                    @endforeach
                ],
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.3,
                fill: false,
                yAxisID: 'y1'
            },
            {
                label: 'Net Profit (RM)',
                data: [
                    @php
                        $netProfits = [];
                        foreach($monthlyRevenue as $index => $revenueMonth) {
                            $revenue = $revenueMonth['value'];
                            $cost = $monthlyCosts[$index]['value'] ?? 0;
                            $netProfits[] = $revenue - $cost;
                        }
                    @endphp
                    @foreach($netProfits as $profit)
                    {{ $profit }},
                    @endforeach
                ],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3,
                fill: false,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    font: {
                        size: 12,
                        weight: 'bold'
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: 'white',
                bodyColor: 'white',
                borderColor: 'rgba(255, 255, 255, 0.2)',
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: true,
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.dataset.label === 'Yield (kg)') {
                            label += Math.round(context.parsed.y) + ' kg';
                        } else {
                            label += 'RM ' + context.parsed.y.toLocaleString();
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            x: {
                display: true,
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        weight: 'bold'
                    }
                }
            },
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Yield (kg)',
                    font: {
                        weight: 'bold'
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                },
                ticks: {
                    callback: function(value) {
                        return value + ' kg';
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Amount (RM)',
                    font: {
                        weight: 'bold'
                    }
                },
                grid: {
                    drawOnChartArea: false,
                },
                ticks: {
                    callback: function(value) {
                        return 'RM ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('#analyticsTabs .nav-link');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active classes from all tabs
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'text-primary', 'border-primary');
                btn.classList.add('text-muted');
                btn.setAttribute('aria-selected', 'false');
            });
            
            // Add active classes to clicked tab
            this.classList.add('active', 'text-primary', 'border-primary');
            this.classList.remove('text-muted');
            this.setAttribute('aria-selected', 'true');
        });
    });
});

</script>

<style>
</div>

<style>
.bg-gradient-success {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
}
.bg-gradient-danger {
    background: linear-gradient(135deg, #dc3545, #e83e8c) !important;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #6610f2) !important;
}
.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8, #6f42c1) !important;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.btn {
    transition: all 0.2s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
}
</style>
@endsection
