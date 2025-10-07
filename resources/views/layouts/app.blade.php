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
            background: linear-gradient(135deg, #28a745, #20c997);
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.85);
            padding: 12px 15px;
            border-radius: 8px;
            margin: 4px 8px;
            transition: all 0.3s ease;
            white-space: nowrap;
            font-size: 14px;
            line-height: 1.3;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateX(5px);
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
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h5 class="text-white mb-3">
                        <i class="fas fa-pepper-hot me-2"></i>
                        KHYSS Farm
                    </h5>
                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a class="nav-link {{ request()->routeIs('harvests.*') ? 'active' : '' }}" href="{{ route('harvests.index') }}">
                            <i class="fas fa-seedling me-2"></i> Harvest Records
                        </a>
                        <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                            <i class="fas fa-shopping-cart me-2"></i> Sales Tracking
                        </a>
                        <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                            <i class="fas fa-users me-2"></i> Customers
                        </a>
                        <a class="nav-link {{ request()->routeIs('costs.*') ? 'active' : '' }}" href="{{ route('costs.index') }}">
                            <i class="fas fa-coins me-2"></i> Cost Management
                        </a>
                        <a class="nav-link {{ request()->routeIs('prices.*') ? 'active' : '' }}" href="{{ route('prices.index') }}">
                            <i class="fas fa-tags me-2"></i> Pricing
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
