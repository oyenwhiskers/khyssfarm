<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Forecast;
use App\Models\Harvest;
use App\Models\Sale;
use App\Models\ResellSale;
use App\Models\Customer;
use Carbon\Carbon;

class GenerateAutoForecasts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'forecasts:generate 
                            {--period=3 : Number of months to forecast} 
                            {--force : Force regeneration of existing forecasts}';

    /**
     * The console command description.
     */
    protected $description = 'Automatically generate yield, revenue, and customer growth forecasts based on historical data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $months = (int) $this->option('period');
        $force = $this->option('force');

        $this->info("Generating automatic forecasts for the next {$months} months...");

        $this->generateYieldForecast($months, $force);
        $this->generateRevenueForecast($months, $force);
        $this->generateCustomerGrowthForecast($months, $force);

        $this->info('Automatic forecast generation completed!');
    }

    private function generateYieldForecast($months, $force)
    {
        $this->info('Generating yield forecasts...');
        
        // Get historical yield data (last 12 months)
        $historicalYield = Harvest::where('harvest_date', '>=', Carbon::now()->subMonths(12))
                                 ->selectRaw('YEAR(harvest_date) as year, MONTH(harvest_date) as month, SUM(quantity_kg) as total')
                                 ->groupBy('year', 'month')
                                 ->orderBy('year', 'desc')
                                 ->orderBy('month', 'desc')
                                 ->get();

        if ($historicalYield->isEmpty()) {
            $this->warn('No historical yield data found. Skipping yield forecast.');
            return;
        }

        // Calculate monthly average and trend
        $avgMonthlyYield = $historicalYield->avg('total');
        $seasonalFactors = $this->calculateSeasonalFactors($historicalYield);
        
        // Generate forecasts for each month
        for ($i = 1; $i <= $months; $i++) {
            $periodStart = Carbon::now()->addMonths($i - 1)->startOfMonth();
            $periodEnd = Carbon::now()->addMonths($i - 1)->endOfMonth();
            
            // Check if forecast already exists
            $existingForecast = Forecast::where('forecast_type', 'yield')
                                      ->where('category', 'farm_production')
                                      ->where('period_start', $periodStart)
                                      ->where('period_end', $periodEnd)
                                      ->first();

            if ($existingForecast && !$force) {
                $this->line("Yield forecast for {$periodStart->format('M Y')} already exists. Use --force to regenerate.");
                continue;
            }

            // Apply seasonal adjustment
            $seasonalMultiplier = $seasonalFactors[$periodStart->month] ?? 1.0;
            $projectedValue = $avgMonthlyYield * $seasonalMultiplier;
            
            // Calculate confidence based on data availability
            $confidence = min(90, 50 + ($historicalYield->count() * 4));

            $forecastData = [
                'forecast_type' => 'yield',
                'category' => 'farm_production',
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'target_value' => $projectedValue * 1.1, // Target 10% above projection
                'projected_value' => $projectedValue,
                'unit' => 'kg',
                'confidence_level' => $confidence,
                'methodology' => 'seasonal_adjustment',
                'notes' => 'Auto-generated based on ' . $historicalYield->count() . ' months of historical data',
                'status' => 'active',
                'created_by' => null,
            ];

            if ($existingForecast) {
                $existingForecast->update($forecastData);
                $this->line("Updated yield forecast for {$periodStart->format('M Y')}");
            } else {
                Forecast::create($forecastData);
                $this->line("Created yield forecast for {$periodStart->format('M Y')}");
            }
        }
    }

    private function generateRevenueForecast($months, $force)
    {
        $this->info('Generating revenue forecasts...');
        
        // Get historical revenue data from both farm sales and resell sales
        $farmRevenue = Sale::where('sale_date', '>=', Carbon::now()->subMonths(12))
                          ->where('payment_status', 'paid')
                          ->selectRaw('YEAR(sale_date) as year, MONTH(sale_date) as month, SUM(total_amount) as total')
                          ->groupBy('year', 'month')
                          ->get();

        $resellRevenue = ResellSale::where('sale_date', '>=', Carbon::now()->subMonths(12))
                                  ->selectRaw('YEAR(sale_date) as year, MONTH(sale_date) as month, SUM(total_sale_amount) as total')
                                  ->groupBy('year', 'month')
                                  ->get();

        // Generate forecasts for farm production
        $this->generateRevenueByCategory($farmRevenue, 'farm_production', $months, $force);
        
        // Generate forecasts for resell business
        $this->generateRevenueByCategory($resellRevenue, 'resell_business', $months, $force);
    }

    private function generateRevenueByCategory($historicalRevenue, $category, $months, $force)
    {
        if ($historicalRevenue->isEmpty()) {
            $this->warn("No historical revenue data found for {$category}. Skipping revenue forecast.");
            return;
        }

        $avgMonthlyRevenue = $historicalRevenue->avg('total');
        
        // Calculate growth trend
        $recentAvg = $historicalRevenue->take(3)->avg('total');
        $earlierAvg = $historicalRevenue->skip(6)->take(3)->avg('total');
        $growthRate = $earlierAvg > 0 ? ($recentAvg - $earlierAvg) / $earlierAvg : 0;

        for ($i = 1; $i <= $months; $i++) {
            $periodStart = Carbon::now()->addMonths($i - 1)->startOfMonth();
            $periodEnd = Carbon::now()->addMonths($i - 1)->endOfMonth();
            
            $existingForecast = Forecast::where('forecast_type', 'revenue')
                                      ->where('category', $category)
                                      ->where('period_start', $periodStart)
                                      ->where('period_end', $periodEnd)
                                      ->first();

            if ($existingForecast && !$force) {
                $this->line("Revenue forecast for {$category} - {$periodStart->format('M Y')} already exists.");
                continue;
            }

            // Apply growth trend
            $projectedValue = $avgMonthlyRevenue * (1 + $growthRate * $i);
            $confidence = min(85, 45 + ($historicalRevenue->count() * 4));

            $forecastData = [
                'forecast_type' => 'revenue',
                'category' => $category,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'target_value' => $projectedValue * 1.15, // Target 15% above projection
                'projected_value' => $projectedValue,
                'unit' => 'myr',
                'confidence_level' => $confidence,
                'methodology' => 'historical_trend',
                'notes' => 'Auto-generated with growth trend analysis',
                'status' => 'active',
                'created_by' => null,
            ];

            if ($existingForecast) {
                $existingForecast->update($forecastData);
                $this->line("Updated revenue forecast for {$category} - {$periodStart->format('M Y')}");
            } else {
                Forecast::create($forecastData);
                $this->line("Created revenue forecast for {$category} - {$periodStart->format('M Y')}");
            }
        }
    }

    private function generateCustomerGrowthForecast($months, $force)
    {
        $this->info('Generating customer growth forecasts...');
        
        // Get historical customer growth data
        $historicalGrowth = Customer::where('created_at', '>=', Carbon::now()->subMonths(12))
                                  ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
                                  ->groupBy('year', 'month')
                                  ->orderBy('year', 'desc')
                                  ->orderBy('month', 'desc')
                                  ->get();

        if ($historicalGrowth->isEmpty()) {
            $this->warn('No historical customer data found. Skipping customer growth forecast.');
            return;
        }

        $avgMonthlyGrowth = $historicalGrowth->avg('total');
        
        for ($i = 1; $i <= $months; $i++) {
            $periodStart = Carbon::now()->addMonths($i - 1)->startOfMonth();
            $periodEnd = Carbon::now()->addMonths($i - 1)->endOfMonth();
            
            $existingForecast = Forecast::where('forecast_type', 'customer_growth')
                                      ->where('category', 'combined')
                                      ->where('period_start', $periodStart)
                                      ->where('period_end', $periodEnd)
                                      ->first();

            if ($existingForecast && !$force) {
                $this->line("Customer growth forecast for {$periodStart->format('M Y')} already exists.");
                continue;
            }

            $projectedValue = $avgMonthlyGrowth;
            $confidence = min(80, 40 + ($historicalGrowth->count() * 5));

            $forecastData = [
                'forecast_type' => 'customer_growth',
                'category' => 'combined',
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'target_value' => $projectedValue * 1.2, // Target 20% above projection
                'projected_value' => $projectedValue,
                'unit' => 'count',
                'confidence_level' => $confidence,
                'methodology' => 'moving_average',
                'notes' => 'Auto-generated customer acquisition forecast',
                'status' => 'active',
                'created_by' => null,
            ];

            if ($existingForecast) {
                $existingForecast->update($forecastData);
                $this->line("Updated customer growth forecast for {$periodStart->format('M Y')}");
            } else {
                Forecast::create($forecastData);
                $this->line("Created customer growth forecast for {$periodStart->format('M Y')}");
            }
        }
    }

    private function calculateSeasonalFactors($historicalData)
    {
        // Calculate seasonal adjustment factors based on monthly averages
        $monthlyAverages = [];
        $overallAverage = $historicalData->avg('total');
        
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $historicalData->where('month', $month);
            if ($monthData->count() > 0) {
                $monthlyAverages[$month] = $monthData->avg('total') / $overallAverage;
            } else {
                // Default seasonal factors for Malaysian agriculture
                $defaultFactors = [
                    1 => 0.8, 2 => 0.9, 3 => 1.1, 4 => 1.2, 5 => 1.3, 6 => 1.2,
                    7 => 1.1, 8 => 1.0, 9 => 0.9, 10 => 0.8, 11 => 0.7, 12 => 0.8
                ];
                $monthlyAverages[$month] = $defaultFactors[$month];
            }
        }
        
        return $monthlyAverages;
    }
}
