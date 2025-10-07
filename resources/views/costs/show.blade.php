@extends('layouts.app')

@section('title', 'Cost Details')
@section('page-title', 'Cost Details')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Cost Details</h5>
                <div>
                    <a href="{{ route('costs.edit', $cost) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <form action="{{ route('costs.destroy', $cost) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3" 
                                onclick="return confirm('Are you sure you want to delete this cost record?')">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Cost Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td>{{ $cost->date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Category:</strong></td>
                                <td><span class="badge bg-secondary">{{ ucfirst($cost->category) }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Description:</strong></td>
                                <td>{{ $cost->description }}</td>
                            </tr>
                            <tr>
                                <td><strong>Amount:</strong></td>
                                <td><h5 class="text-danger">RM{{ number_format($cost->amount, 2) }}</h5></td>
                            </tr>
                            <tr>
                                <td><strong>Supplier:</strong></td>
                                <td>{{ $cost->supplier ?: 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted">Additional Information</h6>
                        @if($cost->notes)
                            <div class="alert alert-light">
                                <strong>Notes:</strong><br>
                                {{ $cost->notes }}
                            </div>
                        @else
                            <p class="text-muted">No additional notes.</p>
                        @endif
                        
                        <div class="mt-4">
                            <h6 class="text-muted">Timestamps</h6>
                            <small class="text-muted">
                                Created: {{ $cost->created_at->format('F d, Y g:i A') }}<br>
                                Last Updated: {{ $cost->updated_at->format('F d, Y g:i A') }}
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('costs.index') }}" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i>Back to Costs
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
