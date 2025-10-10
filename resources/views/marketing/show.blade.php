@extends('layouts.app')

@section('title', 'Campaign Details')

@section('content')
<style>
.ai-insights-content .markdown-content {
    font-size: 14px;
    line-height: 1.6;
}
.ai-insights-content h1, .ai-insights-content h2, .ai-insights-content h3, .ai-insights-content h4, .ai-insights-content h5, .ai-insights-content h6 {
    color: #2c5aa0;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
}
.ai-insights-content strong {
    color: #1a4480;
}
.ai-insights-content ul, .ai-insights-content ol {
    margin-left: 1rem;
}
.ai-insights-content li {
    margin-bottom: 0.25rem;
}

/* Professional AI Insights Styles */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.ai-insights-card {
    transition: all 0.3s ease;
    border: none !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important;
}

.ai-insights-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important;
}

.ai-insights-content .card {
    transition: transform 0.2s ease-in-out;
    border: 1px solid #e9ecef;
}

.ai-insights-content .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.bg-light-success {
    background-color: #d1f2eb !important;
}
.bg-light-info {
    background-color: #d1ecf1 !important;
}
.bg-light-warning {
    background-color: #fff3cd !important;
}
.bg-light-danger {
    background-color: #f8d7da !important;
}

/* Loading Animation Enhancement */
.progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
}

/* Button Enhancements */
.btn-lg.rounded-3 {
    border-radius: 12px !important;
    transition: all 0.3s ease;
}

.btn-lg.rounded-3:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.ai-analysis .card {
    transition: transform 0.2s ease-in-out;
}
.ai-analysis .card:hover {
    transform: translateY(-2px);
}
.insight-section .alert {
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}
</style>
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
                    <h5 class="card-title mb-3">
                        <i class="fas fa-chart-bar me-2"></i>Campaign Performance
                    </h5>
                    
                    <!-- Key Metrics -->
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-primary mb-1">RM {{ number_format($marketing->budget_spent, 2) }}</h4>
                                <small class="text-muted">Budget Spent</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-primary text-white rounded">
                                <h4 class="mb-1">{{ number_format($marketing->leads_generated) }}</h4>
                                <small>Customers Generated</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-success text-white rounded">
                                <h4 class="mb-1">{{ number_format($marketing->conversions) }}</h4>
                                <small>Paid Customers</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-warning text-white rounded">
                                <h4 class="mb-1">RM {{ number_format($marketing->sales_revenue, 2) }}</h4>
                                <small>Total Revenue</small>
                            </div>
                        </div>
                    </div>

                    <!-- Calculated Analytics -->
                    <div class="row mt-3">
                        <div class="col-md-4 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="text-info mb-1">RM {{ number_format($marketing->cost_per_lead, 2) }}</h4>
                                <small class="text-muted">Cost per Customer</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="mb-1 text-{{ $marketing->roi > 0 ? 'success' : 'danger' }}">
                                    {{ number_format($marketing->roi, 1) }}%
                                </h4>
                                <small class="text-muted">Return on Investment</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="text-success mb-1">{{ number_format($marketing->conversion_rate, 1) }}%</h4>
                                <small class="text-muted">Conversion Rate</small>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Categories Breakdown -->
                    @if($marketing->leads_generated > 0)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6 class="text-muted mb-3">Customer Categories from Campaign</h6>
                            <div class="row">
                                @foreach($marketing->customer_categories as $category => $count)
                                <div class="col-md-4 mb-2">
                                    <div class="text-center p-2 bg-light rounded">
                                        <strong>{{ ucfirst($category) }}</strong>
                                        <span class="badge bg-secondary ms-2">{{ $count }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($marketing->impressions || $marketing->clicks)
                    <h6 class="text-muted mb-3 mt-4">Digital Metrics</h6>
                    <div class="row">
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

            <!-- Campaign Performance Summary -->
            @if($marketing->sales_revenue > 0)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-chart-line me-2"></i>Campaign Performance Summary
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="alert alert-{{ $marketing->roi > 0 ? 'success' : 'warning' }}">
                                <h6 class="mb-2">
                                    <i class="fas fa-{{ $marketing->roi > 0 ? 'chart-line' : 'exclamation-triangle' }} me-2"></i>
                                    Campaign Performance
                                </h6>
                                @if($marketing->roi > 0)
                                    <p class="mb-0">
                                        This campaign generated <strong>{{ $marketing->leads_generated }} customers</strong> 
                                        with <strong>RM {{ number_format($marketing->sales_revenue, 2) }} in revenue</strong>. 
                                        ROI: <strong>{{ number_format($marketing->roi, 1) }}%</strong>
                                    </p>
                                @else
                                    <p class="mb-0">
                                        Campaign needs optimization. Generated {{ $marketing->leads_generated }} customers 
                                        but revenue (RM {{ number_format($marketing->sales_revenue, 2) }}) 
                                        is below investment.
                                    </p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-success mb-1">{{ number_format($marketing->conversion_rate, 1) }}%</h4>
                                <small class="text-muted">Customer to Sale Rate</small>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: {{ min($marketing->conversion_rate, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Customer List from Campaign -->
            @if($marketing->leads_generated > 0)
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-users me-2"></i>Customers from this Campaign ({{ $marketing->leads_generated }})
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Total Purchases</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($marketing->customers()->latest()->take(10)->get() as $customer)
                                <tr>
                                    <td>
                                        <a href="{{ route('customers.show', $customer) }}" class="text-decoration-none">
                                            {{ $customer->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($customer->customer_type) }}</span>
                                    </td>
                                    <td>RM {{ number_format($customer->total_purchases, 2) }}</td>
                                    <td>
                                        @if($customer->total_purchases > 0)
                                            <span class="badge bg-success">Purchased</span>
                                        @else
                                            <span class="badge bg-warning">Lead</span>
                                        @endif
                                    </td>
                                    <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if($marketing->customers()->count() > 10)
                        <div class="text-center mt-2">
                            <a href="{{ route('customers.index', ['campaign' => $marketing->id]) }}" class="btn btn-outline-primary btn-sm">
                                View All {{ $marketing->customers()->count() }} Customers
                            </a>
                        </div>
                        @endif
                    </div>
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

            <!-- AI-Powered Insights -->
            <div class="card mb-3 ai-insights-card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-brain text-white fs-4 me-3"></i>
                        <div>
                            <h5 class="mb-0 text-white fw-bold">AI Marketing Insights</h5>
                            <small class="text-white-50">Advanced campaign analysis</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Generate Button -->
                    <div id="generate-section">
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fas fa-chart-line fa-2x text-primary"></i>
                                </div>
                                <h5 class="text-dark mb-2">Ready to Analyze Your Campaign</h5>
                                <p class="text-muted mb-4">Get AI-powered insights including performance analysis, strategic recommendations, and optimization opportunities tailored for your agricultural business.</p>
                            </div>
                            <button id="generate-insights-btn" class="btn btn-primary btn-lg px-4 py-3 rounded-3 shadow-sm">
                                <i class="fas fa-magic me-2"></i>Generate AI Insights
                            </button>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>Analysis typically takes 15-30 seconds
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Loading Section -->
                    <div id="loading-section" style="display: none;">
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <h5 class="text-dark mb-2">Analyzing Your Campaign</h5>
                                <p class="text-muted">Our AI is processing your marketing data and generating intelligent insights...</p>
                            </div>
                            <div class="progress mx-auto" style="height: 6px; width: 200px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 100%"></div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>Estimated time: 15-30 seconds
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Results Section -->
                    <div id="insights-section" style="display: none;">
                        <div class="ai-insights-content">
                            <div class="border-0" id="insights-content">
                                <!-- AI insights will be loaded here -->
                            </div>
                            
                            <div class="mt-4 p-3 bg-light rounded-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success rounded-circle p-1 me-2">
                                            <i class="fas fa-check text-white" style="font-size: 0.75rem;"></i>
                                        </div>
                                        <small class="text-muted fw-medium" id="insights-meta">
                                            AI Analysis completed successfully
                                        </small>
                                    </div>
                                    <button id="regenerate-insights-btn" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                        <i class="fas fa-redo me-1"></i>Regenerate Analysis
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Error Section -->
                    <div id="error-section" style="display: none;">
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                                </div>
                                <h5 class="text-dark mb-2">Analysis Unavailable</h5>
                                <p class="text-muted mb-4" id="error-message">Unable to generate AI insights at this time. Please try again.</p>
                            </div>
                            <button id="retry-insights-btn" class="btn btn-warning btn-lg px-4 py-3 rounded-3 shadow-sm">
                                <i class="fas fa-retry me-2"></i>Try Again
                            </button>
                        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const generateBtn = document.getElementById('generate-insights-btn');
    const regenerateBtn = document.getElementById('regenerate-insights-btn');
    const retryBtn = document.getElementById('retry-insights-btn');
    
    const generateSection = document.getElementById('generate-section');
    const loadingSection = document.getElementById('loading-section');
    const insightsSection = document.getElementById('insights-section');
    const errorSection = document.getElementById('error-section');
    
    const insightsContent = document.getElementById('insights-content');
    const errorMessage = document.getElementById('error-message');
    const insightsMeta = document.getElementById('insights-meta');
    
    function showLoading() {
        generateSection.style.display = 'none';
        loadingSection.style.display = 'block';
        insightsSection.style.display = 'none';
        errorSection.style.display = 'none';
    }
    
    function showInsights(data) {
        generateSection.style.display = 'none';
        loadingSection.style.display = 'none';
        insightsSection.style.display = 'block';
        errorSection.style.display = 'none';
        
        insightsContent.innerHTML = data.insights;
        insightsMeta.innerHTML = '<i class="fas fa-info-circle me-1"></i>AI Analysis completed';
    }
    
    function showError(message) {
        generateSection.style.display = 'none';
        loadingSection.style.display = 'none';
        insightsSection.style.display = 'none';
        errorSection.style.display = 'block';
        
        errorMessage.textContent = message;
    }
    
    function showGenerate() {
        generateSection.style.display = 'block';
        loadingSection.style.display = 'none';
        insightsSection.style.display = 'none';
        errorSection.style.display = 'none';
    }
    
    function generateInsights() {
        showLoading();
        
        fetch('{{ route("marketing.generate-insights", $marketing->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showInsights(data);
            } else {
                showError(data.error || 'Failed to generate AI insights. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Network error. Please check your connection and try again.');
        });
    }
    
    // Event listeners
    generateBtn.addEventListener('click', generateInsights);
    regenerateBtn.addEventListener('click', generateInsights);
    retryBtn.addEventListener('click', generateInsights);
});
</script>

@endsection