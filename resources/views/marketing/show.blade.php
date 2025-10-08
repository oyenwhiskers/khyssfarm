@extends('layouts.app')

@section('title', 'Campaign Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ $marketing->campaign_name }}</h1>
        <div>
            <a href="{{ route('marketing.edit', $marketing) }}" class="btn btn-primary btn-sm rounded-pill px-3 me-2">
                <i class="fas fa-edit me-1"></i>Edit Campaign
            </a>
            <a href="{{ route('marketing.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i>Back to Campaigns
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Campaign Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">Campaign Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">Campaign Type:</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ ucwords(str_replace('_', ' ', $marketing->campaign_type)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Marketing Channel:</td>
                                    <td>{{ ucwords(str_replace('_', ' ', $marketing->marketing_channel)) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Status:</td>
                                    <td>
                                        <span class="badge bg-{{ $marketing->status == 'active' ? 'success' : ($marketing->status == 'completed' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($marketing->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Start Date:</td>
                                    <td>{{ $marketing->start_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">End Date:</td>
                                    <td>{{ $marketing->end_date ? $marketing->end_date->format('M d, Y') : 'Ongoing' }}</td>
                                </tr>
                                @if($marketing->description)
                                <tr>
                                    <td class="fw-bold text-muted">Description:</td>
                                    <td>{{ $marketing->description }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title">Campaign Duration</h5>
                            @php
                                $duration = $marketing->end_date 
                                    ? $marketing->start_date->diffInDays($marketing->end_date) + 1
                                    : $marketing->start_date->diffInDays(now()) + 1;
                            @endphp
                            <div class="text-center py-3">
                                <h2 class="text-primary mb-0">{{ $duration }}</h2>
                                <p class="text-muted mb-0">{{ $duration == 1 ? 'Day' : 'Days' }}</p>
                                @if(!$marketing->end_date)
                                    <small class="text-success">
                                        <i class="fas fa-play-circle me-1"></i> Active Campaign
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Performance Metrics</h5>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-primary mb-1">RM {{ number_format($marketing->budget_spent, 2) }}</h4>
                                <small class="text-muted">Budget Spent</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-info mb-1">{{ number_format($marketing->leads_generated) }}</h4>
                                <small class="text-muted">Leads Generated</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-success mb-1">{{ number_format($marketing->conversions) }}</h4>
                                <small class="text-muted">Conversions</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-warning mb-1">RM {{ number_format($marketing->sales_revenue, 2) }}</h4>
                                <small class="text-muted">Sales Revenue</small>
                            </div>
                        </div>
                    </div>

                    @if($marketing->impressions || $marketing->clicks)
                    <div class="row mt-3">
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-secondary mb-1">{{ number_format($marketing->impressions) }}</h4>
                                <small class="text-muted">Impressions</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-secondary mb-1">{{ number_format($marketing->clicks) }}</h4>
                                <small class="text-muted">Clicks</small>
                            </div>
                        </div>
                        @if($marketing->impressions > 0)
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-secondary mb-1">{{ number_format(($marketing->clicks / $marketing->impressions) * 100, 2) }}%</h4>
                                <small class="text-muted">Click Rate</small>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Calculated Metrics -->
            @if($marketing->leads_generated > 0)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Calculated Analytics</h5>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="text-primary mb-1">RM {{ number_format($marketing->cost_per_lead, 2) }}</h4>
                                <small class="text-muted">Cost per Lead</small>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                        @if($marketing->sales_revenue > 0)
                        <div class="col-md-4 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="mb-1 text-{{ $marketing->roi > 0 ? 'success' : 'danger' }}">
                                    {{ number_format($marketing->roi, 1) }}%
                                </h4>
                                <small class="text-muted">Return on Investment</small>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-{{ $marketing->roi > 0 ? 'success' : 'danger' }}" 
                                         style="width: {{ min(abs($marketing->roi), 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($marketing->conversions > 0)
                        <div class="col-md-4 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="text-success mb-1">{{ number_format($marketing->conversion_rate, 1) }}%</h4>
                                <small class="text-muted">Conversion Rate</small>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: {{ min($marketing->conversion_rate, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($marketing->sales_revenue > 0)
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="alert alert-{{ $marketing->roi > 0 ? 'success' : 'warning' }}">
                                <h6 class="mb-2">
                                    <i class="fas fa-{{ $marketing->roi > 0 ? 'chart-line' : 'exclamation-triangle' }} me-2"></i>
                                    ROI Analysis
                                </h6>
                                @if($marketing->roi > 0)
                                    <p class="mb-0">This campaign is profitable! For every RM1 spent, you gained RM{{ number_format($marketing->roi / 100 + 1, 2) }}.</p>
                                @else
                                    <p class="mb-0">This campaign needs optimization. Consider adjusting targeting or budget allocation.</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6 class="mb-2">
                                    <i class="fas fa-calculator me-2"></i>
                                    Key Insights
                                </h6>
                                <ul class="mb-0 small">
                                    <li>Average revenue per conversion: RM {{ number_format($marketing->sales_revenue / max($marketing->conversions, 1), 2) }}</li>
                                    @if($marketing->clicks > 0)
                                    <li>Cost per click: RM {{ number_format($marketing->budget_spent / $marketing->clicks, 2) }}</li>
                                    @endif
                                    <li>Total profit: RM {{ number_format($marketing->sales_revenue - $marketing->budget_spent, 2) }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('marketing.edit', $marketing) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i> Edit Campaign
                        </a>
                        <button class="btn btn-outline-info" onclick="window.print()">
                            <i class="fas fa-print me-2"></i> Print Report
                        </button>
                        <form action="{{ route('marketing.destroy', $marketing) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100" 
                                    onclick="return confirm('Are you sure you want to delete this campaign?')">
                                <i class="fas fa-trash me-2"></i> Delete Campaign
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Performance Status -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Performance Status</h5>
                    @if($marketing->leads_generated > 0)
                        @php
                            $performance = 'average';
                            if($marketing->cost_per_lead < 50) $performance = 'excellent';
                            elseif($marketing->cost_per_lead < 100) $performance = 'good';
                            elseif($marketing->cost_per_lead > 200) $performance = 'poor';
                        @endphp
                        
                        <div class="alert alert-{{ $performance == 'excellent' ? 'success' : ($performance == 'good' ? 'info' : ($performance == 'poor' ? 'danger' : 'warning')) }}">
                            <h6 class="mb-2">
                                <i class="fas fa-{{ $performance == 'excellent' ? 'star' : ($performance == 'good' ? 'thumbs-up' : ($performance == 'poor' ? 'exclamation-triangle' : 'info-circle')) }} me-2"></i>
                                {{ ucfirst($performance) }} Performance
                            </h6>
                            @if($performance == 'excellent')
                                <p class="mb-0">Outstanding results! This campaign is highly cost-effective.</p>
                            @elseif($performance == 'good')
                                <p class="mb-0">Good performance with room for optimization.</p>
                            @elseif($performance == 'poor')
                                <p class="mb-0">Consider revising strategy or pausing this campaign.</p>
                            @else
                                <p class="mb-0">Moderate performance. Monitor closely for improvements.</p>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-secondary">
                            <p class="mb-0">No performance data available yet. Add campaign results to see analysis.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recommendations -->
            @if($marketing->leads_generated > 0)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recommendations</h5>
                    <div class="alert alert-light">
                        <ul class="mb-0 small">
                            @if($marketing->cost_per_lead > 150)
                                <li>Consider optimizing targeting to reduce cost per lead</li>
                            @endif
                            @if($marketing->conversion_rate < 10)
                                <li>Improve lead quality or follow-up process</li>
                            @endif
                            @if($marketing->roi < 50 && $marketing->sales_revenue > 0)
                                <li>Focus on higher-value conversions</li>
                            @endif
                            @if($marketing->clicks > 0 && ($marketing->clicks / max($marketing->impressions, 1)) < 0.02)
                                <li>Improve ad creative or targeting</li>
                            @endif
                            <li>Track long-term customer value for better ROI analysis</li>
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection