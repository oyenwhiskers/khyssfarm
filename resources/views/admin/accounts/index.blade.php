@extends('layouts.app')

@section('page-title', 'Account Management')

@section('content')
<style>
    /* Account Table Styling */
    .accounts-table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .accounts-table thead th {
        border: none;
        padding: 1rem 0.75rem !important;
        font-weight: 700;
        color: #495057;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .accounts-table tbody tr {
        border-bottom: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }
    
    .accounts-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .accounts-table tbody td {
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
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-active {
        background: #d4edda;
        color: #155724;
    }
    
    .status-inactive {
        background: #f8d7da;
        color: #721c24;
    }
    
    /* Role Badge Styling */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .role-admin {
        background: #f8d7da;
        color: #721c24;
    }
    
    .role-user {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    /* User Info Display */
    .user-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .user-name {
        font-weight: 700;
        color: #212529;
    }
    
    .user-email {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    /* Date Display */
    .date-display {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .date-main {
        font-weight: 600;
        color: #495057;
    }
    
    .date-secondary {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    /* Action Buttons */
    .account-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .btn-action-small {
        padding: 0.35rem 0.65rem;
        font-size: 0.8rem;
        white-space: nowrap;
    }

    /* Summary Cards */
    .summary-card {
        border-radius: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .summary-card .card-body {
        padding: 1.5rem;
    }
    
    .summary-value {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }
    
    .summary-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        opacity: 0.85;
    }
</style>

<div class="row mb-4">
    <div class="col-lg-8">
        <h2><i class="fas fa-users-cog me-2"></i>Account Management</h2>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-lg-3 mb-3">
        <div class="card text-center text-white shadow-sm summary-card" style="background: linear-gradient(135deg, #667eea, #764ba2);">
            <div class="card-body">
                <i class="fas fa-user-check fa-2x mb-3"></i>
                <div class="summary-value">{{ $users->total() }}</div>
                <div class="summary-label">Total Accounts</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-3">
        <div class="card text-center text-white shadow-sm summary-card" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
            <div class="card-body">
                <i class="fas fa-hourglass-half fa-2x mb-3"></i>
                <div class="summary-value">{{ \App\Models\User::where('status', 'pending')->count() }}</div>
                <div class="summary-label">Pending Review</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-3">
        <div class="card text-center text-white shadow-sm summary-card" style="background: linear-gradient(135deg, #27ae60, #229954);">
            <div class="card-body">
                <i class="fas fa-user-check fa-2x mb-3"></i>
                <div class="summary-value">{{ \App\Models\User::where('status', 'active')->count() }}</div>
                <div class="summary-label">Active Users</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-3">
        <div class="card text-center text-white shadow-sm summary-card" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
            <div class="card-body">
                <i class="fas fa-crown fa-2x mb-3"></i>
                <div class="summary-value">{{ \App\Models\User::where('role', 'admin')->count() }}</div>
                <div class="summary-label">Administrators</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter Accounts</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.accounts.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Name or email" value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
                <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary" title="Clear filters">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Accounts Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">
            <i class="fas fa-list me-2 text-primary"></i>All Accounts
        </h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover accounts-table mb-0">
            <thead>
                <tr>
                    <th style="width: 25%;">User Information</th>
                    <th style="width: 15%;">Role</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 20%;">Registered</th>
                    <th style="width: 25%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="user-info">
                                <span class="user-name">{{ $user->name }}</span>
                                <span class="user-email">{{ $user->email }}</span>
                            </div>
                        </td>
                        <td>
                            @if($user->isAdmin())
                                <span class="role-badge role-admin">
                                    <i class="fas fa-crown"></i>Admin
                                </span>
                            @else
                                <span class="role-badge role-user">
                                    <i class="fas fa-user"></i>User
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->isPending())
                                <span class="status-badge status-pending">
                                    <i class="fas fa-hourglass-half"></i>Pending
                                </span>
                            @elseif($user->isActive())
                                <span class="status-badge status-active">
                                    <i class="fas fa-check-circle"></i>Active
                                </span>
                            @else
                                <span class="status-badge status-inactive">
                                    <i class="fas fa-ban"></i>Inactive
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="date-display">
                                <span class="date-main">{{ $user->created_at->format('M d, Y') }}</span>
                                <span class="date-secondary">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="account-actions">
                                <a href="{{ route('admin.accounts.show', $user) }}" class="btn btn-sm btn-outline-primary btn-action-small" title="View Details">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                            <strong>No accounts found</strong>
                            <p class="mb-0 small">Try adjusting your filters</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing {{ $users->count() }} of {{ $users->total() }} accounts
            </small>
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
