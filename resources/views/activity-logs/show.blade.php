@extends('layouts.app')

@section('title', 'Activity Log Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Activity Log Details</h1>
        <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Logs
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Log Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">ID:</div>
                        <div class="col-md-9">{{ $activityLog->id }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">User:</div>
                        <div class="col-md-9">
                            @if($activityLog->user)
                                {{ $activityLog->user->name }}
                                <small class="text-muted">({{ $activityLog->user->email }})</small>
                            @else
                                <span class="text-muted">Guest / Unknown</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Event Type:</div>
                        <div class="col-md-9">
                            <span class="badge bg-{{ $activityLog->getEventBadgeColor() }}">
                                {{ $activityLog->getFormattedEventType() }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">IP Address:</div>
                        <div class="col-md-9">
                            <code>{{ $activityLog->ip_address ?? 'N/A' }}</code>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">User Agent:</div>
                        <div class="col-md-9">
                            <small class="text-muted">{{ $activityLog->user_agent ?? 'N/A' }}</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Description:</div>
                        <div class="col-md-9">{{ $activityLog->description ?? 'N/A' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Date/Time:</div>
                        <div class="col-md-9">
                            {{ $activityLog->created_at->format('F d, Y H:i:s') }}
                            <br>
                            <small class="text-muted">{{ $activityLog->created_at->diffForHumans() }}</small>
                        </div>
                    </div>

                    @if($activityLog->properties)
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Additional Data:</div>
                            <div class="col-md-9">
                                <pre class="bg-light p-3 rounded"><code>{{ json_encode($activityLog->properties, JSON_PRETTY_PRINT) }}</code></pre>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Related Logs</h5>
                </div>
                <div class="card-body">
                    @if($activityLog->user)
                        @php
                            $relatedLogs = App\Models\ActivityLog::where('user_id', $activityLog->user_id)
                                ->where('id', '!=', $activityLog->id)
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();
                        @endphp

                        @if($relatedLogs->count() > 0)
                            <p class="text-muted small mb-3">Recent activity from this user:</p>
                            <div class="list-group">
                                @foreach($relatedLogs as $log)
                                    <a href="{{ route('activity-logs.show', $log) }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <span class="badge bg-{{ $log->getEventBadgeColor() }}">
                                                {{ $log->getFormattedEventType() }}
                                            </span>
                                            <small>{{ $log->created_at->diffForHumans() }}</small>
                                        </div>
                                        <small class="text-muted">{{ Str::limit($log->description, 40) }}</small>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No other logs found for this user.</p>
                        @endif
                    @else
                        <p class="text-muted">No user associated with this log.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
