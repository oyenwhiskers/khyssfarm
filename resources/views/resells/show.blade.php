@extends('layouts.app')

@section('title', 'Resell Details #' . $resell->id)
@section('page-title', 'Resell Details')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h2><i class="fas fa-exchange-alt me-2"></i>Resell Record #{{ $resell->id }}</h2>
            <p class="text-muted">{{ $resell->purchase_date->format('M d, Y') }} - {{ $resell->status_label }}</p>
        </div>
        <div class="col-lg-4 text-end">
            <a href="{{ route('resells.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            @if($resell->remaining_quantity > 0)
            <a href="{{ route('resells.record-sale', $resell) }}" class="btn btn-success me-2">
                <i class="fas fa-dollar-sign me-2"></i>Record Sale
            </a>
            @endif
            <a href="{{ route('resells.edit', $resell) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Purchase Information -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Purchase Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Supplier:</strong><br>
                            <span class="text-dark">{{ $resell->supplier_name }}</span>
                        </div>
                        <div class="col-6">
                            <strong>Contact:</strong><br>
                            <span class="text-muted">{{ $resell->supplier_contact ?: 'Not provided' }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Purchase Date:</strong><br>
                            <span class="text-dark">{{ $resell->purchase_date->format('M d, Y') }}</span>
                        </div>
                        <div class="col-6">
                            <strong>Quantity:</strong><br>
                            <span class="text-dark">{{ number_format($resell->purchase_quantity_kg, 2) }} kg</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Price per KG:</strong><br>
                            <span class="text-danger fw-bold">RM{{ number_format($resell->purchase_price_per_kg, 2) }}</span>
                        </div>
                        <div class="col-6">
                            <strong>Total Cost:</strong><br>
                            <span class="text-danger fw-bold">RM{{ number_format($resell->total_purchase_cost, 2) }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Total Sold:</strong><br>
                            <span class="text-dark">{{ number_format($resell->total_quantity_sold, 2) }} kg</span>
                        </div>
                        <div class="col-6">
                            <strong>Remaining:</strong><br>
                            <span class="text-{{ $resell->remaining_quantity > 0 ? 'warning' : 'success' }}">{{ number_format($resell->remaining_quantity, 2) }} kg</span>
                        </div>
                    </div>

                    @if($resell->variety || $resell->quality_grade)
                    <div class="row mb-3">
                        @if($resell->variety)
                        <div class="col-6">
                            <strong>Variety:</strong><br>
                            <span class="text-dark">{{ $resell->variety }}</span>
                        </div>
                        @endif
                        @if($resell->quality_grade)
                        <div class="col-6">
                            <strong>Quality Grade:</strong><br>
                            <span class="badge bg-info">{{ $resell->quality_grade }}</span>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($resell->purchase_notes)
                    <div class="mb-0">
                        <strong>Notes:</strong><br>
                        <p class="text-muted mb-0">{{ $resell->purchase_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sales Information -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i>Sales Information</h5>
                </div>
                <div class="card-body">
                    @if($resell->resellSales->count() > 0)
                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Total Sales:</strong><br>
                                <span class="text-dark">{{ $resell->resellSales->count() }} transaction(s)</span>
                            </div>
                            <div class="col-6">
                                <strong>Status:</strong><br>
                                <span class="badge bg-{{ $resell->status_color }}">{{ $resell->status_label }}</span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Total Revenue:</strong><br>
                                <span class="text-success fw-bold">RM{{ number_format($resell->total_sale_amount, 2) }}</span>
                            </div>
                            <div class="col-6">
                                <strong>Avg. Profit Margin:</strong><br>
                                <span class="text-{{ $resell->average_profit_margin > 0 ? 'success' : 'danger' }} fw-bold">{{ number_format($resell->average_profit_margin, 1) }}%</span>
                            </div>
                        </div>

                        <!-- Sales List -->
                        <div class="mb-3">
                            <strong>Sales Transactions:</strong>
                            <div class="mt-2" style="max-height: 300px; overflow-y: auto;">
                                @foreach($resell->resellSales as $sale)
                                <div class="border rounded p-3 mb-2">
                                    <div class="row">
                                        <div class="col-8">
                                            <h6 class="mb-1">{{ $sale->sale_date->format('M d, Y') }}</h6>
                                            <small class="text-muted">
                                                <strong>Customer:</strong> {{ $sale->customer->name ?? 'Walk-in' }}
                                                @if($sale->customer && $sale->customer->phone)
                                                    <br><strong>Phone:</strong> {{ $sale->customer->phone }}
                                                @endif
                                            </small>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="text-success fw-bold">RM{{ number_format($sale->total_sale_amount, 2) }}</div>
                                            <small class="text-muted">{{ number_format($sale->sale_quantity_kg, 2) }}kg @ RM{{ number_format($sale->sale_price_per_kg, 2) }}/kg</small>
                                            <br><small class="text-{{ $sale->profit_amount > 0 ? 'success' : 'danger' }}">
                                                Profit: RM{{ number_format($sale->profit_amount, 2) }} ({{ number_format($sale->profit_margin_percentage, 1) }}%)
                                            </small>
                                        </div>
                                    </div>
                                    @if($sale->sale_notes)
                                    <div class="mt-2">
                                        <small class="text-muted"><strong>Notes:</strong> {{ $sale->sale_notes }}</small>
                                    </div>
                                    @endif
                                    <div class="mt-2 d-flex gap-1">
                                        <a href="{{ route('resell-sales.edit', $sale) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteSaleModal{{ $sale->id }}">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Not sold yet</h5>
                            <p class="text-muted">This purchase hasn't been sold yet.</p>
                            @if($resell->remaining_quantity > 0)
                            <a href="{{ route('resells.record-sale', $resell) }}" class="btn btn-success">
                                <i class="fas fa-dollar-sign me-2"></i>Record Sale
                            </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Profit Analysis -->
    @if($resell->resellSales->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Profit Analysis</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="p-3 border rounded">
                                <h3 class="text-danger mb-1">RM{{ number_format($resell->total_purchase_cost, 2) }}</h3>
                                <p class="text-muted mb-0">Total Investment</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 border rounded">
                                <h3 class="text-success mb-1">RM{{ number_format($resell->total_sale_amount, 2) }}</h3>
                                <p class="text-muted mb-0">Total Revenue</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 border rounded">
                                <h3 class="text-{{ $resell->total_profit > 0 ? 'success' : 'danger' }} mb-1">RM{{ number_format($resell->total_profit, 2) }}</h3>
                                <p class="text-muted mb-0">Total Profit</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 border rounded">
                                <h3 class="text-{{ $resell->average_profit_margin > 0 ? 'success' : 'danger' }} mb-1">{{ number_format($resell->average_profit_margin, 1) }}%</h3>
                                <p class="text-muted mb-0">Avg Profit Margin</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Purchase Summary:</h6>
                            <ul class="list-unstyled">
                                <li><strong>Purchase Price:</strong> RM{{ number_format($resell->purchase_price_per_kg, 2) }}/kg</li>
                                <li><strong>Total Purchased:</strong> {{ number_format($resell->purchase_quantity_kg, 2) }} kg</li>
                                <li><strong>Total Investment:</strong> RM{{ number_format($resell->total_purchase_cost, 2) }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Sales Summary:</h6>
                            <ul class="list-unstyled">
                                <li><strong>Transactions:</strong> {{ $resell->resellSales->count() }}</li>
                                <li><strong>Total Sold:</strong> {{ number_format($resell->total_quantity_sold, 2) }} kg</li>
                                <li><strong>Remaining:</strong> {{ number_format($resell->remaining_quantity, 2) }} kg</li>
                            </ul>
                        </div>
                    </div>

                    @if($resell->remaining_quantity > 0)
                    <div class="alert alert-info mt-3">
                        <h6><i class="fas fa-lightbulb me-2"></i>Potential Additional Profit:</h6>
                        @php
                            $latestSale = $resell->resellSales->first();
                            $latestPrice = $latestSale ? $latestSale->sale_price_per_kg : 0;
                        @endphp
                        @if($latestPrice > 0)
                            <p class="mb-0">If you sell the remaining {{ number_format($resell->remaining_quantity, 2) }} kg at the latest sale price (RM{{ number_format($latestPrice, 2) }}/kg), you could earn an additional <strong>RM{{ number_format($resell->remaining_quantity * ($latestPrice - $resell->purchase_price_per_kg), 2) }}</strong> profit.</p>
                        @else
                            <p class="mb-0">You have {{ number_format($resell->remaining_quantity, 2) }} kg remaining to sell.</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Confirmation Modals -->
@if($resell->resellSales->count() > 0)
    @foreach($resell->resellSales as $sale)
        <div class="modal fade" id="deleteSaleModal{{ $sale->id }}" tabindex="-1" aria-labelledby="deleteSaleModalLabel{{ $sale->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteSaleModalLabel{{ $sale->id }}">
                            <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                            Delete Sale Transaction
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>Are you sure you want to delete this sale?</strong>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Date:</strong> {{ $sale->sale_date->format('M d, Y') }}<br>
                                <strong>Customer:</strong> {{ $sale->customer->name ?? 'Walk-in' }}<br>
                                <strong>Quantity:</strong> {{ number_format($sale->sale_quantity_kg, 2) }} kg
                            </div>
                            <div class="col-md-6">
                                <strong>Price:</strong> RM{{ number_format($sale->sale_price_per_kg, 2) }}/kg<br>
                                <strong>Total:</strong> RM{{ number_format($sale->total_sale_amount, 2) }}<br>
                                <strong>Profit:</strong> RM{{ number_format($sale->profit_amount, 2) }}
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                This action cannot be undone. The sale will be permanently removed and the purchase status will be updated accordingly.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </button>
                        <form action="{{ route('resell-sales.destroy', $sale) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i> Delete Sale
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

@endsection