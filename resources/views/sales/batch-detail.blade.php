@extends('layouts.app')

@section('title', 'Batch #' . $harvest->id . ' Details')
@section('page-title', 'Harvest Batch Details')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <h2><i class="fas fa-seedling me-2"></i>Batch #{{ $harvest->id }} - {{ $harvest->harvest_date->format('M d, Y') }}</h2>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('sales.batches') }}" class="btn btn-outline-secondary me-2">
            <i class="fas fa-arrow-left me-2"></i>Back to Batches
        </a>
        @if($harvest->available_quantity > 0)
        <a href="{{ route('sales.create', ['batch_id' => $harvest->id]) }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Sale
        </a>
        @else
        <span class="btn btn-outline-secondary disabled">
            <i class="fas fa-check me-2"></i>Fully Allocated
        </span>
        @endif
    </div>
</div>

<!-- Batch Overview -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Batch Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Harvest Date:</strong></td>
                                <td>{{ $harvest->harvest_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Variety:</strong></td>
                                <td>{{ $harvest->variety ?: 'Mixed' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Field Location:</strong></td>
                                <td>{{ $harvest->field_location ?: 'Not specified' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Total Harvested:</strong></td>
                                <td class="text-success">{{ number_format($harvest->quantity_kg, 2) }} kg</td>
                            </tr>
                            <tr>
                                <td><strong>Total Sold:</strong></td>
                                <td class="text-primary">{{ number_format($harvest->total_quantity_sold, 2) }} kg</td>
                            </tr>
                            <tr>
                                <td><strong>Total Allocated:</strong></td>
                                <td class="text-info">{{ number_format($harvest->total_quantity_allocated, 2) }} kg</td>
                            </tr>
                            <tr>
                                <td><strong>Available:</strong></td>
                                <td class="text-{{ $harvest->available_quantity > 0 ? 'success' : 'danger' }}">{{ number_format($harvest->available_quantity, 2) }} kg</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td><span class="badge bg-{{ $harvest->batch_status_color }}">{{ $harvest->batch_status_label }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($harvest->notes)
                <div class="mt-3">
                    <strong>Notes:</strong>
                    <p class="text-muted mt-1">{{ $harvest->notes }}</p>
                </div>
                @endif

                <div class="mt-3">
                    <strong>Batch Fulfillment Progress:</strong>
                    <div class="progress mt-2" style="height: 15px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $harvest->fulfillment_percentage }}%" 
                             aria-valuenow="{{ $harvest->fulfillment_percentage }}" 
                             aria-valuemin="0" aria-valuemax="100"
                             title="Sold & Paid: {{ $harvest->fulfillment_percentage }}%">
                            @if($harvest->fulfillment_percentage > 10)
                            {{ $harvest->fulfillment_percentage }}% Sold
                            @endif
                        </div>
                        @if($harvest->allocation_percentage > $harvest->fulfillment_percentage)
                        <div class="progress-bar bg-info" role="progressbar" 
                             style="width: {{ $harvest->allocation_percentage - $harvest->fulfillment_percentage }}%" 
                             title="Allocated but Payment Pending: {{ $harvest->allocation_percentage - $harvest->fulfillment_percentage }}%">
                            @if(($harvest->allocation_percentage - $harvest->fulfillment_percentage) > 10)
                            {{ number_format($harvest->allocation_percentage - $harvest->fulfillment_percentage, 1) }}% Pending
                            @endif
                        </div>
                        @endif
                    </div>
                    <div class="mt-2 d-flex justify-content-between">
                        <small class="text-muted">
                            <span class="text-success">■</span> {{ $harvest->fulfillment_percentage }}% sold & paid
                            @if($harvest->allocation_percentage > $harvest->fulfillment_percentage)
                            <span class="text-info ms-2">■</span> {{ number_format($harvest->allocation_percentage - $harvest->fulfillment_percentage, 1) }}% allocated but pending payment
                            @endif
                        </small>
                        <small class="text-muted"><strong>{{ $harvest->allocation_percentage }}% total allocated</strong></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Revenue Summary -->
        <div class="card mb-3">
            <div class="card-body text-center">
                <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                <h4 class="text-success">RM{{ number_format($harvest->total_revenue, 2) }}</h4>
                <p class="mb-0">Total Revenue</p>
                <small class="text-muted">(Paid Sales Only)</small>
            </div>
        </div>

        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                @php
                    $averagePrice = $harvest->total_quantity_sold > 0 
                        ? $harvest->total_revenue / $harvest->total_quantity_sold 
                        : 0;
                @endphp
                <h4 class="text-info">RM{{ number_format($averagePrice, 2) }}</h4>
                <p class="mb-0">Average Price/kg</p>
                <small class="text-muted">(This Batch)</small>
            </div>
        </div>
    </div>
</div>

<!-- Sales from this Batch -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-cash-register me-2"></i>Sales from this Batch ({{ $harvest->sales->count() }})</h5>
    </div>
    <div class="card-body">
        @if($harvest->sales->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Sale Date</th>
                            <th>Customer</th>
                            <th>Quantity (kg)</th>
                            <th>Price/kg</th>
                            <th>Total Amount</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($harvest->sales as $sale)
                        <tr>
                            <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                            <td>
                                @if($sale->customer)
                                    <strong>{{ $sale->customer->name }}</strong>
                                    <br><small class="text-muted">{{ ucfirst($sale->customer->customer_type) }}</small>
                                @else
                                    <span class="text-muted">Walk-in Customer</span>
                                @endif
                            </td>
                            <td><strong>{{ number_format($sale->quantity_kg, 2) }} kg</strong></td>
                            <td>RM{{ number_format($sale->price_per_kg, 2) }}</td>
                            <td><strong>RM{{ number_format($sale->total_amount, 2) }}</strong></td>
                            <td>
                                @switch($sale->payment_status)
                                    @case('paid')
                                        <span class="badge bg-success">Paid</span>
                                        @break
                                    @case('pending')
                                        <span class="badge bg-warning">Pending</span>
                                        @break
                                    @case('partial')
                                        <span class="badge bg-info">Partial</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-info btn-sm rounded-pill" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-outline-success btn-sm rounded-pill" title="Print Receipt" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="{{ route('sales.edit', $sale) }}" class="btn btn-outline-primary btn-sm rounded-pill" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No sales recorded for this batch yet</h5>
                <p class="text-muted">Start selling from this harvest batch to track sales performance.</p>
                @if($harvest->remaining_quantity > 0)
                <a href="{{ route('sales.create', ['batch_id' => $harvest->id]) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Record First Sale
                </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection