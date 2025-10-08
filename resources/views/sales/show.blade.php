@extends('layouts.app')

@section('title', 'Sale Details')
@section('page-title', 'Sale Details')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Sale Details</h5>
                <div>
                    <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-success btn-sm rounded-pill px-3" target="_blank">
                        <i class="fas fa-print me-1"></i>Print Receipt
                    </a>
                    <a href="{{ route('sales.edit', $sale) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <form action="{{ route('sales.destroy', $sale) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3" 
                                onclick="return confirm('Are you sure you want to delete this sale record?')">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Sale Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Sale Date:</strong></td>
                                <td>{{ $sale->sale_date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Quantity:</strong></td>
                                <td>{{ number_format($sale->quantity_kg, 2) }} kg</td>
                            </tr>
                            <tr>
                                <td><strong>Price per kg:</strong></td>
                                <td>RM{{ number_format($sale->price_per_kg, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Amount:</strong></td>
                                <td><h5 class="text-success">RM{{ number_format($sale->total_amount, 2) }}</h5></td>
                            </tr>
                            <tr>
                                <td><strong>Variety:</strong></td>
                                <td>{{ $sale->variety ?: 'Mixed' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Payment Status:</strong></td>
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
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted">Customer Information</h6>
                        @if($sale->customer)
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $sale->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>{{ ucfirst($sale->customer->customer_type) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $sale->customer->phone ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $sale->customer->email ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Location:</strong></td>
                                    <td>{{ $sale->customer->location ?: 'N/A' }}</td>
                                </tr>
                            </table>
                            <a href="{{ route('customers.show', $sale->customer) }}" class="btn btn-outline-info btn-sm rounded-pill px-3">
                                <i class="fas fa-user me-1"></i>View Customer Details
                            </a>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                This was a walk-in customer sale.
                            </div>
                        @endif
                    </div>
                </div>
                
                @if($sale->notes)
                <div class="mt-4">
                    <h6 class="text-muted">Notes</h6>
                    <div class="alert alert-light">
                        {{ $sale->notes }}
                    </div>
                </div>
                @endif
                
                <div class="mt-4">
                    <h6 class="text-muted">Timestamps</h6>
                    <small class="text-muted">
                        Created: {{ $sale->created_at->format('F d, Y g:i A') }}<br>
                        Last Updated: {{ $sale->updated_at->format('F d, Y g:i A') }}
                    </small>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i>Back to Sales
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
