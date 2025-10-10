<?php

namespace App\Services;

use App\Models\Marketing;

class OpenAIService
{
    private $apiKey;
    private static $cache = [];
    private static $requestTimes = [];

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', env('OPENAI_API_KEY'));
    }

    public function getMarketingInsights(Marketing $marketing)
    {
        try {
            // Check cache first to avoid repeated API calls
            $cacheKey = 'insights_' . $marketing->id . '_' . $marketing->updated_at->timestamp;
            
            // Also check if there's a recent request for the same marketing campaign
            $timeKey = 'time_' . $marketing->id;
            $currentTime = time();
            
            if (isset(self::$cache[$cacheKey])) {
                return self::$cache[$cacheKey];
            }
            
            // Prevent duplicate requests within 30 seconds
            if (isset(self::$requestTimes[$timeKey]) && ($currentTime - self::$requestTimes[$timeKey]) < 30) {
                if (isset(self::$cache[$cacheKey])) {
                    return self::$cache[$cacheKey];
                }
                // Return a temporary message if no cache available
                return [
                    'success' => true,
                    'insights' => '<div class="alert alert-info"><i class="fas fa-clock me-2"></i>AI analysis is being processed. Please refresh in a moment.</div>',
                    'usage' => null
                ];
            }
            
            // Record the request time
            self::$requestTimes[$timeKey] = $currentTime;

            // Prepare campaign data for AI analysis
            $campaignData = $this->prepareCampaignData($marketing);
            
            // Create a comprehensive prompt for AI analysis
            $prompt = $this->buildAnalysisPrompt($campaignData);
            
            // Get AI insights from OpenAI using cURL (using faster model)
            $response = $this->callOpenAI([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 1000,
                'temperature' => 0.6,
                'stream' => false
            ]);

            if ($response === false) {
                throw new \Exception('Failed to get response from OpenAI API');
            }

            $responseData = json_decode($response, true);
            
            if (!$responseData || !isset($responseData['choices'][0]['message']['content'])) {
                throw new \Exception('Invalid response format from OpenAI API');
            }

            $insights = $responseData['choices'][0]['message']['content'];
            
            // Clean up the response if it's wrapped in markdown code blocks
            $insights = preg_replace('/```html\s*/', '', $insights);
            $insights = preg_replace('/```\s*$/', '', $insights);
            $insights = trim($insights);
            
            // Extract just the content inside body tag if full HTML is returned
            if (preg_match('/<body[^>]*>(.*?)<\/body>/s', $insights, $matches)) {
                $insights = $matches[1];
            }
            
            // Remove DOCTYPE, html, head tags if present
            $insights = preg_replace('/<!DOCTYPE[^>]*>/i', '', $insights);
            $insights = preg_replace('/<\/?html[^>]*>/i', '', $insights);
            $insights = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $insights);
            $insights = preg_replace('/<\/?body[^>]*>/i', '', $insights);
            $insights = trim($insights);
            
            // If the response is not HTML formatted, convert it
            if (!str_contains($insights, '<div')) {
                $insights = $this->formatPlainTextToHTML($insights);
            }

            $result = [
                'success' => true,
                'insights' => $insights,
                'usage' => $responseData['usage'] ?? null
            ];
            
            // Cache the result
            self::$cache[$cacheKey] = $result;
            
            return $result;
            
        } catch (\Exception $e) {
            // Return error instead of fallback
            return [
                'success' => false,
                'insights' => 'Unable to generate AI insights at this time. Please try again later.',
                'usage' => null,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function callOpenAI($data)
    {
        $startTime = microtime(true);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        if ($error) {
            curl_close($ch);
            if (strpos($error, 'timeout') !== false || strpos($error, 'timed out') !== false) {
                throw new \Exception('Request timed out. The AI service is currently slow. Please try again in a moment.');
            }
            throw new \Exception('Connection Error: ' . $error);
        }
        
        if ($httpCode !== 200) {
            curl_close($ch);
            throw new \Exception('API Error: HTTP ' . $httpCode . ' response from OpenAI');
        }
        
        curl_close($ch);
        return $result;
    }
    
    private function getSystemPrompt()
    {
        return 'You are an expert marketing analyst specializing in agricultural business and Southeast Asian markets. Provide actionable, data-driven insights for small farm businesses in Malaysia.

**CRITICAL: FOLLOW THIS EXACT FORMAT STRUCTURE - DO NOT MODIFY THE HTML CLASSES OR STRUCTURE**

Your response MUST be EXACTLY this HTML structure with these exact Bootstrap classes:

```html
<div class="marketing-analysis">
    <div class="status-section mb-4">
        <h6 class="text-primary mb-3">
            <i class="fas fa-chart-line me-2"></i>Current Marketing Status
        </h6>
        <div class="alert alert-success border-0">
            <i class="fas fa-chart-trend-up me-2"></i>
            Your 2-3 sentence performance assessment here.
        </div>
    </div>

    <div class="recommendations-section mb-4">
        <h6 class="text-primary mb-3">
            <i class="fas fa-brain me-2"></i>AI Recommendations
        </h6>
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start">
                            <div class="badge bg-primary rounded-pill me-3 mt-1">1</div>
                            <div>
                                <h6 class="mb-2 text-dark">Your Title Here</h6>
                                <p class="mb-0 small text-muted">Your description here.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start">
                            <div class="badge bg-primary rounded-pill me-3 mt-1">2</div>
                            <div>
                                <h6 class="mb-2 text-dark">Your Title Here</h6>
                                <p class="mb-0 small text-muted">Your description here.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start">
                            <div class="badge bg-primary rounded-pill me-3 mt-1">3</div>
                            <div>
                                <h6 class="mb-2 text-dark">Your Title Here</h6>
                                <p class="mb-0 small text-muted">Your description here.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start">
                            <div class="badge bg-primary rounded-pill me-3 mt-1">4</div>
                            <div>
                                <h6 class="mb-2 text-dark">Your Title Here</h6>
                                <p class="mb-0 small text-muted">Your description here.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="avoid-section mb-4">
        <h6 class="text-danger mb-3">
            <i class="fas fa-shield-alt me-2"></i>What to Avoid
        </h6>
        <div class="alert alert-light border-0" style="background-color: #f8d7da;">
            <ul class="mb-0 list-unstyled">
                <li class="mb-2">
                    <i class="fas fa-times-circle text-danger me-2"></i>Your specific thing to avoid
                </li>
                <li class="mb-2">
                    <i class="fas fa-times-circle text-danger me-2"></i>Your specific thing to avoid
                </li>
                <li class="mb-2">
                    <i class="fas fa-times-circle text-danger me-2"></i>Your specific thing to avoid
                </li>
            </ul>
        </div>
    </div>

    <div class="insight-section">
        <div class="alert alert-primary border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Key Insight:</strong> Your one powerful strategic insight for future growth.
        </div>
    </div>
</div>
```

**STRICT FORMATTING RULES:**
1. DO NOT change any class names or HTML structure
2. DO NOT add extra divs or modify the layout
3. Provide exactly 2-4 recommendations (each in its own col-12 for full width)
4. Provide exactly 3-5 items to avoid
5. Use alert-success, alert-info, alert-warning, or alert-danger for status
6. Replace only the text content, keep all HTML exactly as shown
7. Use specific numbers and percentages from the data provided

**CONTENT REQUIREMENTS:**
- Status assessment must reference actual ROI, conversion rate, or cost metrics
- Recommendations must be specific and actionable
- Items to avoid must be based on campaign weaknesses
- Key insight must suggest strategic direction
- All content must be relevant to Malaysian agricultural market';
    }

    public function getChannelRecommendations($campaignType, $targetAudience, $budget)
    {
        try {
            $prompt = "Recommend the best marketing channels for KHYSS Farm in Sandakan, Sabah, Malaysia.

**Campaign Requirements:**
- Campaign Type: {$campaignType}
- Target Audience: {$targetAudience}  
- Budget Range: RM {$budget}
- Business: Small agricultural farm selling fresh produce
- Location: Sandakan, Sabah, Malaysia

**IMPORTANT: Use EXACTLY the HTML structure provided in your system prompt. Do not deviate from the format.**

**Requirements:**
1. Provide exactly 4-5 channel recommendations
2. Rate each channel with 1-5 stars based on suitability
3. Include realistic cost estimates in Malaysian Ringgit (RM)
4. Provide budget allocation percentages that total 100%
5. Focus on channels effective for agricultural products in Southeast Asia
6. Include specific implementation tips for Malaysian market

**Considerations:**
- High mobile usage in Malaysia (85%+ access social media via mobile)
- WhatsApp is preferred communication (95% usage rate)
- Local language preferences (Bahasa Malaysia)
- Agricultural business seasonality
- Individual vs wholesale customer segments
- Trust-building importance in agricultural communities
- Visual marketing effectiveness for produce

Ensure recommendations are practical for small farm operations and cost-effective for the specified budget range.";

            $response = $this->callOpenAI([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system', 
                        'content' => $this->getChannelSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 1200,
                'temperature' => 0.6,
            ]);

            if ($response === false) {
                throw new \Exception('Failed to get response from OpenAI API');
            }

            $responseData = json_decode($response, true);
            
            if (!$responseData || !isset($responseData['choices'][0]['message']['content'])) {
                throw new \Exception('Invalid response format from OpenAI API');
            }

            $recommendations = $responseData['choices'][0]['message']['content'];
            
            // Clean up the response if it's wrapped in markdown code blocks
            $recommendations = preg_replace('/```html\s*/', '', $recommendations);
            $recommendations = preg_replace('/```\s*$/', '', $recommendations);
            $recommendations = trim($recommendations);
            
            // Extract just the content inside body tag if full HTML is returned
            if (preg_match('/<body[^>]*>(.*?)<\/body>/s', $recommendations, $matches)) {
                $recommendations = $matches[1];
            }
            
            // Remove DOCTYPE, html, head tags if present
            $recommendations = preg_replace('/<!DOCTYPE[^>]*>/i', '', $recommendations);
            $recommendations = preg_replace('/<\/?html[^>]*>/i', '', $recommendations);
            $recommendations = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $recommendations);
            $recommendations = preg_replace('/<\/?body[^>]*>/i', '', $recommendations);
            $recommendations = trim($recommendations);

            return $recommendations;
            
        } catch (\Exception $e) {
            // Return error message instead of fallback
            return '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Unable to generate AI channel recommendations at this time. Please try again later.</div>';
        }
    }
    
    private function getChannelSystemPrompt()
    {
        return 'You are a marketing consultant specializing in agricultural businesses and Southeast Asian markets.

**CRITICAL: FOLLOW THIS EXACT FORMAT STRUCTURE FOR CHANNEL RECOMMENDATIONS**

Your response MUST follow this exact HTML structure:

```html
<div class="channel-recommendations">
    <div class="mb-4">
        <h6 class="text-primary mb-3">
            <i class="fas fa-bullseye me-2"></i>Recommended Marketing Channels
        </h6>
        <p class="text-muted small">Optimized for agricultural business in Sandakan, Sabah</p>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="mb-0 text-primary">[Channel Name]</h6>
                        <div>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                    </div>
                    <span class="badge bg-light text-dark mb-2">[Cost Range]</span>
                    <p class="small mb-2"><strong>Why:</strong> [Reason for recommendation]</p>
                    <p class="small mb-2"><strong>Best for:</strong> [Target audience fit]</p>
                    <p class="small mb-0 text-muted">
                        <i class="fas fa-lightbulb me-1"></i>
                        <em>[Implementation tip]</em>
                    </p>
                </div>
            </div>
        </div>
        <!-- Repeat for 4-5 channels -->
    </div>
    
    <div class="mt-4">
        <div class="alert alert-info border-0">
            <h6 class="mb-3">
                <i class="fas fa-chart-pie me-2"></i>Recommended Budget Allocation
            </h6>
            <div class="row text-center">
                <div class="col-6 col-md-3 mb-2">
                    <div class="h5 text-primary mb-0">[%]</div>
                    <small class="text-muted">[Channel]</small>
                </div>
                <!-- Repeat for each allocation -->
            </div>
            <p class="mb-0 mt-3 small">
                <i class="fas fa-rocket me-2"></i>
                <strong>Best Starting Combination:</strong> [Recommended channel mix]
            </p>
        </div>
    </div>
</div>
```

**FORMATTING RULES:**
1. Always include exactly 4-5 channel recommendations
2. Use 5-star rating system (adjust number of filled stars based on suitability)
3. Include cost estimates in RM (Malaysian Ringgit)
4. Budget allocation percentages must add up to 100%
5. Focus on Malaysian agricultural market context
6. Include specific implementation tips for each channel
7. Use appropriate Bootstrap color classes (primary, success, warning, info)

**CONTENT GUIDELINES:**
- Prioritize channels effective in Southeast Asian agricultural markets
- Consider mobile-first approach (high mobile usage in Malaysia)
- Include local market factors (Bahasa Malaysia, cultural preferences)
- Provide realistic cost estimates for small farm operations
- Focus on channels with proven ROI for agricultural products';
    }
    
    private function prepareCampaignData(Marketing $marketing)
    {
        return [
            'campaign_name' => $marketing->campaign_name,
            'campaign_type' => $marketing->campaign_type,
            'marketing_channel' => $marketing->marketing_channel,
            'budget_spent' => $marketing->budget_spent,
            'customers_generated' => $marketing->getAttribute('leads_generated_calculated') ?? $marketing->leads_generated,
            'conversions' => $marketing->getAttribute('conversions_calculated') ?? $marketing->conversions,
            'revenue' => $marketing->getAttribute('sales_revenue_calculated') ?? $marketing->sales_revenue,
            'roi' => $marketing->getAttribute('roi_calculated') ?? $marketing->roi,
            'cost_per_customer' => $marketing->getAttribute('cost_per_lead_calculated') ?? $marketing->cost_per_lead,
            'conversion_rate' => $marketing->getAttribute('conversion_rate_calculated') ?? $marketing->conversion_rate,
            'duration_days' => $marketing->start_date->diffInDays($marketing->end_date) + 1,
            'impressions' => $marketing->impressions,
            'clicks' => $marketing->clicks,
            'customer_categories' => $marketing->getAttribute('customer_categories_calculated') ?? $marketing->customer_categories,
            'status' => $marketing->status,
            'start_date' => $marketing->start_date->format('Y-m-d'),
            'end_date' => $marketing->end_date ? $marketing->end_date->format('Y-m-d') : 'ongoing'
        ];
    }

    private function buildAnalysisPrompt(array $data)
    {
        $customerBreakdown = '';
        if (!empty($data['customer_categories'])) {
            $customerBreakdown = "\nCustomer Types: ";
            foreach ($data['customer_categories'] as $type => $count) {
                $customerBreakdown .= ucfirst($type) . ": {$count}, ";
            }
            $customerBreakdown = rtrim($customerBreakdown, ', ');
        }

        return "Analyze this KHYSS Farm marketing campaign (agricultural business, Sandakan, Malaysia):

**Campaign:** {$data['campaign_name']} | {$data['campaign_type']} | {$data['marketing_channel']} | {$data['duration_days']} days | {$data['status']}

**Performance:**
- Budget: RM {$data['budget_spent']} | Customers: {$data['customers_generated']} | Conversions: {$data['conversions']}
- Revenue: RM {$data['revenue']} | ROI: {$data['roi']}% | Cost/Customer: RM {$data['cost_per_customer']} | Conv Rate: {$data['conversion_rate']}%{$customerBreakdown}

**Required Analysis:** Use EXACT HTML format from system prompt:
1. Marketing Status (ROI vs 100-300% agricultural standard)
2. AI Recommendations (2-4 specific actions)
3. What to Avoid (3-5 pitfalls)
4. Key Insight (growth strategy)

Focus: ROI optimization, Malaysian agricultural market, local Sabah factors, seasonal considerations.";
    }
    
    private function formatPlainTextToHTML($text)
    {
        // Convert markdown-style formatting to HTML if needed
        $html = '<div class="ai-analysis">';
        
        // Split into sections and format
        $sections = explode("\n\n", $text);
        foreach ($sections as $section) {
            if (str_contains($section, '**') || str_contains($section, '#')) {
                // Format as styled content
                $section = str_replace('**', '<strong>', $section);
                $section = str_replace('**', '</strong>', $section);
                $html .= '<div class="mb-3">' . nl2br($section) . '</div>';
            } else {
                $html .= '<p>' . nl2br($section) . '</p>';
            }
        }
        
        $html .= '</div>';
        return $html;
    }
}