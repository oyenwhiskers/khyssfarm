@extends('layouts.app')

@section('title', 'Create Marketing Campaign')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>New Marketing Campaign</h5>
            </div>
            <div class="card-body">
                    <form action="{{ route('marketing.store') }}" method="POST">
                        @csrf
                        
                        <!-- Campaign Information -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">Campaign Information</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="campaign_name" class="form-label">Campaign Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('campaign_name') is-invalid @enderror" 
                                           id="campaign_name" name="campaign_name" value="{{ old('campaign_name') }}" 
                                           placeholder="Enter campaign name" required>
                                    @error('campaign_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="campaign_type" class="form-label">Campaign Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('campaign_type') is-invalid @enderror" 
                                            id="campaign_type" name="campaign_type" required>
                                        <option value="">Select Campaign Type</option>
                                        <option value="lead_generation" {{ old('campaign_type') == 'lead_generation' ? 'selected' : '' }}>Lead Generation</option>
                                        <option value="brand_awareness" {{ old('campaign_type') == 'brand_awareness' ? 'selected' : '' }}>Brand Awareness</option>
                                        <option value="sales_conversion" {{ old('campaign_type') == 'sales_conversion' ? 'selected' : '' }}>Sales Conversion</option>
                                        <option value="customer_retention" {{ old('campaign_type') == 'customer_retention' ? 'selected' : '' }}>Customer Retention</option>
                                        <option value="product_launch" {{ old('campaign_type') == 'product_launch' ? 'selected' : '' }}>Product Launch</option>
                                    </select>
                                    @error('campaign_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="marketing_channel" class="form-label">Marketing Channel <span class="text-danger">*</span></label>
                                    <select class="form-select @error('marketing_channel') is-invalid @enderror" 
                                            id="marketing_channel" name="marketing_channel" required>
                                        <option value="">Select Marketing Channel</option>
                                        <option value="facebook" {{ old('marketing_channel') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                        <option value="instagram" {{ old('marketing_channel') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                        <option value="tiktok" {{ old('marketing_channel') == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                                        <option value="whatsapp" {{ old('marketing_channel') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                        <option value="google_ads" {{ old('marketing_channel') == 'google_ads' ? 'selected' : '' }}>Google Ads</option>
                                        <option value="flyers" {{ old('marketing_channel') == 'flyers' ? 'selected' : '' }}>Flyers</option>
                                        <option value="radio" {{ old('marketing_channel') == 'radio' ? 'selected' : '' }}>Radio</option>
                                        <option value="newspaper" {{ old('marketing_channel') == 'newspaper' ? 'selected' : '' }}>Newspaper</option>
                                        <option value="word_of_mouth" {{ old('marketing_channel') == 'word_of_mouth' ? 'selected' : '' }}>Word of Mouth</option>
                                        <option value="event" {{ old('marketing_channel') == 'event' ? 'selected' : '' }}>Event</option>
                                        <option value="other" {{ old('marketing_channel') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('marketing_channel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="paused" {{ old('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Enter campaign description">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Campaign Duration -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">Campaign Duration</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date') }}">
                                    <div class="form-text">Leave empty for ongoing campaigns</div>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Budget & Results -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">Budget & Results</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="budget_spent" class="form-label">Budget Spent (RM) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('budget_spent') is-invalid @enderror" 
                                           id="budget_spent" name="budget_spent" value="{{ old('budget_spent') }}" 
                                           step="0.01" min="0" placeholder="0.00" required>
                                    @error('budget_spent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="leads_generated" class="form-label">Leads Generated</label>
                                    <input type="number" class="form-control @error('leads_generated') is-invalid @enderror" 
                                           id="leads_generated" name="leads_generated" value="{{ old('leads_generated') }}" 
                                           min="0" placeholder="0">
                                    @error('leads_generated')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="impressions" class="form-label">Impressions</label>
                                    <input type="number" class="form-control @error('impressions') is-invalid @enderror" 
                                           id="impressions" name="impressions" value="{{ old('impressions') }}" 
                                           min="0" placeholder="0">
                                    @error('impressions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="clicks" class="form-label">Clicks</label>
                                    <input type="number" class="form-control @error('clicks') is-invalid @enderror" 
                                           id="clicks" name="clicks" value="{{ old('clicks') }}" 
                                           min="0" placeholder="0">
                                    @error('clicks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="conversions" class="form-label">Conversions</label>
                                    <input type="number" class="form-control @error('conversions') is-invalid @enderror" 
                                           id="conversions" name="conversions" value="{{ old('conversions') }}" 
                                           min="0" placeholder="0">
                                    @error('conversions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="sales_revenue" class="form-label">Sales Revenue (RM)</label>
                                    <input type="number" class="form-control @error('sales_revenue') is-invalid @enderror" 
                                           id="sales_revenue" name="sales_revenue" value="{{ old('sales_revenue') }}" 
                                           step="0.01" min="0" placeholder="0.00">
                                    @error('sales_revenue')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between gap-2">
                            <a href="{{ route('marketing.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Campaigns
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Create Campaign
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tips Section -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Campaign Tips</h5>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb me-2"></i>Tips for Better Campaigns:</h6>
                        <ul class="mb-0 small">
                            <li>Use clear, descriptive campaign names</li>
                            <li>Set realistic budgets based on your goals</li>
                            <li>Track all relevant metrics for better ROI analysis</li>
                            <li>Regular monitoring helps optimize performance</li>
                            <li>Test different channels to find what works best</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-chart-line me-2"></i>Key Metrics:</h6>
                        <ul class="mb-0 small">
                            <li><strong>Cost per Lead:</strong> Budget ÷ Leads</li>
                            <li><strong>ROI:</strong> (Revenue - Budget) ÷ Budget × 100</li>
                            <li><strong>Conversion Rate:</strong> Conversions ÷ Leads × 100</li>
                            <li><strong>Click Rate:</strong> Clicks ÷ Impressions × 100</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection