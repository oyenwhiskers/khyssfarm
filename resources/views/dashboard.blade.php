@extends('layouts.app')

@section('styles')
<style>
    /* Dashboard Container */
    .dashboard-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
        min-height: 100vh;
        padding: 1rem 0;
    }
    
    /* Header Section */
    .dashboard-header {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-bottom: 1rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .dashboard-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.15rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .dashboard-subtitle {
        color: #718096;
        font-size: 0.85rem;
        margin: 0;
    }
    
    /* Date Filter Card */
    .date-filter-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .date-filter-card:hover {
        box-shadow: 0 3px 16px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .date-filter-card .form-label {
        font-size: 0.9rem;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }
    
    .date-filter-card .form-control {
        border-radius: 6px;
        border: 1px solid #cbd5e0;
        transition: all 0.2s ease;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        height: 38px;
    }
    
    .date-filter-card .form-control:focus {
        border-color: #4299e1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
    }
    
    .date-filter-card .btn {
        height: 38px;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    /* Metric Cards */
    .metric-card {
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .metric-card:hover::before {
        opacity: 1;
    }
    
    .metric-card:hover {
        transform: translateY(-5px) scale(1.01);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    
    .metric-card .card-body {
        padding: 1.25rem;
    }
    
    .metric-value {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.35rem;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }
    
    .metric-label {
        font-size: 0.8rem;
        font-weight: 500;
        opacity: 0.95;
        letter-spacing: 0.2px;
    }
    
    .metric-icon {
        font-size: 2rem;
        opacity: 0.3;
        transition: all 0.3s ease;
    }
    
    .metric-card:hover .metric-icon {
        opacity: 0.5;
        transform: scale(1.1);
    }
    
    /* Business Stream Cards */
    .business-stream-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        background: white;
    }
    
    .business-stream-card:hover {
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        transform: translateY(-3px);
    }
    
    .business-stream-card .card-header {
        padding: 1rem 1.25rem;
        border-bottom: none;
    }
    
    .business-stream-card .card-header h5 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
    }
    
    .business-stream-card .card-body {
        padding: 1rem 1.25rem;
    }
    
    .metric-box {
        background: linear-gradient(135deg, #f8f9fa, #ffffff);
        border-radius: 10px;
        padding: 1rem;
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        height: 100%;
        min-height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    
    .metric-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
    }
    
    .metric-box-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .metric-box-value {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.15rem;
    }
    
    .metric-box-label {
        font-size: 0.8rem;
        color: #718096;
        font-weight: 500;
    }
    
    /* Quick Action Buttons */
    .quick-action-btn {
        border-radius: 6px;
        padding: 0.4rem 0.75rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.12);
    }
    
    /* Alert Styling */
    .alert-modern {
        border-radius: 10px;
        border: none;
        box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        padding: 1rem;
    }
    
    /* Chart Cards */
    .chart-card {
        border-radius: 12px;
        background: white;
        box-shadow: 0 3px 15px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
    }
    
    .chart-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .chart-card .card-header {
        background: white;
        border-bottom: 2px solid #f1f5f9;
        border-radius: 12px 12px 0 0 !important;
        padding: 1rem 1.25rem;
    }
    
    .chart-card .card-body {
        padding: 1rem 1.25rem;
    }
    
    .chart-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }
    
    /* Trend Indicators */
    .trend-indicator {
        background: white;
        border-radius: 10px;
        padding: 0.875rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
    }
    
    .trend-indicator:hover {
        box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    
    .trend-indicator h6 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
    }
    
    .trend-indicator small {
        font-size: 0.7rem;
        color: #718096;
    }
    
    /* Analytics Tabs */
    .analytics-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #718096;
        font-weight: 600;
        padding: 0.625rem 1.25rem;
        transition: all 0.3s ease;
        border-radius: 0;
        font-size: 0.9rem;
    }
    
    .analytics-tabs .nav-link:hover {
        color: #4299e1;
        background: rgba(66, 153, 225, 0.05);
    }
    
    .analytics-tabs .nav-link.active {
        color: #2b6cb0;
        border-bottom-color: #4299e1;
        background: rgba(66, 153, 225, 0.08);
    }
    
    /* Analytics Cards */
    .analytics-card {
        border-radius: 10px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .analytics-card:hover {
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    }
    
    .analytics-card .card-header {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .analytics-card .card-body {
        padding: 1rem;
    }
    
    /* Table Styling */
    .analytics-table {
        margin: 0;
    }
    
    .analytics-table thead th {
        background: #f8f9fa;
        color: #4a5568;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        padding: 0.75rem 0.875rem;
    }
    
    .analytics-table tbody tr {
        transition: background 0.2s ease;
    }
    
    .analytics-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .analytics-table tbody td {
        padding: 0.75rem 0.875rem;
        border-color: #f1f5f9;
        vertical-align: middle;
        font-size: 0.875rem;
    }
    
    /* Badge Styling */
    .badge-modern {
        padding: 0.3rem 0.65rem;
        border-radius: 5px;
        font-weight: 600;
        font-size: 0.7rem;
        letter-spacing: 0.3px;
    }
    
    /* Dropdown Styling */
    .dropdown-toggle {
        border-radius: 6px;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }
    
    .dropdown-toggle:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    
    /* Section Spacing */
    .row.mb-5 {
        margin-bottom: 2rem !important;
    }
    
    .row.mt-5 {
        margin-top: 2rem !important;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .dashboard-title {
            font-size: 1.25rem;
        }
        
        .metric-value {
            font-size: 1.25rem;
        }
        
        .metric-icon {
            font-size: 1.75rem;
        }
        
        .metric-card .card-body {
            padding: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid dashboard-container">
    <!-- Header Section with Date Filter -->
    <div class="row mb-3">
        <div class="col-lg-8 mb-3">
            <div class="dashboard-header">
                <h1 class="dashboard-title">
                    <i class="fas fa-chart-line text-primary"></i>
                    Farm Analytics Dashboard
                </h1>
                <p class="dashboard-subtitle">
                    <i class="fas fa-info-circle me-1"></i>
                    Comprehensive overview of your agricultural business
                </p>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="date-filter-card p-3">
                <form method="GET" action="{{ route('dashboard') }}" id="dashboardFilterForm">
                    <label class="form-label fw-bold mb-2">
                        <i class="fas fa-calendar-alt me-1 text-primary"></i>
                        Date Range
                    </label>
                    <div class="row g-2">
                        <div class="col-5">
                            <input type="date" name="date_from" class="form-control" 
                                   value="{{ request('date_from') }}" placeholder="From">
                        </div>
                        <div class="col-5">
                            <input type="date" name="date_to" class="form-control" 
                                   value="{{ request('date_to') }}" placeholder="To">
                        </div>
                        <div class="col-2">
                            <button type="submit" class="btn btn-primary w-100" title="Apply Filter">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                    @if(request('date_from') || request('date_to'))
                    <div class="mt-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i>Clear Filters
                        </a>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-3">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 metric-card" style="background: linear-gradient(135deg, #28a745, #20c997);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="metric-value">RM{{ number_format($totalRevenue, 2) }}</div>
                            <p class="metric-label mb-0">
                                <i class="fas fa-arrow-up me-1"></i>
                                Total Revenue
                            </p>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 metric-card" style="background: linear-gradient(135deg, #dc3545, #e76f7c);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="metric-value">RM{{ number_format($totalAllCosts, 2) }}</div>
                            <p class="metric-label mb-0">
                                <i class="fas fa-arrow-down me-1"></i>
                                Total Costs
                            </p>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 metric-card" style="background: linear-gradient(135deg, #007bff, #4da3ff);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="metric-value">RM{{ number_format($grandTotalProfit, 2) }}</div>
                            <p class="metric-label mb-0">
                                <i class="fas fa-trophy me-1"></i>
                                Net Profit
                            </p>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 metric-card" style="background: linear-gradient(135deg, #e91e63, #f06292);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="metric-value">{{ number_format($totalRevenue > 0 ? ($grandTotalProfit / $totalRevenue) * 100 : 0, 1) }}%</div>
                            <p class="metric-label mb-0">
                                <i class="fas fa-percent me-1"></i>
                                Profit Margin
                            </p>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-percentage"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Outstanding Payments Alert -->
    @if($pendingRevenue > 0)
    <div class="row my-3">
        <div class="col-12">
            <div class="alert alert-warning alert-modern" role="alert">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-exclamation-triangle fs-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">
                            <i class="fas fa-bell me-1"></i>
                            Outstanding Payments
                        </h6>
                        <p class="mb-0">You have <strong>RM{{ number_format($pendingRevenue, 2) }}</strong> in pending payments that need attention.</p>
                    </div>
                    <div>
                        <a href="{{ route('sales.index', ['payment_status' => 'pending']) }}" class="btn btn-warning quick-action-btn">
                            <i class="fas fa-eye me-1"></i> View Pending Sales
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Business Streams -->
    <div class="row">
        <!-- Farm Production -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 business-stream-card">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #007bff, #0056b3);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1">
                                <i class="fas fa-tractor me-2"></i>
                                Farm Production
                            </h5>
                            <p class="mb-0 opacity-75 small">Your own agricultural produce</p>
                        </div>
                        <a href="{{ route('harvests.index') }}" class="btn btn-light btn-sm quick-action-btn">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Farm Yield -->
                        <div class="col-6">
                            <div class="metric-box text-center">
                                <div class="metric-box-icon text-primary">
                                    <i class="fas fa-weight"></i>
                                </div>
                                <div class="metric-box-value text-dark">{{ number_format($totalYield, 2) }}</div>
                                <p class="metric-box-label mb-0">kg Harvested</p>
                            </div>
                        </div>
                        
                        <!-- Farm Revenue -->
                        <div class="col-6">
                            <div class="metric-box text-center">
                                <div class="metric-box-icon text-success">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="metric-box-value text-success">{{ number_format($farmRevenue, 2) }}</div>
                                <p class="metric-box-label mb-0">RM Revenue</p>
                                @if($pendingRevenue > 0)
                                    <div class="mt-2">
                                        <span class="badge badge-modern bg-warning text-dark">
                                            <i class="fas fa-clock"></i> +{{ number_format($pendingRevenue, 2) }} pending
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Farm Costs -->
                        <div class="col-6">
                            <div class="metric-box text-center">
                                <div class="metric-box-icon text-danger">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <div class="metric-box-value text-danger">{{ number_format($totalCosts, 2) }}</div>
                                <p class="metric-box-label mb-0">RM Costs</p>
                            </div>
                        </div>
                        
                        <!-- Farm Profit -->
                        <div class="col-6">
                            <div class="metric-box text-center">
                                <div class="metric-box-icon text-info">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="metric-box-value text-info">{{ number_format($farmProfit, 2) }}</div>
                                <p class="metric-box-label mb-0">RM Profit</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="border-top pt-3 mt-4">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('harvests.create') }}" class="btn btn-primary btn-sm quick-action-btn">
                                <i class="fas fa-plus-circle me-1"></i> New Harvest
                            </a>
                            <a href="{{ route('sales.create') }}" class="btn btn-success btn-sm quick-action-btn">
                                <i class="fas fa-cash-register me-1"></i> Record Sale
                            </a>
                            <a href="{{ route('costs.create') }}" class="btn btn-outline-danger btn-sm quick-action-btn">
                                <i class="fas fa-receipt me-1"></i> Add Cost
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resell Business -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 business-stream-card">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #28a745, #20c997);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1">
                                <i class="fas fa-store me-2"></i>
                                Resell Business
                            </h5>
                            <p class="mb-0 opacity-75 small">Trading purchased chilies</p>
                        </div>
                        <a href="{{ route('resells.index') }}" class="btn btn-light btn-sm quick-action-btn">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Resell Inventory -->
                        <div class="col-6">
                            <div class="metric-box text-center">
                                <div class="metric-box-icon text-primary">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <div class="metric-box-value text-dark">{{ number_format($resellYield, 2) }}</div>
                                <p class="metric-box-label mb-0">kg Purchased</p>
                            </div>
                        </div>
                        
                        <!-- Resell Revenue -->
                        <div class="col-6">
                            <div class="metric-box text-center">
                                <div class="metric-box-icon text-success">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="metric-box-value text-success">{{ number_format($resellRevenue, 2) }}</div>
                                <p class="metric-box-label mb-0">RM Revenue</p>
                            </div>
                        </div>
                        
                        <!-- Purchase Costs -->
                        <div class="col-6">
                            <div class="metric-box text-center">
                                <div class="metric-box-icon text-danger">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="metric-box-value text-danger">{{ number_format($resellPurchaseCosts, 2) }}</div>
                                <p class="metric-box-label mb-0">RM Purchase Cost</p>
                            </div>
                        </div>
                        
                        <!-- Resell Profit -->
                        <div class="col-6">
                            <div class="metric-box text-center">
                                <div class="metric-box-icon text-info">
                                    <i class="fas fa-arrow-circle-up"></i>
                                </div>
                                <div class="metric-box-value text-info">{{ number_format($resellProfit, 2) }}</div>
                                <p class="metric-box-label mb-0">RM Profit</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="border-top pt-3 mt-4">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('resells.create') }}" class="btn btn-success btn-sm quick-action-btn">
                                <i class="fas fa-plus-circle me-1"></i> New Purchase
                            </a>
                            <a href="{{ route('resells.index') }}" class="btn btn-primary btn-sm quick-action-btn">
                                <i class="fas fa-eye me-1"></i> View Inventory
                            </a>
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-info btn-sm quick-action-btn">
                                <i class="fas fa-users me-1"></i> Customers
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Trends Section -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card border-0 chart-card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="chart-card-title mb-0">
                                <i class="fas fa-chart-area text-primary me-2"></i>Monthly Trends
                            </h5>
                            <p class="text-muted mb-0 small mt-1">
                                <i class="fas fa-info-circle me-1"></i>
                                6-month performance overview
                            </p>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" id="trendsPeriodBtn">
                                    <i class="fas fa-calendar-alt me-1"></i><span id="trendsPeriodLabel">Last 6 Months</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item trends-period-filter" href="#" data-period="3"><i class="fas fa-calendar me-2"></i>Last 3 Months</a></li>
                                    <li><a class="dropdown-item trends-period-filter active" href="#" data-period="6"><i class="fas fa-calendar-check me-2"></i>Last 6 Months</a></li>
                                    <li><a class="dropdown-item trends-period-filter" href="#" data-period="12"><i class="fas fa-calendar-week me-2"></i>Last 12 Months</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-12">
                            <div style="height: 400px; position: relative;">
                                <canvas id="monthlyTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Trend Indicators -->
                    <div class="row mt-4 g-3">
                        <div class="col-md-3 col-6">
                            <div class="trend-indicator text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div class="rounded-circle p-2 me-2" style="width: 12px; height: 12px; background-color: rgb(251, 191, 36) !important;"></div>
                                    <span class="fw-bold small" style="color: rgb(251, 191, 36);">Yield</span>
                                </div>
                                <small class="d-block">Last 6 months average</small>
                                <h6 class="mt-2">{{ number_format($monthlyYield->avg('value'), 0) }} kg</h6>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="trend-indicator text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div class="rounded-circle p-2 me-2" style="width: 12px; height: 12px; background-color: rgb(34, 197, 94) !important;"></div>
                                    <span class="fw-bold small" style="color: rgb(34, 197, 94);">Revenue</span>
                                </div>
                                <small class="d-block">Last 6 months average</small>
                                <h6 class="mt-2">RM{{ number_format($monthlyRevenue->avg('value'), 0) }}</h6>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="trend-indicator text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div class="rounded-circle p-2 me-2" style="width: 12px; height: 12px; background-color: rgb(239, 68, 68) !important;"></div>
                                    <span class="fw-bold small" style="color: rgb(239, 68, 68);">Costs</span>
                                </div>
                                <small class="d-block">Last 6 months average</small>
                                <h6 class="mt-2">RM{{ number_format($monthlyCosts->avg('value'), 0) }}</h6>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="trend-indicator text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div class="rounded-circle p-2 me-2" style="width: 12px; height: 12px; background-color: rgb(59, 130, 246) !important;"></div>
                                    <span class="fw-bold small" style="color: rgb(59, 130, 246);">Net Profit</span>
                                </div>
                                <small class="d-block">Last 6 months average</small>
                                @php
                                    $avgProfit = $monthlyRevenue->avg('value') - $monthlyCosts->avg('value');
                                @endphp
                                <h6 class="mt-2">RM{{ number_format($avgProfit, 0) }}</h6>
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
            <div class="card border-0 chart-card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs analytics-tabs mb-4" id="analyticsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold text-primary border-0 border-bottom border-primary" 
                                    id="yield-tab" data-bs-toggle="tab" data-bs-target="#yield" type="button" 
                                    role="tab" aria-controls="yield" aria-selected="true">
                                <i class="fas fa-chart-bar me-2"></i>Yield Analytics
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-muted border-0" 
                                    id="cost-tab" data-bs-toggle="tab" data-bs-target="#cost" type="button" 
                                    role="tab" aria-controls="cost" aria-selected="false">
                                <i class="fas fa-wallet me-2"></i>Cost Analytics
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-muted border-0" 
                                    id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" type="button" 
                                    role="tab" aria-controls="customer" aria-selected="false">
                                <i class="fas fa-users me-2"></i>Customer Analytics
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-muted border-0" 
                                    id="trends-tab" data-bs-toggle="tab" data-bs-target="#trends" type="button" 
                                    role="tab" aria-controls="trends" aria-selected="false">
                                <i class="fas fa-chart-line me-2"></i>Market Trends
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content" id="analyticsTabContent">
                        <!-- Yield Analytics Tab -->
                        <div class="tab-pane fade show active" id="yield" role="tabpanel" aria-labelledby="yield-tab">
                            <!-- Monthly Yield Trends - Highlighted -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card border-0 analytics-card">
                                        <div class="card-header text-white" style="background: linear-gradient(135deg, #28a745, #20c997);">
                                            <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Monthly Yield Trends</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="monthlyYieldChart" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Secondary Analytics - 3 Columns -->
                            <div class="row">
                                <!-- Variety Distribution -->
                                <div class="col-lg-4 mb-3">
                                    <div class="card border-0 analytics-card h-100">
                                        <div class="card-header text-white" style="background: linear-gradient(135deg, #17a2b8, #20c997);">
                                            <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Variety Distribution</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="varietyChart" height="260"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Harvests -->
                                <div class="col-lg-4 mb-3">
                                    <div class="card border-0 analytics-card h-100">
                                        <div class="card-header text-white" style="background: linear-gradient(135deg, #007bff, #0056b3);">
                                            <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Harvests</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm analytics-table mb-0">
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
                                                                <span class="badge badge-modern bg-{{ $harvest->quality_grade == 'Premium' ? 'success' : ($harvest->quality_grade == 'Grade A' ? 'primary' : 'secondary') }}">
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
                                <div class="col-lg-4 mb-3">
                                    <div class="card border-0 analytics-card h-100">
                                        <div class="card-header text-white" style="background: linear-gradient(135deg, #ffc107, #ff9800);">
                                            <h6 class="mb-0"><i class="fas fa-calendar-day me-2"></i>Harvest Trends</h6>
                                        </div>
                                        <div class="card-body">
                                            @if($yieldAnalytics['dailyHarvestTrends']->count() > 0)
                                                <canvas id="weeklyYieldChart" height="260"></canvas>
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="fas fa-calendar-times text-muted" style="font-size: 2rem;"></i>
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
                                            <h6 class="mb-0"><i class="fas fa-chart-area me-2"></i>Monthly Cost vs Revenue</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="costRevenueChart" height="250"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cost Breakdown -->
                                <div class="col-lg-4 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-secondary text-white">
                                            <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Cost Categories</h6>
                                        </div>
                                        <div class="card-body">
                                            <div style="height: 365px; position: relative;">
                                                <canvas id="costBreakdownChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Expenses -->
                                <div class="col-lg-12 mb-4">
                                    <div class="card border-0 analytics-card">
                                        <div class="card-header text-white" style="background: linear-gradient(135deg, #343a40, #495057);">
                                            <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Recent Expenses</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table analytics-table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 12%;">DATE</th>
                                                            <th style="width: 18%;">CATEGORY</th>
                                                            <th style="width: 50%;">DESCRIPTION</th>
                                                            <th style="width: 20%;" class="text-end">AMOUNT</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($recentCosts as $cost)
                                                        <tr>
                                                            <td class="fw-semibold text-muted">{{ $cost->date->format('M d') }}</td>
                                                            <td>
                                                                <span class="badge badge-modern bg-secondary">{{ ucfirst($cost->category) }}</span>
                                                            </td>
                                                            <td class="text-muted">{{ $cost->description }}</td>
                                                            <td class="text-end">
                                                                <span class="fw-bold text-danger">RM{{ number_format($cost->amount, 2) }}</span>
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

                        <!-- Customer Analytics Tab -->
                        <div class="tab-pane fade" id="customer" role="tabpanel" aria-labelledby="customer-tab">
                            <div class="row">
                                <!-- Customer Growth -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-user-plus me-2"></i>Customer Growth</h6>
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
                                            <h6 class="mb-0"><i class="fas fa-users me-2"></i>Customer Types</h6>
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
                                            <h6 class="mb-0"><i class="fas fa-trophy me-2"></i>Top Customers</h6>
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
                                            <h6 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Geographic Distribution</h6>
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
                                            <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Market Channel Distribution</h6>
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
                                            <h6 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Channel Performance</h6>
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
                                            <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Sales Activity</h6>
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
// Global chart instance for monthly trends
let monthlyTrendsChartInstance = null;

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
    type: 'bar',
    data: {
        labels: [
            @foreach($costAnalytics['categoryBreakdown'] as $category)
            '{{ ucfirst($category->category) }}',
            @endforeach
        ],
        datasets: [{
            label: 'Total Cost (RM)',
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
            ],
            borderColor: [
                'rgb(220, 38, 38)',
                'rgb(217, 119, 6)',
                'rgb(22, 163, 74)',
                'rgb(37, 99, 235)',
                'rgb(126, 34, 206)',
                'rgb(190, 24, 93)',
                'rgb(2, 132, 199)',
                'rgb(101, 51, 15)',
                'rgb(220, 38, 38)',
                'rgb(8, 145, 178)',
                'rgb(220, 38, 38)',
                'rgb(16, 185, 129)',
                'rgb(108, 92, 231)',
                'rgb(202, 138, 4)',
                'rgb(107, 114, 128)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const value = context.parsed.x;
                        return `Total Cost: RM${value.toLocaleString()}`;
                    }
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'RM' + value.toLocaleString();
                    }
                }
            },
            y: {
                ticks: {
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});

// Monthly Trends Chart
const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
monthlyTrendsChartInstance = new Chart(monthlyTrendsCtx, {
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

<script>
// Monthly Trends Period Filter
document.querySelectorAll('.trends-period-filter').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        
        const period = this.getAttribute('data-period');
        const periodLabels = {
            3: 'Last 3 Months',
            6: 'Last 6 Months',
            12: 'Last 12 Months'
        };
        
        // Update button label
        document.getElementById('trendsPeriodLabel').textContent = periodLabels[period];
        
        // Update active state
        document.querySelectorAll('.trends-period-filter').forEach(item => {
            item.classList.remove('active');
        });
        this.classList.add('active');
        
        // Fetch updated chart data via AJAX
        fetchMonthlyTrendsData(period);
    });
});

// Fetch Monthly Trends Data
function fetchMonthlyTrendsData(period) {
    const dateFrom = new URLSearchParams(window.location.search).get('date_from');
    const dateTo = new URLSearchParams(window.location.search).get('date_to');
    
    let url = `{{ route('dashboard.trends-data') }}?trends_period=${period}`;
    if (dateFrom) url += `&date_from=${dateFrom}`;
    if (dateTo) url += `&date_to=${dateTo}`;
    
    console.log('Fetching trends data from:', url);
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            updateMonthlyTrendsChart(data);
        })
        .catch(error => console.error('Error fetching data:', error));
}

