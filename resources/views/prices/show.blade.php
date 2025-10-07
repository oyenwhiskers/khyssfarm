@extends('layouts.app')

@section('title', 'Price Details')
@section('page-title', 'Price Details')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Price Details</h5>
                <div>
                    <a href="{{ route('prices.edit', $price) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <form action="{{ route('prices.destroy', $price) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3" 
                                onclick="return confirm('Are you sure you want to delete this price record?')">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Price Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Variety:</strong></td>
                                <td>{{ $price->variety ?: 'All Varieties' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Customer Type:</strong></td>
                                <td>
                                    @switch($price->customer_type)
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
                            <tr>
                                <td><strong>Price per kg:</strong></td>
                                <td><h5 class="text-success">RM{{ number_format($price->price_per_kg, 2) }}</h5></td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($price->is_active && $price->effective_from <= now() && (!$price->effective_to || $price->effective_to >= now()))
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted">Validity Period</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Effective From:</strong></td>
                                <td>{{ $price->effective_from->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Effective To:</strong></td>
                                <td>{{ $price->effective_to ? $price->effective_to->format('F d, Y') : 'Ongoing' }}</td>
                            </tr>
                            @if($price->effective_to)
                            <tr>
                                <td><strong>Duration:</strong></td>
                                <td>{{ $price->effective_from->diffInDays($price->effective_to) }} days</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                @if($price->notes)
                <div class="mt-4">
                    <h6 class="text-muted">Notes</h6>
                    <div class="alert alert-light">
                        {{ $price->notes }}
                    </div>
                </div>
                @endif
                
                <div class="mt-4">
                    <h6 class="text-muted">Timestamps</h6>
                    <small class="text-muted">
                        Created: {{ $price->created_at->format('F d, Y g:i A') }}<br>
                        Last Updated: {{ $price->updated_at->format('F d, Y g:i A') }}
                    </small>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('prices.index') }}" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i>Back to Prices
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
