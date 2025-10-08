@extends('layouts.app')

@section('title', 'Marketing Campaigns')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Marketing Campaigns</h1>
        <a href="{{ route('marketing.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> New Campaign
        </a>
    </div>

    <!-- Campaign Performance Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Total Campaigns</h5>
                            <h3 class="mb-0">{{ $campaigns->count() }}</h3>
                        </div>
                        <i class="fas fa-bullhorn fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Total Budget</h5>
                            <h3 class="mb-0">RM {{ number_format($totalBudget, 2) }}</h3>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Total Leads</h5>
                            <h3 class="mb-0">{{ $totalLeads }}</h3>
                        </div>
                        <i class="fas fa-user-friends fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Avg. Cost/Lead</h5>
                            <h3 class="mb-0">RM {{ number_format($avgCostPerLead, 2) }}</h3>
                        </div>
                        <i class="fas fa-chart-line fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('marketing.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="campaign_type" class="form-label">Campaign Type</label>
                    <select name="campaign_type" id="campaign_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="lead_generation" {{ request('campaign_type') == 'lead_generation' ? 'selected' : '' }}>Lead Generation</option>
                        <option value="brand_awareness" {{ request('campaign_type') == 'brand_awareness' ? 'selected' : '' }}>Brand Awareness</option>
                        <option value="sales_conversion" {{ request('campaign_type') == 'sales_conversion' ? 'selected' : '' }}>Sales Conversion</option>
                        <option value="customer_retention" {{ request('campaign_type') == 'customer_retention' ? 'selected' : '' }}>Customer Retention</option>
                        <option value="product_launch" {{ request('campaign_type') == 'product_launch' ? 'selected' : '' }}>Product Launch</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-filter"></i>
                        </button>
                        <a href="{{ route('marketing.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="card">
        <div class="card-body">
            @if($campaigns->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Campaign Name</th>
                                <th>Type</th>
                                <th>Channel</th>
                                <th>Budget</th>
                                <th>Leads</th>
                                <th>Cost/Lead</th>
                                <th>ROI</th>
                                <th>Status</th>
                                <th>Period</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campaigns as $campaign)
                                <tr>
                                    <td>
                                        <strong>{{ $campaign->campaign_name }}</strong>
                                        @if($campaign->description)
                                            <br><small class="text-muted">{{ Str::limit($campaign->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ ucwords(str_replace('_', ' ', $campaign->campaign_type)) }}
                                        </span>
                                    </td>
                                    <td>{{ ucwords(str_replace('_', ' ', $campaign->marketing_channel)) }}</td>
                                    <td>RM {{ number_format($campaign->budget_spent, 2) }}</td>
                                    <td>{{ number_format($campaign->leads_generated) }}</td>
                                    <td>
                                        @if($campaign->cost_per_lead)
                                            RM {{ number_format($campaign->cost_per_lead, 2) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($campaign->roi)
                                            <span class="badge bg-{{ $campaign->roi > 0 ? 'success' : 'danger' }}">
                                                {{ number_format($campaign->roi, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $campaign->status == 'active' ? 'success' : ($campaign->status == 'completed' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($campaign->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            {{ $campaign->start_date->format('M d') }} - 
                                            {{ $campaign->end_date ? $campaign->end_date->format('M d, Y') : 'Ongoing' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('marketing.show', $campaign) }}" class="btn btn-outline-info btn-sm rounded-pill" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('marketing.edit', $campaign) }}" class="btn btn-outline-primary btn-sm rounded-pill" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('marketing.destroy', $campaign) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Delete" 
                                                        onclick="return confirm('Are you sure you want to delete this campaign?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No marketing campaigns found</h5>
                    <p class="text-muted">Start tracking your marketing efforts by creating your first campaign.</p>
                    <a href="{{ route('marketing.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Create Campaign
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection