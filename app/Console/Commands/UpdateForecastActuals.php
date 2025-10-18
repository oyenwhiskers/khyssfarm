<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Forecast;
use App\Models\Harvest;
use App\Models\Sale;
use App\Models\ResellSale;
use App\Models\Customer;
use Carbon\Carbon;

class UpdateForecastActuals extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'forecasts:update-actuals {--silent : Run without output}';

    /**
     * The console command description.
     */
    protected $description = 'Update actual values for completed forecasts based on real data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $silent = $this->option('silent');
        
        if (!$silent) {
            $this->info('Updating actual values for completed forecasts...');
        }

        $completedForecasts = Forecast::where('period_end', '<', Carbon::now())
                                    ->where(function($query) {
                                        $query->whereNull('actual_value')
                                              ->orWhere('updated_at', '<', Carbon::now()->subHours(6));
                                    })
                                    ->get();

        $updated = 0;

        foreach ($completedForecasts as $forecast) {
            $actualValue = $this->calculateActualValue($forecast);
            
            if ($actualValue !== null) {
                $forecast->update([
                    'actual_value' => $actualValue,
                    'status' => 'completed'
                ]);
                $updated++;
                
                if (!$silent) {
                    $this->line("Updated {$forecast->getTypeLabel()} forecast for {$forecast->period_start->format('M Y')}: {$actualValue}");
                }
            }
        }

        if (!$silent) {
            $this->info("Updated {$updated} forecast(s) with actual values.");
        }

        return $updated;
    }

    private function calculateActualValue($forecast)
    {
        switch ($forecast->forecast_type) {
            case 'yield':
                return Harvest::whereBetween('harvest_date', [$forecast->period_start, $forecast->period_end])
                             ->sum('quantity_kg');
                             
            case 'revenue':
                if ($forecast->category === 'resell_business') {
                    return ResellSale::whereBetween('sale_date', [$forecast->period_start, $forecast->period_end])
                                   ->sum('total_sale_amount');
                } else {
                    return Sale::whereBetween('sale_date', [$forecast->period_start, $forecast->period_end])
                             ->where('payment_status', 'paid')
                             ->sum('total_amount');
                }
                
            case 'customer_growth':
                return Customer::whereBetween('created_at', [$forecast->period_start, $forecast->period_end])
                              ->count();
                              
            default:
                return null;
        }
    }
}
