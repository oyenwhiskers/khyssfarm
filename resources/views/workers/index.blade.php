@extends('layouts.app')

@section('title', 'Workers')
@section('page-title', 'Workers')

@section('content')
<style>
    .worker-table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .worker-table thead th {
        border: none;
        padding: 0.9rem 0.75rem !important;
        font-weight: 700;
        color: #495057;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .worker-table tbody tr {
        border-bottom: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }

    .worker-table tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .worker-table tbody td {
        padding: 0.85rem 0.75rem !important;
        vertical-align: middle;
    }

    .worker-name {
        font-weight: 600;
        color: #212529;
        font-size: 0.95rem;
    }

    .worker-role {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: #e7f3ff;
        color: #0066cc;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .filter-card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border-radius: 10px;
    }

    .main-card {
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border-radius: 10px;
        overflow: hidden;
    }

    .stats-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }
</style>

<div class="row mb-4">
    <div class="col-lg-8">
        <h2><i class="fas fa-users-cog me-2"></i>Worker Management</h2>
        <p class="text-muted mb-0">Manage your farm workers and track their performance</p>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('workers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Worker
        </a>
    </div>
</div>

<div class="card filter-card mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter Workers</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('workers.index') }}" class="row g-3">
            <div class="col-md-5">
                <label for="search" class="form-label fw-semibold">
                    <i class="fas fa-search me-1"></i>Search
                </label>
                <input type="text" class="form-select" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name, contact, or notes...">
            </div>
            <div class="col-md-3">
                <label for="role" class="form-label fw-semibold">Role</label>
                <select class="form-select" id="role" name="role">
                    <option value="">All Roles</option>
                    @foreach($roles as $key => $label)
                        <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label fw-semibold">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 me-2">
                    <i class="fas fa-search me-1"></i>Search
                </button>
            </div>
        </form>
        @if(request()->hasAny(['search', 'role', 'status']))
            <div class="mt-3">
                <a href="{{ route('workers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times me-1"></i>Clear All Filters
                </a>
                @if(request('search'))
                    <span class="badge bg-primary ms-2">
                        Search: "{{ request('search') }}"
                    </span>
                @endif
                @if(request('role'))
                    <span class="badge bg-info ms-2">
                        Role: {{ $roles[request('role')] ?? request('role') }}
                    </span>
                @endif
                @if(request('status'))
                    <span class="badge bg-success ms-2">
                        Status: {{ $statuses[request('status')] ?? request('status') }}
                    </span>
                @endif
            </div>
        @endif
    </div>
</div>

<div class="card main-card">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-users me-2 text-primary"></i>All Workers
            </h5>
            <span class="stats-badge">{{ $workers->total() }} Total</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table worker-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th class="text-end" style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workers as $worker)
                        <tr>
                            <td>
                                <div class="worker-name">{{ $worker->name }}</div>
                            </td>
                            <td>
                                <span class="worker-role">
                                    {{ $roles[$worker->role] ?? ucfirst(str_replace('_', ' ', $worker->role)) }}
                                </span>
                            </td>
                            <td>
                                @if($worker->contact)
                                    <i class="fas fa-phone text-muted me-1"></i>{{ $worker->contact }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($worker->status === 'active')
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2">
                                        <i class="fas fa-minus-circle me-1"></i>Inactive
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 220px;" title="{{ $worker->notes }}">
                                    {{ $worker->notes ?: '—' }}
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('workers.show', $worker) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('workers.edit', $worker) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('workers.destroy', $worker) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this worker?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">No workers found.</p>
                                    <small>Try adjusting your filters or create a new worker.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($workers->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $workers->links() }}
        </div>
    @endif
</div>
@endsection
