@extends('layouts.app')

@section('page-title', 'Account Details - ' . $user->name)

@section('content')
<style>
    /* Hero Header Section */
    .account-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px 8px 0 0;
        padding: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .account-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }
    
    .account-hero-content {
        position: relative;
        z-index: 1;
    }
    
    .account-name {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }
    
    .account-email {
        font-size: 1rem;
        opacity: 0.9;
        margin-bottom: 1rem;
    }
    
    .account-badges {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    
    .account-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        backdrop-filter: blur(10px);
    }
    
    /* Info Section Styling */
    .info-section {
        background: white;
        border-radius: 8px;
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-top: 3px solid;
    }
    
    .info-section.info-primary {
        border-top-color: #667eea;
    }
    
    .info-section.info-success {
        border-top-color: #27ae60;
    }
    
    .info-section.info-info {
        border-top-color: #17a2b8;
    }
    
    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .section-title i {
        color: #667eea;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        font-weight: 600;
    }
    
    .info-value {
        font-size: 0.95rem;
        color: #212529;
        font-weight: 600;
        text-align: right;
    }
    
    /* Action Button Group */
    .action-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    
    .action-group.full {
        grid-template-columns: 1fr;
    }
    
    .action-btn {
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        border-radius: 6px;
        border: none;
        transition: all 0.2s ease;
        white-space: nowrap;
        font-size: 0.95rem;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .action-btn:active {
        transform: translateY(0);
    }
    
    .divider {
        height: 1px;
        background: #e9ecef;
        margin: 1rem 0;
    }
    
    /* Activity Log Table */
    .activity-table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .activity-table thead th {
        border: none;
        padding: 1rem 0.75rem !important;
        font-weight: 700;
        color: #495057;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .activity-table tbody tr {
        border-bottom: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }
    
    .activity-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .activity-table tbody td {
        padding: 1rem 0.75rem !important;
        vertical-align: middle;
    }
    
    /* Event Badge Styling */
    .event-badge {
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

    /* Responsive Grid */
    @media (max-width: 768px) {
        .action-group {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Header Section -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 8px; overflow: hidden;">
    <div class="account-hero">
        <div class="account-hero-content">
            <div class="account-name">{{ $user->name }}</div>
            <div class="account-email">{{ $user->email }}</div>
            <div class="account-badges">
                @if($user->isAdmin())
                    <span class="account-badge">
                        <i class="fas fa-crown"></i>Administrator
                    </span>
                @else
                    <span class="account-badge">
                        <i class="fas fa-user"></i>Regular User
                    </span>
                @endif
                
                @if($user->isPending())
                    <span class="account-badge">
                        <i class="fas fa-hourglass-half"></i>Pending Review
                    </span>
                @elseif($user->isActive())
                    <span class="account-badge">
                        <i class="fas fa-check-circle"></i>Active Account
                    </span>
                @else
                    <span class="account-badge">
                        <i class="fas fa-ban"></i>Inactive
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; border-top: 1px solid #e9ecef;">
            <div>
                <small class="text-muted d-block mb-2">Account Created</small>
                <strong>{{ $user->created_at->format('M d, Y') }}</strong>
            </div>
            <div style="text-align: center;">
                <small class="text-muted d-block mb-2">Days Active</small>
                <strong>{{ $user->created_at->diffInDays(now()) }} days</strong>
            </div>
            <div style="text-align: right;">
                <small class="text-muted d-block mb-2">Total Events</small>
                <strong>{{ $activityLogs->count() }}</strong>
            </div>
        </div>
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

<div class="row">
    <!-- Account Information & Actions -->
    <div class="col-lg-5">
        <!-- Account Details -->
        <div class="info-section info-primary">
            <h6 class="section-title">
                <i class="fas fa-id-card"></i>Account Details
            </h6>
            <div class="info-item">
                <span class="info-label">Status</span>
                <span>
                    @if($user->isPending())
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-hourglass-half me-1"></i>Pending
                        </span>
                    @elseif($user->isActive())
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>Active
                        </span>
                    @else
                        <span class="badge bg-danger">
                            <i class="fas fa-ban me-1"></i>Inactive
                        </span>
                    @endif
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Role</span>
                <span>
                    @if($user->isAdmin())
                        <span class="badge bg-danger">
                            <i class="fas fa-crown me-1"></i>Admin
                        </span>
                    @else
                        <span class="badge bg-secondary">
                            <i class="fas fa-user me-1"></i>User
                        </span>
                    @endif
                </span>
            </div>
            @if($user->approved_at)
                <div class="info-item">
                    <span class="info-label">Approved Date</span>
                    <span class="info-value">{{ $user->approved_at->format('M d, Y') }}</span>
                </div>
            @endif
        </div>

        <!-- Actions Section -->
        <div class="info-section info-success">
            <h6 class="section-title">
                <i class="fas fa-cog"></i>Account Actions
            </h6>
            
            @if($user->isPending())
                <form method="POST" action="{{ route('admin.accounts.approve', $user) }}" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 action-btn">
                        <i class="fas fa-check-circle me-2"></i>Approve Account
                    </button>
                </form>
                
                <button type="button" class="btn btn-danger w-100 action-btn" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fas fa-times-circle me-2"></i>Reject Account
                </button>
            @endif

            @if($user->isActive())
                <form method="POST" action="{{ route('admin.accounts.deactivate', $user) }}" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-warning w-100 action-btn text-dark">
                        <i class="fas fa-ban me-2"></i>Deactivate Account
                    </button>
                </form>
            @elseif($user->isInactive())
                <form method="POST" action="{{ route('admin.accounts.reactivate', $user) }}" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 action-btn">
                        <i class="fas fa-check-circle me-2"></i>Reactivate Account
                    </button>
                </form>
            @endif

            <div class="divider"></div>

            @if(!$user->isAdmin())
                <form method="POST" action="{{ route('admin.accounts.promote', $user) }}">
                    @csrf
                    <button type="submit" class="btn btn-info w-100 action-btn text-white">
                        <i class="fas fa-crown me-2"></i>Promote to Admin
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.accounts.demote', $user) }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary w-100 action-btn">
                        <i class="fas fa-user me-2"></i>Demote to User
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Activity Summary -->
    <div class="col-lg-7">
        <div class="info-section info-info">
            <h6 class="section-title">
                <i class="fas fa-activity"></i>Activity Summary
            </h6>
            
            @php
                $totalLogins = $activityLogs->where('event_type', 'login')->count();
                $lastLogin = $activityLogs->where('event_type', 'login')->first();
                $loginAttempts = $activityLogs->where('event_type', 'failed_login')->count();
            @endphp

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div style="text-align: center; padding-bottom: 1rem; border-bottom: 1px solid #f0f0f0;">
                    <div style="font-size: 1.75rem; font-weight: 700; color: #667eea; margin-bottom: 0.5rem;">{{ $totalLogins }}</div>
                    <small class="text-muted" style="text-transform: uppercase; font-weight: 600;">Total Logins</small>
                </div>
                <div style="text-align: center; padding-bottom: 1rem; border-bottom: 1px solid #f0f0f0;">
                    <div style="font-size: 1.75rem; font-weight: 700; color: #e74c3c; margin-bottom: 0.5rem;">{{ $loginAttempts }}</div>
                    <small class="text-muted" style="text-transform: uppercase; font-weight: 600;">Failed Attempts</small>
                </div>
                <div style="text-align: center; padding-bottom: 1rem; border-bottom: 1px solid #f0f0f0;">
                    <div style="font-size: 1.75rem; font-weight: 700; color: #27ae60; margin-bottom: 0.5rem;">{{ $activityLogs->count() }}</div>
                    <small class="text-muted" style="text-transform: uppercase; font-weight: 600;">Total Events</small>
                </div>
            </div>

            <div class="info-item" style="border-bottom: none;">
                <span class="info-label">Last Login</span>
                <span class="info-value">
                    @if($lastLogin)
                        <span title="{{ $lastLogin->created_at->format('F d, Y H:i') }}">
                            {{ $lastLogin->created_at->diffForHumans() }}
                        </span>
                    @else
                        <span class="text-muted">Never logged in</span>
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Activity Logs Section -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0">
            <i class="fas fa-history me-2 text-primary"></i>Recent Activity Log (Last 20 Events)
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover activity-table mb-0">
            <thead>
                <tr>
                    <th style="width: 18%;">Event Type</th>
                    <th style="width: 18%;">IP Address</th>
                    <th style="width: 40%;">Description</th>
                    <th style="width: 24%;">Date/Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activityLogs as $log)
                    <tr>
                        <td>
                            <span class="event-badge bg-{{ $log->getEventBadgeColor() }}">
                                {{ $log->getFormattedEventType() }}
                            </span>
                        </td>
                        <td>
                            <code style="font-size: 0.85rem; background: #f8f9fa; padding: 0.3rem 0.6rem; border-radius: 4px;">{{ $log->ip_address ?? 'N/A' }}</code>
                        </td>
                        <td>
                            <small>{{ $log->description }}</small>
                        </td>
                        <td>
                            <small class="text-muted d-block">{{ $log->created_at->format('M d, Y H:i') }}</small>
                            <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                            <strong>No activity logs found</strong>
                            <p class="mb-0 small mt-2">This account hasn't been accessed yet</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <form method="POST" action="{{ route('admin.accounts.reject', $user) }}">
                @csrf
                <div class="modal-header bg-danger text-white border-0" style="padding: 1.5rem;">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Reject Account
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 1.5rem;">
                    <p class="text-muted mb-3">Please provide a detailed reason for rejecting this account. The user will be notified of your decision.</p>
                    <div class="mb-0">
                        <label for="reason" class="form-label fw-bold mb-2">Rejection Reason</label>
                        <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror" 
                                  rows="5" required placeholder="Example: Account violates our terms of service..."></textarea>
                        @error('reason')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-top" style="padding: 1rem 1.5rem;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        <i class="fas fa-times-circle me-2"></i>Reject Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
