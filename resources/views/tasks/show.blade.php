@extends('layouts.app')

@section('title', 'Task Details')
@section('page-title', 'Task Details')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <h2><i class="fas fa-eye me-2"></i>{{ $task->title }}</h2>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Tasks
        </a>
        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Task Information -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Task Information</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Title</dt>
                    <dd class="col-sm-8">{{ $task->title }}</dd>

                    <dt class="col-sm-4">Type</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-info">{{ $types[$task->type] ?? ucfirst($task->type) }}</span>
                    </dd>

                    <dt class="col-sm-4">Work Date</dt>
                    <dd class="col-sm-8">{{ $task->work_date->format('M d, Y') }}</dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        @switch($task->status)
                            @case('completed')
                                <span class="badge bg-success">Completed</span>
                                @break
                            @case('in_progress')
                                <span class="badge bg-warning text-dark">In Progress</span>
                                @break
                            @default
                                <span class="badge bg-secondary">Planned</span>
                        @endswitch
                    </dd>

                    @if($task->type === 'harvesting' && $task->quantity_kg)
                        <dt class="col-sm-4">Quantity</dt>
                        <dd class="col-sm-8">{{ number_format($task->quantity_kg, 2) }} kg</dd>
                    @endif

                    @if($task->type === 'manuring' && $task->tree_count)
                        <dt class="col-sm-4">Trees Count</dt>
                        <dd class="col-sm-8">{{ number_format($task->tree_count) }} trees</dd>
                    @endif

                    @if($task->rate)
                        <dt class="col-sm-4">Payment Rate</dt>
                        <dd class="col-sm-8">
                            RM{{ number_format($task->rate, 2) }}
                            @if($task->type === 'harvesting')
                                per kg
                            @elseif($task->type === 'manuring')
                                per tree
                            @else
                                per job per worker
                            @endif
                        </dd>
                    @endif

                    <dt class="col-sm-4">Total Cost</dt>
                    <dd class="col-sm-8">
                        <strong class="text-success">RM{{ number_format($task->cost ?? 0, 2) }}</strong>
                    </dd>

                    @if($task->description)
                        <dt class="col-sm-4">Description</dt>
                        <dd class="col-sm-8">{{ $task->description }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Workers Section -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Workers Assigned</h5>
            </div>
            <div class="card-body">
                @if($task->workers->isEmpty())
                    <p class="text-muted mb-0">No workers assigned to this task.</p>
                @else
                    <div class="row">
                        @foreach($task->workers as $worker)
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center p-3 border rounded bg-light">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        <i class="fas fa-user fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $worker->name }}</h6>
                                        <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $worker->role)) }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if($task->description)
            <!-- Notes Section -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes & Description</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $task->description }}</p>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Quick Stats</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Created</small>
                    <strong>{{ $task->created_at->format('M d, Y H:i') }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Last Updated</small>
                    <strong>{{ $task->updated_at->format('M d, Y H:i') }}</strong>
                </div>
                <div>
                    <small class="text-muted d-block">Workers Count</small>
                    <strong>{{ $task->workers->count() }}</strong>
                </div>
            </div>
        </div>

        <!-- Attachment -->
        @if($task->attachment_path)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i>Attachment</h6>
                </div>
                <div class="card-body">
                    <a href="{{ asset('storage/'.$task->attachment_path) }}" target="_blank" class="btn btn-outline-primary w-100">
                        <i class="fas fa-download me-2"></i>Download File
                    </a>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h6>
            </div>
            <div class="card-body">
                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary w-100 mb-2">
                    <i class="fas fa-edit me-2"></i>Edit Task
                </a>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-trash me-2"></i>Delete Task
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
