@extends('layouts.app')

@section('title', 'Customer Details')
@section('page-title', 'Customer Details')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Details</h5>
                <div>
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3" 
                                onclick="return confirm('Are you sure you want to delete this customer?')">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Customer Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $customer->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td>
                                    @switch($customer->customer_type)
                                        @case('individual')
                                            <span class="badge bg-warning">Individual</span>
                                            @break
                                        @case('retailer')
                                            <span class="badge bg-success">Retailer</span>
                                            @break
                                        @case('wholesaler')
                                            <span class="badge bg-info">Wholesaler</span>
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                            @if($customer->source)
                            <tr>
                                <td><strong>Source:</strong></td>
                                <td>
                                    <span class="badge bg-primary">{{ \App\Models\Customer::getSourceOptions()[$customer->source] ?? $customer->source }}</span>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $customer->phone ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $customer->email ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Location:</strong></td>
                                <td>{{ $customer->location ?: 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted">Purchase Summary</h6>
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h4 class="text-success">RM{{ number_format($customer->total_purchases, 2) }}</h4>
                                <p class="mb-1">Total Purchases</p>
                                <small class="text-muted">{{ number_format($customer->total_quantity, 2) }} kg purchased</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($customer->address)
                <div class="mt-4">
                    <h6 class="text-muted">Address</h6>
                    <div class="alert alert-light">
                        {{ $customer->address }}
                    </div>
                </div>
                @endif
                
                @if($customer->notes)
                <div class="mt-4">
                    <h6 class="text-muted">Notes</h6>
                    <div class="alert alert-light">
                        {{ $customer->notes }}
                    </div>
                </div>
                @endif
                
                <!-- Recent Sales -->
                @if($customer->sales->count() > 0)
                <div class="mt-4">
                    <h6 class="text-muted">Recent Sales</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->sales->take(5) as $sale)
                                <tr>
                                    <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                                    <td>{{ number_format($sale->quantity_kg, 2) }} kg</td>
                                    <td>RM{{ number_format($sale->total_amount, 2) }}</td>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($customer->sales->count() > 5)
                    <small class="text-muted">Showing 5 of {{ $customer->sales->count() }} sales</small>
                    @endif
                </div>
                @endif
                
                <div class="mt-4">
                    <h6 class="text-muted">Timestamps</h6>
                    <small class="text-muted">
                        Created: {{ $customer->created_at->format('F d, Y g:i A') }}<br>
                        Last Updated: {{ $customer->updated_at->format('F d, Y g:i A') }}
                    </small>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i>Back to Customers
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
