<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Harvest;
use App\Models\Sale;
use App\Models\Cost;
use App\Models\Customer;
use App\Models\Resell;
use App\Models\ResellSale;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate key metrics (only paid sales count as revenue)
        $totalYield = Harvest::sum('quantity_kg');
        $farmRevenue = Sale::where('payment_status', 'paid')->sum('total_amount');
        $totalCosts = Cost::sum('amount');
        
        // Resell metrics
        $resellYield = Resell::sum('purchase_quantity_kg'); // Total chili purchased for resell
        $resellPurchaseCosts = Resell::sum('total_purchase_cost');
        $resellRevenue = ResellSale::sum('total_sale_amount');
        $resellProfit = ResellSale::sum('profit_amount');
        
        // Combined metrics
        $totalRevenue = $farmRevenue + $resellRevenue;
        $totalAllCosts = $totalCosts + $resellPurchaseCosts;
        $farmProfit = $farmRevenue - $totalCosts;
        $grandTotalProfit = $farmProfit + $resellProfit;
        
        // Calculate average price per kg (only from paid sales)
        $totalQuantitySold = Sale::where('payment_status', 'paid')->sum('quantity_kg');
        $averagePricePerKg = $totalQuantitySold > 0 ? $farmRevenue / $totalQuantitySold : 0;
        
        // Calculate pending amounts for dashboard display
        $pendingRevenue = Sale::where('payment_status', 'pending')->sum('total_amount');
        $partialRevenue = Sale::where('payment_status', 'partial')->sum('total_amount');
        
        // Get recent data
        $recentHarvests = Harvest::latest('harvest_date')->take(5)->get();
        $recentSales = Sale::with('customer')->latest('sale_date')->take(5)->get();
        $recentCosts = Cost::latest('date')->take(5)->get();
        
        // Monthly data for charts
        $monthlyRevenue = $this->getMonthlyData('sales', 'total_amount');
        $monthlyCosts = $this->getMonthlyData('costs', 'amount');
        $monthlyYield = $this->getMonthlyData('harvests', 'quantity_kg');
        
        // Top customers (including both farm and resell sales)
        $topCustomers = Customer::all()
            ->map(function($customer) {
                $farmRevenue = $customer->sales()->where('payment_status', 'paid')->sum('total_amount');
                $resellRevenue = $customer->resellSales()->sum('total_sale_amount');
                $customer->total_revenue = $farmRevenue + $resellRevenue;
                return $customer;
            })
            ->sortByDesc('total_revenue')
            ->take(5);
        
        // Customer location distribution
        $customersByLocation = Customer::selectRaw('location, COUNT(*) as count')
            ->groupBy('location')
            ->whereNotNull('location')
            ->get();

        // Daily harvest data for the last 30 days
        $dailyHarvests = $this->getDailyHarvestData();

        // Harvest by variety
        $harvestByVariety = Harvest::selectRaw('variety, SUM(quantity_kg) as total')
            ->groupBy('variety')
            ->whereNotNull('variety')
            ->get();

        // === YIELD ANALYTICS ===
        $yieldAnalytics = $this->getYieldAnalytics();
        
        // === CUSTOMER ANALYTICS ===
        $customerAnalytics = $this->getCustomerAnalytics();
        
        // === COST ANALYTICS ===
        $costAnalytics = $this->getCostAnalytics();

        return view('dashboard', compact(
            'totalYield',
            'resellYield',
            'farmRevenue',
            'totalRevenue',
            'totalCosts',
            'totalAllCosts',
            'farmProfit',
            'resellRevenue',
            'resellPurchaseCosts',
            'resellProfit',
            'grandTotalProfit',
            'averagePricePerKg',
            'pendingRevenue',
            'partialRevenue',
            'recentHarvests',
            'recentSales',
            'recentCosts',
            'monthlyRevenue',
            'monthlyCosts',
            'monthlyYield',
            'topCustomers',
            'customersByLocation',
            'dailyHarvests',
            'harvestByVariety',
            'yieldAnalytics',
            'customerAnalytics',
            'costAnalytics'
        ));
    }

    private function getMonthlyData($table, $column, $months = 6)
    {
        $data = [];
        $model = match($table) {
            'sales' => Sale::class,
            'costs' => Cost::class,
            'harvests' => Harvest::class,
        };

        $dateField = match($table) {
            'sales' => 'sale_date',
            'costs' => 'date',
            'harvests' => 'harvest_date',
        };

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $query = $model::whereYear($dateField, $date->year)
                ->whereMonth($dateField, $date->month);
            
            // For sales revenue, only count paid sales
            if ($table === 'sales' && $column === 'total_amount') {
                $query->where('payment_status', 'paid');
            }
            
            $monthData = $query->sum($column);
            
            $data[] = [
                'month' => $date->format('M Y'),
                'value' => $monthData
            ];
        }

        return collect($data);
    }

    private function getDailyHarvestData($days = 30)
    {
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyHarvest = Harvest::whereDate('harvest_date', $date->toDateString())
                ->sum('quantity_kg');
            
            $data[] = [
                'date' => $date->format('M d'),
                'quantity' => $dailyHarvest ?: 0
            ];
        }

        return collect($data);
    }

    private function getYieldAnalytics()
    {
        // Monthly yield comparison
        $monthlyYieldComparison = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $yield = Harvest::whereYear('harvest_date', $date->year)
                ->whereMonth('harvest_date', $date->month)
                ->sum('quantity_kg');
            
            $monthlyYieldComparison[] = [
                'month' => $date->format('M Y'),
                'yield' => $yield
            ];
        }

        // Yield by variety with percentages
        $yieldByVariety = Harvest::selectRaw('variety, SUM(quantity_kg) as total')
            ->groupBy('variety')
            ->whereNotNull('variety')
            ->get();

        $totalYield = $yieldByVariety->sum('total');
        $yieldByVarietyWithPercentage = $yieldByVariety->map(function($item) use ($totalYield) {
            $item->percentage = $totalYield > 0 ? round(($item->total / $totalYield) * 100, 1) : 0;
            return $item;
        });

        // Weekly yield trends (last 8 weeks)
        $weeklyYield = [];
        for ($i = 7; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $yield = Harvest::whereBetween('harvest_date', [$startDate, $endDate])
                ->sum('quantity_kg');
            
            $weeklyYield[] = [
                'week' => 'Week ' . $startDate->format('W'),
                'yield' => $yield
            ];
        }

        // Daily harvest trends (last 30 days with actual harvest data)
        $dailyHarvestTrends = Harvest::where('harvest_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('harvest_date', 'asc')
            ->selectRaw('harvest_date, SUM(quantity_kg) as daily_yield')
            ->groupBy('harvest_date')
            ->get()
            ->map(function($harvest) {
                return [
                    'date' => Carbon::parse($harvest->harvest_date)->format('M d'),
                    'full_date' => Carbon::parse($harvest->harvest_date)->format('Y-m-d'),
                    'yield' => $harvest->daily_yield,
                    'day_name' => Carbon::parse($harvest->harvest_date)->format('D')
                ];
            });

        // Average daily yield
        $avgDailyYield = Harvest::avg('quantity_kg') ?: 0;
        
        // Best performing month
        $bestMonth = collect($monthlyYieldComparison)->sortByDesc('yield')->first();
        
        return [
            'monthlyComparison' => collect($monthlyYieldComparison),
            'varietyBreakdown' => $yieldByVarietyWithPercentage,
            'weeklyTrends' => collect($weeklyYield),
            'dailyHarvestTrends' => $dailyHarvestTrends,
            'avgDailyYield' => $avgDailyYield,
            'bestMonth' => $bestMonth,
            'totalVarieties' => $yieldByVariety->count()
        ];
    }

    private function getCustomerAnalytics()
    {
        // Customer type distribution
        $customerTypes = Customer::selectRaw('customer_type, COUNT(*) as count')
            ->groupBy('customer_type')
            ->get();

        // Customer purchase patterns (including both farm sales and resell sales)
        $customerPurchasePattern = Customer::all()
            ->map(function($customer) {
                $farmSales = $customer->sales()->sum('total_amount');
                $resellSales = $customer->resellSales()->sum('total_sale_amount');
                $totalPurchases = $farmSales + $resellSales;
                
                $customer->total_purchases = $totalPurchases;
                return $customer;
            })
            ->groupBy(function($customer) {
                $totalPurchases = $customer->total_purchases;
                if ($totalPurchases >= 1000) return 'High Value (RM1000+)';
                if ($totalPurchases >= 500) return 'Medium Value (RM500-999)';
                if ($totalPurchases >= 100) return 'Low Value (RM100-499)';
                return 'New Customer (<RM100)';
            })
            ->map(function($group) {
                return $group->count();
            });

        // Monthly customer acquisition with purchase tracking (including resell sales)
        $monthlyCustomers = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            // Total new customers in this month
            $newCustomers = Customer::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            // New customers who actually made purchases (farm or resell)
            $newCustomersWithPurchases = Customer::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where(function($query) {
                    $query->whereHas('sales')
                          ->orWhereHas('resellSales');
                })
                ->count();
            
            $monthlyCustomers[] = [
                'month' => $date->format('M Y'),
                'new_customers' => $newCustomers,
                'customers_with_purchases' => $newCustomersWithPurchases
            ];
        }

        // Customer location analytics (including resell sales)
        $locationStats = Customer::leftJoin('sales', 'customers.id', '=', 'sales.customer_id')
            ->leftJoin('resell_sales', 'customers.id', '=', 'resell_sales.customer_id')
            ->selectRaw('
                customers.location, 
                COUNT(DISTINCT customers.id) as customer_count, 
                COALESCE(SUM(sales.total_amount), 0) + COALESCE(SUM(resell_sales.total_sale_amount), 0) as total_revenue
            ')
            ->groupBy('customers.location')
            ->whereNotNull('customers.location')
            ->get();

        // Top customers by total revenue (farm + resell sales)
        $topCustomersByRevenue = Customer::all()
            ->map(function($customer) {
                $farmRevenue = $customer->sales()->sum('total_amount');
                $resellRevenue = $customer->resellSales()->sum('total_sale_amount');
                $customer->total_revenue = $farmRevenue + $resellRevenue;
                return $customer;
            })
            ->sortByDesc('total_revenue')
            ->take(10);

        // Customer retention rate (customers who made multiple purchases in either farm or resell)
        $totalCustomers = Customer::count();
        $repeatCustomers = Customer::whereHas('sales', function($query) {
                $query->havingRaw('COUNT(*) > 1');
            })
            ->orWhereHas('resellSales', function($query) {
                $query->havingRaw('COUNT(*) > 1');
            })
            ->orWhere(function($query) {
                $query->whereHas('sales')
                      ->whereHas('resellSales');
            })
            ->count();
        
        $retentionRate = $totalCustomers > 0 ? round(($repeatCustomers / $totalCustomers) * 100, 1) : 0;

        // Customer acquisition channels with conversion data (including resell sales)
        $sourceDistribution = Customer::selectRaw('
                source, 
                COUNT(*) as total_customers,
                COUNT(CASE WHEN EXISTS (
                    SELECT 1 FROM sales WHERE sales.customer_id = customers.id
                    UNION
                    SELECT 1 FROM resell_sales WHERE resell_sales.customer_id = customers.id
                ) THEN 1 END) as customers_with_purchases
            ')
            ->whereNotNull('source')
            ->groupBy('source')
            ->get()
            ->map(function($item) {
                $sourceOptions = Customer::getSourceOptions();
                $item->label = $sourceOptions[$item->source] ?? $item->source;
                $item->conversion_rate = $item->total_customers > 0 ? round(($item->customers_with_purchases / $item->total_customers) * 100, 1) : 0;
                return $item;
            });

        // Add customers with no source specified (including resell sales)
        $noSourceCustomers = Customer::selectRaw('
                COUNT(*) as total_customers,
                COUNT(CASE WHEN EXISTS (
                    SELECT 1 FROM sales WHERE sales.customer_id = customers.id
                    UNION
                    SELECT 1 FROM resell_sales WHERE resell_sales.customer_id = customers.id
                ) THEN 1 END) as customers_with_purchases
            ')
            ->whereNull('source')
            ->first();
            
        if ($noSourceCustomers && $noSourceCustomers->total_customers > 0) {
            $conversionRate = round(($noSourceCustomers->customers_with_purchases / $noSourceCustomers->total_customers) * 100, 1);
            $sourceDistribution->push((object)[
                'source' => 'unknown',
                'label' => 'Not Specified',
                'total_customers' => $noSourceCustomers->total_customers,
                'customers_with_purchases' => $noSourceCustomers->customers_with_purchases,
                'conversion_rate' => $conversionRate
            ]);
        }

        return [
            'typeDistribution' => $customerTypes,
            'purchasePatterns' => collect($customerPurchasePattern),
            'monthlyAcquisition' => collect($monthlyCustomers),
            'locationStats' => $locationStats,
            'topCustomers' => $topCustomersByRevenue,
            'retentionRate' => $retentionRate,
            'totalCustomers' => $totalCustomers,
            'repeatCustomers' => $repeatCustomers,
            'sourceDistribution' => $sourceDistribution
        ];
    }

    private function getCostAnalytics()
    {
        // Monthly cost trends
        $monthlyCostTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $totalCost = Cost::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');
            
            $monthlyCostTrends[] = [
                'month' => $date->format('M Y'),
                'cost' => $totalCost
            ];
        }

        // Cost by category
        $costByCategory = Cost::selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        $totalCosts = $costByCategory->sum('total');
        $costByCategoryWithPercentage = $costByCategory->map(function($item) use ($totalCosts) {
            $item->percentage = $totalCosts > 0 ? round(($item->total / $totalCosts) * 100, 1) : 0;
            return $item;
        });

        // Weekly cost analysis (last 8 weeks)
        $weeklyCosts = [];
        for ($i = 7; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $cost = Cost::whereBetween('date', [$startDate, $endDate])
                ->sum('amount');
            
            $weeklyCosts[] = [
                'week' => 'Week ' . $startDate->format('W'),
                'cost' => $cost
            ];
        }

        // Cost efficiency metrics
        $avgDailyCost = Cost::avg('amount') ?: 0;
        $highestCostCategory = $costByCategory->sortByDesc('total')->first();
        
        // Cost vs Revenue ratio
        $totalRevenue = Sale::sum('total_amount');
        $totalCostAmount = Cost::sum('amount');
        $costRevenueRatio = $totalRevenue > 0 ? round(($totalCostAmount / $totalRevenue) * 100, 1) : 0;

        return [
            'monthlyTrends' => collect($monthlyCostTrends),
            'categoryBreakdown' => $costByCategoryWithPercentage,
            'weeklyAnalysis' => collect($weeklyCosts),
            'avgDailyCost' => $avgDailyCost,
            'highestCategory' => $highestCostCategory,
            'costRevenueRatio' => $costRevenueRatio,
            'totalCategories' => $costByCategory->count()
        ];
    }
}
