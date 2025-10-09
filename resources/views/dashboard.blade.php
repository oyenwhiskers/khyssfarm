@extends('layouts.app')

@section('title', 'Dashboard - KHYSS Chili Farm')
@section('page-title', 'Farm Analytics Dashboard')

@section('styles')
<style>
    .nav-tabs .nav-link {
        border: 2px solid transparent;
        border-radius: 10px 10px 0 0;
        font-weight: 600;
        padding: 12px 20px;
        margin-right: 5px;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link:hover {
        border-color: #dee2e6 #dee2e6 #fff;
        isolation: isolate;
        background-color: #f8f9fa;
    }
    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white !important;
        border-color: #28a745;
    }
    .tab-content {
        padding-top: 20px;
    }
    .analytics-card {
        transition: transform 0.3s ease;
    }
    .analytics-card:hover {
        transform: translateY(-3px);
    }
    
    /* Enhanced Action Button Styles */
    .btn.rounded-pill {
        transition: all 0.3s ease;
        border-width: 2px;
        font-weight: 500;
    }
    
    .btn.rounded-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    
    .btn-primary.rounded-pill {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border-color: #007bff;
    }
    
    .btn-info.rounded-pill {
        background: linear-gradient(135deg, #17a2b8, #138496);
        border-color: #17a2b8;
    }
    
    .btn-outline-info.rounded-pill:hover,
    .btn-outline-primary.rounded-pill:hover,
    .btn-outline-danger.rounded-pill:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }
    
    /* Table action buttons spacing */
    .d-flex.gap-1 {
        gap: 0.25rem !important;
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- Key Metrics Cards -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card metric-card">
            <div class="card-body text-center">
                <i class="fas fa-weight fa-2x mb-3"></i>
                <h3 class="mb-1">{{ number_format($totalYield, 2) }} kg</h3>
                <p class="mb-0">Total Yield</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card metric-card revenue">
            <div class="card-body text-center">
                <i class="fas fa-dollar-sign fa-2x mb-3"></i>
                <h3 class="mb-1">RM{{ number_format($totalRevenue, 2) }}</h3>
                <p class="mb-0">Confirmed Revenue</p>
                @if($pendingRevenue > 0)
                    <small class="text-muted">+RM{{ number_format($pendingRevenue, 2) }} pending</small>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card metric-card cost">
            <div class="card-body text-center">
                <i class="fas fa-minus-circle fa-2x mb-3"></i>
                <h3 class="mb-1">RM{{ number_format($totalCosts, 2) }}</h3>
                <p class="mb-0">Total Costs</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card metric-card profit">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-2x mb-3"></i>
                <h3 class="mb-1 {{ $netProfit >= 0 ? '' : 'text-warning' }}">RM{{ number_format($netProfit, 2) }}</h3>
                <p class="mb-0">Net Profit</p>
            </div>
        </div>
    </div>
</div>

@if($pendingRevenue > 0 || $partialRevenue > 0)
<!-- Pending Payments Alert -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            <div class="flex-grow-1">
                <h6 class="mb-1">Outstanding Payments</h6>
                <div class="row">
                    @if($pendingRevenue > 0)
                    <div class="col-md-6">
                        <strong>Pending Payments:</strong> RM{{ number_format($pendingRevenue, 2) }}
                    </div>
                    @endif
                    @if($partialRevenue > 0)
                    <div class="col-md-6">
                        <strong>Partial Payments:</strong> RM{{ number_format($partialRevenue, 2) }}
                    </div>
                    @endif
                </div>
            </div>
            <a href="{{ route('sales.index') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-eye me-1"></i>View Sales
            </a>
        </div>
    </div>
</div>
@endif

<div class="row">
    <!-- Monthly Trends Chart -->
    <div class="col-lg-9 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Monthly Trends</h5>
            </div>
            <div class="card-body">
                <canvas id="trendsChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Quick Stats</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3 p-2 bg-light rounded">
                    <span><i class="fas fa-money-bill-wave text-success me-2"></i>Avg Price/kg:</span>
                    <strong class="text-success">RM{{ number_format($averagePricePerKg, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3 p-2 bg-light rounded">
                    <span><i class="fas fa-users text-primary me-2"></i>Total Customers:</span>
                    <strong class="text-primary">{{ $topCustomers->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3 p-2 bg-light rounded">
                    <span><i class="fas fa-seedling text-info me-2"></i>Recent Harvests:</span>
                    <strong class="text-info">{{ $recentHarvests->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between p-2 bg-light rounded">
                    <span><i class="fas fa-clock text-warning me-2"></i>Pending Sales:</span>
                    <strong class="text-warning">{{ $recentSales->where('payment_status', 'pending')->count() }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Customers -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #28a745, #20c997);">
                <h5 class="mb-0"><i class="fas fa-crown me-2"></i>Top Customers</h5>
            </div>
            <div class="card-body">
                @forelse($topCustomers->take(5) as $customer)
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                    <div>
                        <strong class="text-dark">{{ $customer->name }}</strong>
                        <br><small class="badge bg-secondary">{{ ucfirst($customer->customer_type) }}</small>
                    </div>
                    <div class="text-end">
                        <strong class="text-success">RM{{ number_format($customer->sales_sum_total_amount ?? 0, 2) }}</strong>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No customer data available.</p>
                @endforelse
                @if($topCustomers->count() > 5)
                <div class="text-center mt-3">
                    <a href="{{ route('customers.index') }}" class="btn btn-primary btn-sm rounded-pill px-4 py-2 shadow-sm">
                        <i class="fas fa-users me-2"></i>View All Customers
                        <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #17a2b8, #20c997);">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Activity</h5>
            </div>
            <div class="card-body">
                @forelse($recentSales->take(5) as $sale)
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                    <div>
                        <strong class="text-dark">{{ $sale->quantity_kg }}kg Sale</strong>
                        <br><small class="text-muted">{{ $sale->customer ? $sale->customer->name : 'Walk-in Customer' }}</small>
                    </div>
                    <div class="text-end">
                        <strong class="text-success">RM{{ number_format($sale->total_amount, 2) }}</strong>
                        <br><small class="text-muted">{{ $sale->sale_date->format('M d') }}</small>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No recent sales.</p>
                @endforelse
                @if($recentSales->count() > 5)
                <div class="text-center mt-3">
                    <a href="{{ route('sales.index') }}" class="btn btn-info btn-sm rounded-pill px-4 py-2 shadow-sm">
                        <i class="fas fa-chart-line me-2"></i>View All Sales
                        <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Customer Location Distribution -->
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Customer Location Distribution</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-5">
                        <canvas id="locationChart" height="200"></canvas>
                    </div>
                    <div class="col-lg-7">
                        <div class="mt-3">
                            <h6 class="text-muted">Location Summary</h6>
                            @forelse($customersByLocation as $location)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <span><i class="fas fa-map-pin text-primary me-2"></i>{{ $location->location }}</span>
                                <span class="badge bg-primary">{{ $location->count }} customers</span>
                            </div>
                            @empty
                            <p class="text-muted">No location data available.</p>
                            @endforelse
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
        <div class="card">
            <div class="card-header">
                <h3 class="mb-3"><i class="fas fa-chart-line me-2"></i>Farm Analytics Dashboard</h3>
                
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="analyticsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="yield-tab" data-bs-toggle="tab" data-bs-target="#yield" type="button" role="tab" aria-controls="yield" aria-selected="true">
                            <i class="fas fa-seedling me-2"></i>Yield Analytics
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" type="button" role="tab" aria-controls="customer" aria-selected="false">
                            <i class="fas fa-users me-2"></i>Customer Analytics
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cost-tab" data-bs-toggle="tab" data-bs-target="#cost" type="button" role="tab" aria-controls="cost" aria-selected="false">
                            <i class="fas fa-calculator me-2"></i>Cost Analytics
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body">
                <!-- Tab Content -->
                <div class="tab-content" id="analyticsTabContent">
                    
                    <!-- YIELD ANALYTICS TAB -->
                    <div class="tab-pane fade show active" id="yield" role="tabpanel" aria-labelledby="yield-tab">
                        <!-- Yield Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card bg-success text-white analytics-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                                        <h4>{{ number_format($yieldAnalytics['avgDailyYield'], 2) }} kg</h4>
                                        <p class="mb-0">Avg Daily Yield</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card bg-info text-white analytics-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-pepper-hot fa-2x mb-2"></i>
                                        <h4>{{ $yieldAnalytics['totalVarieties'] }}</h4>
                                        <p class="mb-0">Varieties Grown</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card bg-warning text-white analytics-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-trophy fa-2x mb-2"></i>
                                        <h5>{{ $yieldAnalytics['bestMonth']['month'] ?? 'N/A' }}</h5>
                                        <p class="mb-0">Best Month</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card bg-primary text-white analytics-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-weight fa-2x mb-2"></i>
                                        <h4>{{ number_format($yieldAnalytics['bestMonth']['yield'] ?? 0, 2) }} kg</h4>
                                        <p class="mb-0">Peak Yield</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Yield Charts -->
                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Monthly Yield Trends</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="monthlyYieldChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-seedling me-2"></i>Daily Harvest (Last 30 Days)</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="dailyHarvestChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Variety Charts - Hidden for now -->
                        <div class="row d-none">
                            <div class="col-lg-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Yield by Variety (Detailed)</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="yieldVarietyChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-pepper-hot me-2"></i>Harvest by Variety (Overview)</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="varietyChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- CUSTOMER ANALYTICS TAB -->
                    <div class="tab-pane fade" id="customer" role="tabpanel" aria-labelledby="customer-tab">
                        <!-- Customer Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <h4>{{ $customerAnalytics['totalCustomers'] }}</h4>
                                        <p class="mb-0">Total Customers</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-redo fa-2x mb-2"></i>
                                        <h4>{{ $customerAnalytics['retentionRate'] }}%</h4>
                                        <p class="mb-0">Retention Rate</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-heart fa-2x mb-2"></i>
                                        <h4>{{ $customerAnalytics['repeatCustomers'] }}</h4>
                                        <p class="mb-0">Repeat Customers</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-crown fa-2x mb-2"></i>
                                        <h4>{{ ($customerAnalytics['topCustomers']->first()->sales_sum_total_amount ?? 0) > 0 ? 'RM' . number_format($customerAnalytics['topCustomers']->first()->sales_sum_total_amount, 0) : 'N/A' }}</h4>
                                        <p class="mb-0">Top Customer Value</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Customer Charts -->
                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-doughnut me-2"></i>Customer Types</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="customerTypeChart" height="180"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Purchase Patterns</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="customerPurchaseChart" height="180"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Customer Acquisition Channels -->
                        <div class="row">
                            <div class="col-lg-8 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Customer Acquisition Channels</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="customerSourceChart" height="120"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Channel Breakdown</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($customerAnalytics['sourceDistribution']->isNotEmpty())
                                            @foreach($customerAnalytics['sourceDistribution'] as $source)
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="fw-bold">{{ $source->label }}</span>
                                                    <span class="badge bg-primary">{{ $source->count }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted text-center">No source data available yet</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Monthly Acquisition Chart -->
                        <div class="row">
                            
                            <div class="col-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Customer Acquisition</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="customerAcquisitionChart" height="120"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- COST ANALYTICS TAB -->
                    <div class="tab-pane fade" id="cost" role="tabpanel" aria-labelledby="cost-tab">
                        <!-- Cost Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                        <h4>RM{{ number_format($costAnalytics['avgDailyCost'], 2) }}</h4>
                                        <p class="mb-0">Avg Daily Cost</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-percentage fa-2x mb-2"></i>
                                        <h4>{{ $costAnalytics['costRevenueRatio'] }}%</h4>
                                        <p class="mb-0">Cost/Revenue Ratio</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-tags fa-2x mb-2"></i>
                                        <h4>{{ $costAnalytics['totalCategories'] }}</h4>
                                        <p class="mb-0">Cost Categories</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card bg-secondary text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                        <h5>{{ ucfirst($costAnalytics['highestCategory']->category ?? 'N/A') }}</h5>
                                        <p class="mb-0">Highest Category</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cost Charts -->
                        <div class="row">
                            <div class="col-lg-8 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Cost Trends</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="monthlyCostChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Cost by Category</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="costCategoryChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Weekly Cost Analysis</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="weeklyCostChart" height="100"></canvas>
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
@endsection

@section('scripts')
<script>
// Monthly Trends Chart
const trendsCtx = document.getElementById('trendsChart').getContext('2d');
new Chart(trendsCtx, {
    type: 'line',
    data: {
        labels: @json($monthlyRevenue->pluck('month')),
        datasets: [{
            label: 'Revenue',
            data: @json($monthlyRevenue->pluck('value')),
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4
        }, {
            label: 'Costs',
            data: @json($monthlyCosts->pluck('value')),
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            tension: 0.4
        }, {
            label: 'Yield (kg)',
            data: @json($monthlyYield->pluck('value')),
            borderColor: '#17a2b8',
            backgroundColor: 'rgba(23, 162, 184, 0.1)',
            tension: 0.4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false,
                },
            }
        }
    }
});

// Customer Location Chart
const locationCtx = document.getElementById('locationChart').getContext('2d');
new Chart(locationCtx, {
    type: 'doughnut',
    data: {
        labels: @json($customersByLocation->pluck('location')),
        datasets: [{
            data: @json($customersByLocation->pluck('count')),
            backgroundColor: [
                '#28a745',
                '#17a2b8',
                '#ffc107',
                '#dc3545',
                '#6f42c1',
                '#20c997'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    usePointStyle: true,
                    font: {
                        size: 12
                    }
                }
            }
        },
        cutout: '60%'
    }
});

// Daily Harvest Chart
const dailyHarvestCtx = document.getElementById('dailyHarvestChart').getContext('2d');
new Chart(dailyHarvestCtx, {
    type: 'bar',
    data: {
        labels: @json($dailyHarvests->pluck('date')),
        datasets: [{
            label: 'Daily Harvest (kg)',
            data: @json($dailyHarvests->pluck('quantity')),
            backgroundColor: 'rgba(40, 167, 69, 0.6)',
            borderColor: '#28a745',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Quantity (kg)'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Date'
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y + ' kg';
                    }
                }
            }
        }
    }
});

// Harvest by Variety Chart
const varietyCtx = document.getElementById('varietyChart').getContext('2d');
new Chart(varietyCtx, {
    type: 'pie',
    data: {
        labels: @json($harvestByVariety->pluck('variety')->map(function($variety) { return ucfirst($variety); })),
        datasets: [{
            data: @json($harvestByVariety->pluck('total')),
            backgroundColor: [
                '#dc3545',  // Red for spicy varieties
                '#fd7e14',  // Orange
                '#ffc107',  // Yellow
                '#28a745',  // Green
                '#20c997',  // Teal
                '#6f42c1'   // Purple
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    fontSize: 12
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + context.parsed + ' kg (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// ===== YIELD ANALYTICS CHARTS =====

// Monthly Yield Trends Chart
const monthlyYieldCtx = document.getElementById('monthlyYieldChart').getContext('2d');
new Chart(monthlyYieldCtx, {
    type: 'line',
    data: {
        labels: @json($yieldAnalytics['monthlyComparison']->pluck('month')),
        datasets: [{
            label: 'Monthly Yield (kg)',
            data: @json($yieldAnalytics['monthlyComparison']->pluck('yield')),
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Yield (kg)'
                }
            }
        }
    }
});

// Yield by Variety Chart
const yieldVarietyCtx = document.getElementById('yieldVarietyChart').getContext('2d');
new Chart(yieldVarietyCtx, {
    type: 'doughnut',
    data: {
        labels: @json($yieldAnalytics['varietyBreakdown']->pluck('variety')->map(function($variety) { return ucfirst($variety); })),
        datasets: [{
            data: @json($yieldAnalytics['varietyBreakdown']->pluck('total')),
            backgroundColor: [
                '#dc3545', '#fd7e14', '#ffc107', '#28a745', '#20c997', '#6f42c1', '#17a2b8'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const variety = context.label;
                        const value = context.parsed;
                        const percentage = @json($yieldAnalytics['varietyBreakdown']->pluck('percentage'))[context.dataIndex];
                        return variety + ': ' + value + ' kg (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// ===== CUSTOMER ANALYTICS CHARTS =====

// Customer Types Chart
const customerTypeCtx = document.getElementById('customerTypeChart').getContext('2d');
new Chart(customerTypeCtx, {
    type: 'pie',
    data: {
        labels: @json($customerAnalytics['typeDistribution']->pluck('customer_type')->map(function($type) { return ucfirst($type); })),
        datasets: [{
            data: @json($customerAnalytics['typeDistribution']->pluck('count')),
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    usePointStyle: true,
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

// Customer Purchase Patterns Chart
const customerPurchaseCtx = document.getElementById('customerPurchaseChart').getContext('2d');
new Chart(customerPurchaseCtx, {
    type: 'bar',
    data: {
        labels: @json($customerAnalytics['purchasePatterns']->keys()),
        datasets: [{
            label: 'Number of Customers',
            data: @json($customerAnalytics['purchasePatterns']->values()),
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: '#36a2eb',
            borderWidth: 2,
            borderRadius: 4
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
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Customer Source Distribution Chart
const customerSourceCtx = document.getElementById('customerSourceChart').getContext('2d');
new Chart(customerSourceCtx, {
    type: 'doughnut',
    data: {
        labels: @json($customerAnalytics['sourceDistribution']->pluck('label')),
        datasets: [{
            data: @json($customerAnalytics['sourceDistribution']->pluck('count')),
            backgroundColor: [
                '#4267B2', // Facebook blue
                '#E4405F', // Instagram pink  
                '#000000', // TikTok black
                '#25D366', // WhatsApp green
                '#FFA500', // Recommendation orange
                '#6F42C1', // Repeat customer purple
                '#6C757D', // Walk-in gray
                '#0D6EFD', // Online search blue
                '#FD7E14', // Marketplace orange
                '#DC3545'  // Other red
            ],
            borderWidth: 2,
            borderColor: '#fff',
            hoverBorderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    font: {
                        size: 11
                    },
                    generateLabels: function(chart) {
                        const data = chart.data;
                        if (data.labels.length && data.datasets.length) {
                            return data.labels.map((label, i) => {
                                const count = data.datasets[0].data[i];
                                const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = ((count / total) * 100).toFixed(1);
                                return {
                                    text: `${label} (${percentage}%)`,
                                    fillStyle: data.datasets[0].backgroundColor[i],
                                    strokeStyle: data.datasets[0].borderColor,
                                    lineWidth: data.datasets[0].borderWidth,
                                    hidden: false,
                                    index: i
                                };
                            });
                        }
                        return [];
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return `${context.label}: ${context.parsed} customers (${percentage}%)`;
                    }
                }
            }
        },
        cutout: '50%'
    }
});

// Customer Acquisition Chart
const customerAcquisitionCtx = document.getElementById('customerAcquisitionChart').getContext('2d');
new Chart(customerAcquisitionCtx, {
    type: 'line',
    data: {
        labels: @json($customerAnalytics['monthlyAcquisition']->pluck('month')),
        datasets: [{
            label: 'New Customers',
            data: @json($customerAnalytics['monthlyAcquisition']->pluck('count')),
            borderColor: '#17a2b8',
            backgroundColor: 'rgba(23, 162, 184, 0.1)',
            borderWidth: 3,
            pointBackgroundColor: '#17a2b8',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            tension: 0.4,
            fill: true
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
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// ===== COST ANALYTICS CHARTS =====

// Monthly Cost Trends Chart
const monthlyCostCtx = document.getElementById('monthlyCostChart').getContext('2d');
new Chart(monthlyCostCtx, {
    type: 'line',
    data: {
        labels: @json($costAnalytics['monthlyTrends']->pluck('month')),
        datasets: [{
            label: 'Monthly Costs (RM)',
            data: @json($costAnalytics['monthlyTrends']->pluck('cost')),
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Cost (RM)'
                }
            }
        }
    }
});

// Cost by Category Chart
const costCategoryCtx = document.getElementById('costCategoryChart').getContext('2d');
new Chart(costCategoryCtx, {
    type: 'doughnut',
    data: {
        labels: @json($costAnalytics['categoryBreakdown']->pluck('category')->map(function($category) { return ucfirst($category); })),
        datasets: [{
            data: @json($costAnalytics['categoryBreakdown']->pluck('total')),
            backgroundColor: [
                '#dc3545', '#fd7e14', '#ffc107', '#28a745', '#20c997', '#6f42c1', '#17a2b8', '#6c757d'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const category = context.label;
                        const value = 'RM' + context.parsed.toFixed(2);
                        const percentage = @json($costAnalytics['categoryBreakdown']->pluck('percentage'))[context.dataIndex];
                        return category + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Weekly Cost Analysis Chart
const weeklyCostCtx = document.getElementById('weeklyCostChart').getContext('2d');
new Chart(weeklyCostCtx, {
    type: 'bar',
    data: {
        labels: @json($costAnalytics['weeklyAnalysis']->pluck('week')),
        datasets: [{
            label: 'Weekly Costs (RM)',
            data: @json($costAnalytics['weeklyAnalysis']->pluck('cost')),
            backgroundColor: 'rgba(255, 193, 7, 0.6)',
            borderColor: '#ffc107',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Cost (RM)'
                }
            }
        }
    }
});
</script>
@endsection
