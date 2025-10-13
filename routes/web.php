<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HarvestController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\MarketingController;
use App\Services\OpenAIService;

// Dashboard - Main landing page
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Resource routes for all modules
Route::resource('harvests', HarvestController::class);
Route::resource('sales', SaleController::class);
Route::resource('customers', CustomerController::class);
Route::resource('costs', CostController::class);
Route::resource('prices', PriceController::class);
Route::resource('marketing', MarketingController::class);

// Additional routes
Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
Route::get('sales-batches', [SaleController::class, 'batches'])->name('sales.batches');
Route::get('sales-batches/{harvest}', [SaleController::class, 'batchDetail'])->name('sales.batch-detail');
Route::post('marketing/{marketing}/generate-insights', [MarketingController::class, 'generateInsights'])->name('marketing.generate-insights');
Route::get('marketing/channel-recommendations', [MarketingController::class, 'getChannelRecommendations'])->name('marketing.channel-recommendations');
