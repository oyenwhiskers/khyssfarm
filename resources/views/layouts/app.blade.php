<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KHYSS Chili Farm Management')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1e7e34 0%, #28a745 50%, #20c997 100%);
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
        }
        
        .sidebar::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        
        .sidebar-header {
            position: relative;
            padding: 1.5rem;
            background: rgba(0,0,0,0.15);
            border-bottom: 2px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
        }
        
        .sidebar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .sidebar-brand i {
            font-size: 1.75rem;
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.2));
        }
        
        .sidebar .nav {
            padding: 0 0.75rem;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 14px 18px;
            border-radius: 12px;
            margin: 6px 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            font-size: 15px;
            font-weight: 500;
            line-height: 1.4;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 0;
            background: white;
            border-radius: 0 4px 4px 0;
            transition: height 0.3s ease;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 16px;
            transition: transform 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.25);
            color: white;
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .sidebar .nav-link:hover::before {
            height: 60%;
        }
        
        .sidebar .nav-link:hover i {
            transform: scale(1.15);
        }
        
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.3);
            color: white;
            transform: translateX(8px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
            font-weight: 600;
        }
        
        .sidebar .nav-link.active::before {
            height: 70%;
        }
        
        .sidebar .nav-link.active i {
            transform: scale(1.2);
        }
        
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            background: rgba(0,0,0,0.1);
            border-top: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar-footer small {
            color: rgba(255,255,255,0.7);
            font-size: 0.75rem;
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .navbar-brand {
            font-weight: bold;
            color: #28a745 !important;
        }
        .metric-card {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 15px;
        }
        .metric-card.revenue {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        .metric-card.cost {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }
        .metric-card.profit {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }
        .btn-primary {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #20c997, #28a745);
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
        
        /* Enhanced button hover effects for all button types */
        .btn:not(.nav-link):hover {
            transition: all 0.3s ease;
        }
        
        /* Table Styles */
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #dee2e6;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table td {
            border-top: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }
        
        .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        /* Pagination Styles */
        .pagination {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .pagination .page-link {
            border: none;
            padding: 12px 16px;
            color: #495057;
            background-color: #fff;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            border-radius: 0;
        }
        
        .pagination .page-link:hover {
            background-color: #28a745;
            color: white;
            transform: translateY(-2px);
        }
        
        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }
        
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #f8f9fa;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="sidebar-header">
                    <div class="sidebar-brand">
                        <i class="fas fa-pepper-hot"></i>
                        <span>KHYSS Farm</span>
                    </div>
                </div>
                <div class="sidebar-content">
                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('harvests.*') ? 'active' : '' }}" href="{{ route('harvests.index') }}">
                            <i class="fas fa-seedling"></i>
                            <span>Harvest Records</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Sales Tracking</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                            <i class="fas fa-users"></i>
                            <span>Customers</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('costs.*') ? 'active' : '' }}" href="{{ route('costs.index') }}">
                            <i class="fas fa-coins"></i>
                            <span>Cost Management</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('prices.*') ? 'active' : '' }}" href="{{ route('prices.index') }}">
                            <i class="fas fa-tags"></i>
                            <span>Pricing</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('marketing.*') ? 'active' : '' }}" href="{{ route('marketing.index') }}">
                            <i class="fas fa-bullhorn"></i>
                            <span>Marketing</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('resells.*') ? 'active' : '' }}" href="{{ route('resells.index') }}">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Resell Tracking</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 main-content">
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-brand">@yield('page-title', 'Dashboard')</span>
                        <div class="navbar-nav ms-auto">
                            <span class="nav-item nav-link">
                                <i class="fas fa-calendar me-1"></i>
                                {{ now()->format('F d, Y') }}
                            </span>
                        </div>
                    </div>
                </nav>

                <div class="container-fluid py-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('scripts')
</body>
</html>
