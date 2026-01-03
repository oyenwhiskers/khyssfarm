@extends('layouts.app')

@section('title', 'Worker Details')
@section('page-title', 'Worker Details')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <h2><i class="fas fa-user me-2"></i>{{ $worker->name }}</h2>
        <p class="text-muted mb-0">
            {{ $roles[$worker->role] ?? ucfirst(str_replace('_', ' ', $worker->role)) }}
            @if($worker->status === 'active')
                <span class="badge bg-success ms-2">Active</span>
            @else
                <span class="badge bg-secondary ms-2">Inactive</span>
            @endif
        </p>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('workers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
        <a href="{{ route('workers.edit', $worker) }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Earnings</p>
                        <h4 class="mb-0 text-success">RM{{ number_format($totalEarnings, 2) }}</h4>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Completed Tasks</p>
                        <h4 class="mb-0 text-primary">{{ $completedTasks }}</h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="fas fa-check-circle fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Pending Tasks</p>
                        <h4 class="mb-0 text-warning">{{ $pendingTasks }}</h4>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Tasks</p>
                        <h4 class="mb-0">{{ $tasks->count() }}</h4>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded">
                        <i class="fas fa-tasks fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Task History -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Task History</h5>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('workers.show', array_merge(['worker' => $worker], request()->except('status'))) }}" 
                           class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
                        <a href="{{ route('workers.show', array_merge(['worker' => $worker, 'status' => 'completed'], request()->except('status'))) }}" 
                           class="btn {{ request('status') === 'completed' ? 'btn-primary' : 'btn-outline-secondary' }}">Completed</a>
                        <a href="{{ route('workers.show', array_merge(['worker' => $worker, 'status' => 'in_progress'], request()->except('status'))) }}" 
                           class="btn {{ request('status') === 'in_progress' ? 'btn-primary' : 'btn-outline-secondary' }}">In Progress</a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Task</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Workers</th>
                                <th>My Share</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                @php
                                    $workerCount = $task->workers->count();
                                    $myShare = $workerCount > 0 ? ($task->cost / $workerCount) : 0;
                                @endphp
                                <tr>
                                    <td>{{ $task->work_date->format('M d, Y') }}</td>
                                    <td class="fw-semibold">{{ $task->title }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ \App\Models\Task::TYPES[$task->type] ?? ucfirst($task->type) }}
                                        </span>
                                    </td>
                                    <td>
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
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $workerCount }} worker{{ $workerCount !== 1 ? 's' : '' }}</small>
                                    </td>
                                    <td>
                                        <strong class="text-success">RM{{ number_format($myShare, 2) }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No tasks found for this worker.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Earnings Chart -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Earnings Trend (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="earningsChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Worker Information -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Worker Information</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Name</dt>
                    <dd class="col-7">{{ $worker->name }}</dd>

                    <dt class="col-5">Role</dt>
                    <dd class="col-7">{{ $roles[$worker->role] ?? ucfirst(str_replace('_', ' ', $worker->role)) }}</dd>

                    <dt class="col-5">Contact</dt>
                    <dd class="col-7">{{ $worker->contact ?: '—' }}</dd>

                    <dt class="col-5">Status</dt>
                    <dd class="col-7">
                        @if($worker->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </dd>

                    <dt class="col-5">Joined</dt>
                    <dd class="col-7">{{ $worker->created_at->format('M d, Y') }}</dd>

                    @if($worker->notes)
                        <dt class="col-12 mt-2">Notes</dt>
                        <dd class="col-12">{{ $worker->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Generate Payslip -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Generate Payslip</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('workers.payslip', $worker) }}" method="GET" target="_blank">
                    <div class="mb-3">
                        <label for="year" class="form-label">Year</label>
                        <select name="year" id="year" class="form-select" required>
                            @for($y = now()->year; $y >= now()->year - 2; $y--)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="month" class="form-label">Month</label>
                        <select name="month" id="month" class="form-select" required>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-print me-2"></i>Generate Payslip
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('workers.edit', $worker) }}" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fas fa-edit me-2"></i>Edit Worker
                </a>
                <a href="{{ route('tasks.create', ['worker_id' => $worker->id]) }}" class="btn btn-outline-success w-100 mb-2">
                    <i class="fas fa-plus me-2"></i>Assign New Task
                </a>
                <form action="{{ route('workers.destroy', $worker) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this worker? This will not delete their task history.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="fas fa-trash me-2"></i>Delete Worker
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('earningsChart');
    if (!ctx) return;

    const monthlyData = @json($monthlyEarnings);
    const labels = Object.keys(monthlyData).map(key => {
        const [year, month] = key.split('-');
        return new Date(year, month - 1).toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    });
    const values = Object.values(monthlyData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Earnings (RM)',
                data: values,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'RM' + value.toFixed(2);
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
