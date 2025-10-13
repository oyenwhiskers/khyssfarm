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
        <div class="col-md-3 mb-3">
            <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #007bff, #0056b3);">
                <div class="card-body py-4">
                    <i class="fas fa-bullhorn fa-2x mb-3"></i>
                    <h3 class="mb-2">{{ $campaigns->count() }}</h3>
                    <p class="mb-1 fw-bold">Total Campaigns</p>
                    <small class="opacity-75">Marketing Efforts</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #28a745, #20c997);">
                <div class="card-body py-4">
                    <i class="fas fa-dollar-sign fa-2x mb-3"></i>
                    <h3 class="mb-2">RM {{ number_format($totalBudget, 2) }}</h3>
                    <p class="mb-1 fw-bold">Total Budget</p>
                    <small class="opacity-75">Marketing Spend</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #17a2b8, #138496);">
                <div class="card-body py-4">
                    <i class="fas fa-user-friends fa-2x mb-3"></i>
                    <h3 class="mb-2">{{ $totalLeads }}</h3>
                    <p class="mb-1 fw-bold">Total Leads</p>
                    <small class="opacity-75">Generated Prospects</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center text-white shadow-sm" style="background: linear-gradient(135deg, #ffc107, #fd7e14);">
                <div class="card-body py-4">
                    <i class="fas fa-chart-line fa-2x mb-3"></i>
                    <h3 class="mb-2">RM {{ number_format($avgCostPerLead, 2) }}</h3>
                    <p class="mb-1 fw-bold">Avg. Cost/Lead</p>
                    <small class="opacity-75">Efficiency Metric</small>
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
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light border-0 py-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 text-dark">Marketing Campaigns</h4>
                    <p class="text-muted mb-0">Manage your marketing efforts and track performance</p>
                </div>
                <a href="{{ route('marketing.create') }}" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-plus me-2"></i>New Campaign
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($campaigns->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <tr>
                                <th class="px-4 py-3 text-dark fw-semibold">Campaign Name</th>
                                <th class="px-3 py-3 text-dark fw-semibold">Type</th>
                                <th class="px-3 py-3 text-dark fw-semibold">Channel</th>
                                <th class="px-3 py-3 text-dark fw-semibold text-end">Budget</th>
                                <th class="px-3 py-3 text-dark fw-semibold text-center">Leads</th>
                                <th class="px-3 py-3 text-dark fw-semibold text-end">Cost/Lead</th>
                                <th class="px-3 py-3 text-dark fw-semibold text-center">ROI</th>
                                <th class="px-3 py-3 text-dark fw-semibold text-center">Status</th>
                                <th class="px-4 py-3 text-dark fw-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campaigns as $campaign)
                                <tr class="align-middle">
                                    <td class="px-4 py-4">
                                        <div>
                                            <h6 class="mb-1 text-dark">{{ $campaign->campaign_name }}</h6>
                                            @if($campaign->description)
                                                <small class="text-muted">{{ Str::limit($campaign->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-4">
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-2">
                                            {{ ucwords(str_replace('_', ' ', $campaign->campaign_type)) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4">
                                        <span class="text-dark fw-medium">{{ ucwords(str_replace('_', ' ', $campaign->marketing_channel)) }}</span>
                                    </td>
                                    <td class="px-3 py-4 text-end">
                                        <span class="text-dark fw-semibold">RM {{ number_format($campaign->budget_spent, 2) }}</span>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">{{ number_format($campaign->leads_generated) }}</span>
                                    </td>
                                    <td class="px-3 py-4 text-end">
                                        @if($campaign->cost_per_lead)
                                            <span class="text-dark fw-medium">RM {{ number_format($campaign->cost_per_lead, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        @if($campaign->roi)
                                            <span class="badge bg-{{ $campaign->roi > 0 ? 'success' : 'danger' }}-subtle text-{{ $campaign->roi > 0 ? 'success' : 'danger' }} border border-{{ $campaign->roi > 0 ? 'success' : 'danger' }}-subtle rounded-pill px-3 py-2">
                                                {{ number_format($campaign->roi, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <span class="badge bg-{{ $campaign->status == 'active' ? 'success' : ($campaign->status == 'completed' ? 'primary' : 'warning') }}-subtle text-{{ $campaign->status == 'active' ? 'success' : ($campaign->status == 'completed' ? 'primary' : 'warning') }} border border-{{ $campaign->status == 'active' ? 'success' : ($campaign->status == 'completed' ? 'primary' : 'warning') }}-subtle rounded-pill px-3 py-2">
                                            {{ ucfirst($campaign->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('marketing.show', $campaign) }}" class="btn btn-outline-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('marketing.edit', $campaign) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('marketing.destroy', $campaign) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete" 
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
                    <div class="py-4">
                        <i class="fas fa-bullhorn fa-4x text-primary mb-4 opacity-50"></i>
                        <h4 class="text-dark mb-2">No marketing campaigns found</h4>
                        <p class="text-muted mb-4">Start tracking your marketing efforts and boost your farm's visibility</p>
                        <a href="{{ route('marketing.create') }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-plus me-2"></i>Create Your First Campaign
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        @if($campaigns->hasPages())
            <div class="card-footer bg-light border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $campaigns->firstItem() }} to {{ $campaigns->lastItem() }} of {{ $campaigns->total() }} campaigns
                    </div>
                    {{ $campaigns->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection