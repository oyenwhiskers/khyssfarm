<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HarvestController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ResellController;
use App\Http\Controllers\ResellSaleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\AccountManagementController;

// Welcome page (public)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    // Dashboard - Main landing page
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/trends-data', [DashboardController::class, 'getTrendsData'])->name('dashboard.trends-data');

    // Resource routes for all modules
    Route::resource('harvests', HarvestController::class);
    Route::resource('sales', SaleController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('costs', CostController::class);
    Route::resource('prices', PriceController::class);
    Route::resource('marketing', MarketingController::class);
    Route::resource('resells', ResellController::class);
    Route::resource('workers', WorkerController::class);
    Route::resource('tasks', TaskController::class);

    // Additional routes
    Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    Route::get('sales-batches', [SaleController::class, 'batches'])->name('sales.batches');
    Route::get('sales-batches/{harvest}', [SaleController::class, 'batchDetail'])->name('sales.batch-detail');
    Route::post('marketing/{marketing}/generate-insights', [MarketingController::class, 'generateInsights'])->name('marketing.generate-insights');
    Route::get('marketing/channel-recommendations', [MarketingController::class, 'getChannelRecommendations'])->name('marketing.channel-recommendations');

    // Resell additional routes
    Route::get('resells/{resell}/record-sale', [ResellController::class, 'recordSale'])->name('resells.record-sale');
    Route::post('resells/{resell}/store-sale', [ResellController::class, 'storeSale'])->name('resells.store-sale');

    // Resell Sales management routes
    Route::resource('resell-sales', ResellSaleController::class)->only(['edit', 'update', 'destroy']);

    // Worker additional routes
    Route::get('workers/{worker}/payslip', [WorkerController::class, 'payslip'])->name('workers.payslip');

    // Task API routes
    Route::get('api/tasks/next-job-number', [TaskController::class, 'getNextJobNumber'])->name('tasks.api.next-job-number');

    // Activity Log routes - Admin only
    Route::middleware('can:admin')->group(function () {
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
        Route::post('activity-logs/cleanup', [ActivityLogController::class, 'cleanup'])->name('activity-logs.cleanup');
        Route::get('activity-logs/export/csv', [ActivityLogController::class, 'export'])->name('activity-logs.export');

        // Admin Account Management Routes
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('accounts', [AccountManagementController::class, 'index'])->name('accounts.index');
            Route::get('accounts/{user}', [AccountManagementController::class, 'show'])->name('accounts.show');
            Route::post('accounts/{user}/approve', [AccountManagementController::class, 'approve'])->name('accounts.approve');
            Route::post('accounts/{user}/reject', [AccountManagementController::class, 'reject'])->name('accounts.reject');
            Route::post('accounts/{user}/deactivate', [AccountManagementController::class, 'deactivate'])->name('accounts.deactivate');
            Route::post('accounts/{user}/reactivate', [AccountManagementController::class, 'reactivate'])->name('accounts.reactivate');
            Route::post('accounts/{user}/promote', [AccountManagementController::class, 'promoteToAdmin'])->name('accounts.promote');
            Route::post('accounts/{user}/demote', [AccountManagementController::class, 'demoteToUser'])->name('accounts.demote');
        });
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
