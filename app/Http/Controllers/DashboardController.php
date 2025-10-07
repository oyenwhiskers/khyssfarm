<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Harvest;
use App\Models\Sale;
use App\Models\Cost;
use App\Models\Customer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate key metrics
        $totalYield = Harvest::sum('quantity_kg');
        $totalRevenue = Sale::sum('total_amount');
        $totalCosts = Cost::sum('amount');
        $netProfit = $totalRevenue - $totalCosts;
        
        // Calculate average price per kg
        $totalQuantitySold = Sale::sum('quantity_kg');
        $averagePricePerKg = $totalQuantitySold > 0 ? $totalRevenue / $totalQuantitySold : 0;
        
        // Get recent data
        $recentHarvests = Harvest::latest('harvest_date')->take(5)->get();
        $recentSales = Sale::with('customer')->latest('sale_date')->take(5)->get();
        $recentCosts = Cost::latest('date')->take(5)->get();
        
        // Monthly data for charts
        $monthlyRevenue = $this->getMonthlyData('sales', 'total_amount');
        $monthlyCosts = $this->getMonthlyData('costs', 'amount');
        $monthlyYield = $this->getMonthlyData('harvests', 'quantity_kg');
        
        // Top customers
        $topCustomers = Customer::withSum('sales', 'total_amount')
            ->orderBy('sales_sum_total_amount', 'desc')
            ->take(5)
            ->get();
        
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
            'totalRevenue',
            'totalCosts',
            'netProfit',
            'averagePricePerKg',
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
            $monthData = $model::whereYear($dateField, $date->year)
                ->whereMonth($dateField, $date->month)
                ->sum($column);
            
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

        // Average daily yield
        $avgDailyYield = Harvest::avg('quantity_kg') ?: 0;
        
        // Best performing month
        $bestMonth = collect($monthlyYieldComparison)->sortByDesc('yield')->first();
        
        return [
            'monthlyComparison' => collect($monthlyYieldComparison),
            'varietyBreakdown' => $yieldByVarietyWithPercentage,
            'weeklyTrends' => collect($weeklyYield),
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

        // Customer purchase patterns
        $customerPurchasePattern = Customer::withSum('sales', 'total_amount')
            ->withSum('sales', 'quantity_kg')
            ->withCount('sales')
            ->get()
            ->groupBy(function($customer) {
                $totalPurchases = $customer->sales_sum_total_amount ?? 0;
                if ($totalPurchases >= 1000) return 'High Value (RM1000+)';
                if ($totalPurchases >= 500) return 'Medium Value (RM500-999)';
                if ($totalPurchases >= 100) return 'Low Value (RM100-499)';
                return 'New Customer (<RM100)';
            })
            ->map(function($group) {
                return $group->count();
            });

        // Monthly customer acquisition
        $monthlyCustomers = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Customer::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $monthlyCustomers[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }

        // Customer location analytics
        $locationStats = Customer::leftJoin('sales', 'customers.id', '=', 'sales.customer_id')
            ->selectRaw('customers.location, COUNT(DISTINCT customers.id) as customer_count, COALESCE(SUM(sales.total_amount), 0) as total_revenue')
            ->groupBy('customers.location')
            ->whereNotNull('customers.location')
            ->get();

        // Top customers by revenue
        $topCustomersByRevenue = Customer::withSum('sales', 'total_amount')
            ->orderBy('sales_sum_total_amount', 'desc')
            ->take(10)
            ->get();

        // Customer retention rate (customers who made multiple purchases)
        $totalCustomers = Customer::count();
        $repeatCustomers = Customer::withCount('sales')
            ->having('sales_count', '>', 1)
            ->count();
        
        $retentionRate = $totalCustomers > 0 ? round(($repeatCustomers / $totalCustomers) * 100, 1) : 0;

        return [
            'typeDistribution' => $customerTypes,
            'purchasePatterns' => collect($customerPurchasePattern),
            'monthlyAcquisition' => collect($monthlyCustomers),
            'locationStats' => $locationStats,
            'topCustomers' => $topCustomersByRevenue,
            'retentionRate' => $retentionRate,
            'totalCustomers' => $totalCustomers,
            'repeatCustomers' => $repeatCustomers
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