// Update Monthly Trends Chart
function updateMonthlyTrendsChart(data) {
    console.log('Updating chart with data:', data);
    
    if (monthlyTrendsChartInstance) {
        // Update labels
        monthlyTrendsChartInstance.data.labels = data.labels;
        
        // Update each dataset
        if (monthlyTrendsChartInstance.data.datasets[0]) {
            monthlyTrendsChartInstance.data.datasets[0].data = data.yield;
        }
        if (monthlyTrendsChartInstance.data.datasets[1]) {
            monthlyTrendsChartInstance.data.datasets[1].data = data.revenue;
        }
        if (monthlyTrendsChartInstance.data.datasets[2]) {
            monthlyTrendsChartInstance.data.datasets[2].data = data.costs;
        }
        if (monthlyTrendsChartInstance.data.datasets[3]) {
            monthlyTrendsChartInstance.data.datasets[3].data = data.netProfit;
        }
        
        // Force chart update with animation
        monthlyTrendsChartInstance.update('active');
        console.log('Chart updated successfully');
        console.log('Chart instance:', monthlyTrendsChartInstance);
    } else {
        console.log('Chart instance not found');
    }
    
    // Update trend indicators
    updateTrendIndicators(data.period, data.averages);
}

// Update Trend Indicators
function updateTrendIndicators(period, averages) {
    const periodLabels = {
        3: 'Last 3 months average',
        6: 'Last 6 months average',
        12: 'Last 12 months average'
    };
    
    document.querySelectorAll('small.d-block').forEach((elem, index) => {
        if (elem.textContent.includes('Last')) {
            elem.textContent = periodLabels[period];
        }
    });
    
    // Update values
    const indicators = document.querySelectorAll('.trend-indicator h6');
    if (indicators[0]) indicators[0].textContent = averages.yield + ' kg';
    if (indicators[1]) indicators[1].textContent = 'RM' + averages.revenue;
    if (indicators[2]) indicators[2].textContent = 'RM' + averages.costs;
    if (indicators[3]) indicators[3].textContent = 'RM' + averages.netProfit;
}
</script>
@endsection
