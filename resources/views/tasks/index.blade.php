@extends('layouts.app')

@section('title', 'Task Management')
@section('page-title', 'Task Management')

@section('content')
<style>
    .transition-icon-task {
        transition: transform 0.3s ease;
        display: inline-block;
    }

    .btn-link[aria-expanded="true"] .transition-icon-task {
        transform: rotate(180deg);
    }

    .month-header-task {
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }

    .month-header-task:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    }

    .month-header-task .btn-link {
        padding: 1.1rem !important;
        font-weight: 600;
    }

    .month-header-task .btn-link:focus {
        box-shadow: none;
    }

    .month-label-task {
        font-size: 1.05rem;
        color: #1a1a1a;
        margin-right: 1rem;
    }

    .month-stats-task {
        display: flex;
        gap: 1.75rem;
        align-items: center;
    }

    .stat-box-task {
        text-align: right;
        min-width: 140px;
    }

    .stat-value-task {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.15rem;
        line-height: 1.2;
    }

    .stat-label-task {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #6c757d;
    }

    .stat-count { color: #198754; }
    .stat-completed { color: #0d6efd; }
    .stat-cost { color: #d63384; }

    .record-badge-task {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        background: #e8f4ec;
        color: #0f5132;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .progress-bar-mini-task {
        height: 3px;
        background: linear-gradient(90deg, #198754, #20c997);
        border-radius: 2px;
        margin-top: 0.65rem;
        opacity: 0;
        transition: opacity 0.3s ease;
        width: 100%;
    }

    .progress-bar-mini-inner-task {
        height: 100%;
        background: inherit;
        border-radius: 2px;
        transition: width 0.4s ease;
    }

    .month-header-task:hover .progress-bar-mini-task { opacity: 0.6; }
    .month-header-task.show .progress-bar-mini-task { opacity: 0.8; }

    .task-table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .task-table thead th {
        border: none;
        padding: 0.9rem 0.75rem !important;
        font-weight: 700;
        color: #495057;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .task-table tbody tr {
        border-bottom: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }

    .task-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .task-table tbody td {
        padding: 0.85rem 0.75rem !important;
        vertical-align: middle;
    }

    .month-records-container-task {
        background: linear-gradient(135deg, #fafbfc 0%, #f5f7fa 100%);
        border-left: 4px solid #198754;
    }

    /* Calendar view */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .calendar-day {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        min-height: 140px;
        padding: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }

    .calendar-day:hover {
        box-shadow: 0 6px 18px rgba(0,0,0,0.06);
        transform: translateY(-1px);
    }

    .calendar-day.muted {
        background: #f8f9fa;
        color: #adb5bd;
    }

    .calendar-date {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 700;
        color: #212529;
    }

    .calendar-date .day-number {
        font-size: 1.1rem;
    }

    .calendar-date .day-name {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .calendar-tasks {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .task-chip {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.4rem 0.5rem;
        border-radius: 10px;
        background: #ffffff;
        font-size: 0.9rem;
        color: #212529;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    }

    .type-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
        color: #fff;
        font-weight: 700;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
        min-width: 42px;
        text-transform: uppercase;
    }

    .type-pill.harvesting { background: #2e7d32; }
    .type-pill.spraying { background: #0d6efd; }
    .type-pill.grass_cut { background: #f59f00; }
    .type-pill.sanitation { background: #6c757d; }
    .type-pill.maintenance { background: #6f42c1; }
    .type-pill.manuring { background: #20c997; }
    .type-pill.other { background: #adb5bd; }

    .type-pill-count {
        position: relative;
    }

    .type-pill-count .count-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #dc3545;
        color: white;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.15rem 0.4rem;
        border-radius: 999px;
        min-width: 18px;
        text-align: center;
        line-height: 1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .calendar-empty {
        color: #adb5bd;
        font-size: 0.85rem;
    }

    @media (max-width: 992px) {
        .calendar-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    @media (max-width: 576px) {
        .calendar-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
<div class="row mb-4">
    <div class="col-lg-8">
        <h2><i class="fas fa-list-check me-2"></i>Farm Tasks</h2>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Task
        </a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filters & Search</h6>
    </div>
    <div class="card-body bg-white">
        <form method="GET" action="{{ route('tasks.index') }}" class="row g-3">
            <input type="hidden" name="view" value="{{ $viewMode }}">
            <div class="col-md-4">
                <label for="search" class="form-label fw-semibold">
                    <i class="fas fa-search me-1"></i>Search
                </label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by title or description...">
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label fw-semibold">
                    <i class="fas fa-calendar me-1"></i>From Date
                </label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label fw-semibold">
                    <i class="fas fa-calendar me-1"></i>To Date
                </label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label fw-semibold">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="type" class="form-label fw-semibold">Task Type</label>
                <select class="form-select" id="type" name="type">
                    <option value="">All Types</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12 d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-search me-1"></i>Apply Filters
                </button>
                <a href="{{ route('tasks.index', ['view' => $viewMode]) }}" class="btn btn-outline-secondary px-3">
                    <i class="fas fa-times me-1"></i>Clear All
                </a>
            </div>
        </form>
        @if(request()->hasAny(['search', 'date_from', 'date_to', 'status', 'type']))
            <div class="mt-3 d-flex flex-wrap gap-2">
                <span class="text-muted small">Active Filters:</span>
                @if(request('search'))
                    <span class="badge bg-primary">
                        <i class="fas fa-search me-1"></i>"{{ request('search') }}"
                    </span>
                @endif
                @if(request('date_from'))
                    <span class="badge bg-info">
                        <i class="fas fa-calendar-alt me-1"></i>From: {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                    </span>
                @endif
                @if(request('date_to'))
                    <span class="badge bg-info">
                        <i class="fas fa-calendar-alt me-1"></i>To: {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                    </span>
                @endif
                @if(request('status'))
                    <span class="badge bg-success">
                        <i class="fas fa-tasks me-1"></i>{{ $statuses[request('status')] ?? request('status') }}
                    </span>
                @endif
                @if(request('type'))
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-tag me-1"></i>{{ $types[request('type')] ?? request('type') }}
                    </span>
                @endif
            </div>
        @endif
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2 text-primary"></i>Tasks
            </h5>
            <div class="d-flex align-items-center gap-3">
                <small class="text-muted">{{ $totalTasks }} total | {{ $completedTasks }} completed</small>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('tasks.index', array_merge(request()->query(), ['view' => 'grouped'])) }}" class="btn btn-outline-primary {{ $viewMode === 'grouped' ? 'active' : '' }}">Grouped</a>
                    <a href="{{ route('tasks.index', array_merge(request()->query(), ['view' => 'list'])) }}" class="btn btn-outline-primary {{ $viewMode === 'list' ? 'active' : '' }}">List</a>
                    <a href="{{ route('tasks.index', array_merge(request()->query(), ['view' => 'calendar', 'month' => $calendarMonthValue])) }}" class="btn btn-outline-primary {{ $viewMode === 'calendar' ? 'active' : '' }}">Calendar</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($tasksByMonth->isEmpty() && $viewMode !== 'calendar')
            <div class="text-center text-muted py-5">
                <i class="fas fa-clipboard-list fa-3x mb-3 opacity-50"></i>
                <div class="h5">No tasks found.</div>
                <p>Start by <a href="{{ route('tasks.create') }}" class="text-decoration-none">adding your first task</a>.</p>
            </div>
        @elseif($viewMode === 'list')
            <div class="table-responsive">
                <table class="table align-middle mb-0 task-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Workers</th>
                            <th>Cost</th>
                            <th>Attachment</th>
                            <th class="text-end" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                            <tr>
                                <td class="fw-semibold">{{ $task->title }}</td>
                                <td>{{ $types[$task->type] ?? ucfirst($task->type) }}</td>
                                <td>{{ $task->work_date->format('M d, Y') }}</td>
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
                                    @php $names = $task->workers->pluck('name')->toArray(); @endphp
                                    {{ $names ? implode(', ', $names) : '—' }}
                                </td>
                                <td>{{ $task->cost !== null ? 'RM'.number_format($task->cost, 2) : '—' }}</td>
                                <td>
                                    @if($task->attachment_path)
                                        <a href="{{ asset('storage/'.$task->attachment_path) }}" target="_blank">View</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-info me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this task?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif($viewMode === 'calendar')
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">{{ $calendarMonthLabel }}</h5>
                        <small class="text-muted">Tasks scheduled this month</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('tasks.index', array_merge(request()->query(), ['view' => 'calendar', 'month' => $calendarPrev])) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-chevron-left"></i> Prev
                        </a>
                        <a href="{{ route('tasks.index', array_merge(request()->query(), ['view' => 'calendar', 'month' => $calendarNext])) }}" class="btn btn-outline-secondary btn-sm">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>

                @php
                    $typePrefixes = [
                        'harvesting' => 'HA',
                        'spraying' => 'SP',
                        'grass_cut' => 'GC',
                        'sanitation' => 'SA',
                        'maintenance' => 'MA',
                        'manuring' => 'MN',
                        'other' => 'OT',
                    ];
                @endphp

                <div class="calendar-grid">
                    @foreach($calendarRange as $day)
                        @php
                            $dateKey = $day->toDateString();
                            $dayTasks = $tasksByDay[$dateKey] ?? collect();
                            $isCurrentMonth = $day->betweenIncluded($monthStart, $monthEnd);
                            // Group tasks by type
                            $tasksByType = $dayTasks->groupBy('type');
                        @endphp
                        <div class="calendar-day {{ $isCurrentMonth ? '' : 'muted' }}">
                            <div class="calendar-date">
                                <span class="day-number">{{ $day->format('j') }}</span>
                                <span class="day-name">{{ $day->format('D') }}</span>
                            </div>
                            <div class="calendar-tasks">
                                @if($dayTasks->isEmpty())
                                    <span class="calendar-empty">No tasks</span>
                                @else
                                    @foreach($tasksByType as $taskType => $typeTasks)
                                        @php
                                            $typeLabel = $types[$taskType] ?? ucfirst($taskType);
                                            $prefix = $typePrefixes[$taskType] ?? strtoupper(substr($taskType, 0, 2));
                                            $count = $typeTasks->count();
                                            // Prepare all tasks data for modal
                                            $allTasksData = $typeTasks->map(function($task) use ($types) {
                                                $workerNames = $task->workers->pluck('name')->toArray();
                                                return [
                                                    'title' => $task->title,
                                                    'type' => $types[$task->type] ?? ucfirst($task->type),
                                                    'status' => ucfirst(str_replace('_', ' ', $task->status)),
                                                    'date' => $task->work_date->format('M d, Y'),
                                                    'workers' => $workerNames ? implode(', ', $workerNames) : '—',
                                                    'cost' => $task->cost !== null ? 'RM'.number_format($task->cost, 2) : '—',
                                                    'description' => $task->description ?? '—',
                                                ];
                                            })->toArray();
                                        @endphp
                                        <div class="task-chip">
                                            <span class="type-pill {{ $taskType }} type-pill-count" title="{{ $typeLabel }}">
                                                {{ $prefix }}
                                                @if($count > 1)
                                                    <span class="count-badge">{{ $count }}</span>
                                                @endif
                                            </span>
                                            <button type="button"
                                                    class="btn btn-link p-0 ms-auto text-decoration-none text-muted task-chip-detail"
                                                    title="View details"
                                                    data-tasks='@json($allTasksData)'
                                                    data-type-label="{{ $typeLabel }}">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            @foreach($tasksByMonth as $monthKey => $monthTasks)
                @php
                    $firstTask = $monthTasks->first();
                    $monthDate = $firstTask->work_date;
                    $monthLabel = $monthDate->format('F Y');
                    $monthYear = $monthDate->format('Y-m');
                    $monthCount = $monthlyStats[$monthKey]['count'] ?? $monthTasks->count();
                    $completedCount = $monthlyStats[$monthKey]['completed'] ?? $monthTasks->where('status', 'completed')->count();
                    $monthCost = $monthlyStats[$monthKey]['cost'] ?? $monthTasks->sum('cost');
                    $collapseId = 'month_tasks_' . str_replace('-', '_', $monthYear);
                    $progress = $maxMonthlyCount > 0 ? ($monthCount / $maxMonthlyCount * 100) : 0;
                @endphp

                <div class="border-bottom month-header-task">
                    <button class="btn btn-link w-100 text-start p-0 text-decoration-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" style="color: inherit;">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-chevron-down me-1 transition-icon-task" style="color: #666; font-size: 0.9rem;"></i>
                                <div>
                                    <div class="month-label-task">{{ $monthLabel }}</div>
                                    <span class="record-badge-task">
                                        <i class="fas fa-clipboard-check" style="font-size: 0.75rem;"></i>
                                        {{ $monthCount }} {{ $monthCount === 1 ? 'task' : 'tasks' }}
                                    </span>
                                </div>
                            </div>

                            <div class="month-stats-task">
                                <div class="stat-box-task">
                                    <div class="stat-value-task stat-count">{{ $monthCount }}</div>
                                    <div class="stat-label-task">Total Tasks</div>
                                </div>
                                <div class="stat-box-task">
                                    <div class="stat-value-task stat-completed">{{ $completedCount }}</div>
                                    <div class="stat-label-task">Completed</div>
                                </div>
                                <div class="stat-box-task">
                                    <div class="stat-value-task stat-cost">RM{{ number_format($monthCost, 2) }}</div>
                                    <div class="stat-label-task">Cost</div>
                                </div>
                            </div>
                        </div>
                        <div class="progress-bar-mini-task">
                            <div class="progress-bar-mini-inner-task" style="width: {{ $progress }}%"></div>
                        </div>
                    </button>

                    <div class="collapse month-records-container-task" id="{{ $collapseId }}">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 task-table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Workers</th>
                                        <th>Cost</th>
                                        <th>Attachment</th>
                                        <th class="text-end" style="width: 150px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthTasks as $task)
                                        <tr>
                                            <td class="fw-semibold">{{ $task->title }}</td>
                                            <td>{{ $types[$task->type] ?? ucfirst($task->type) }}</td>
                                            <td>{{ $task->work_date->format('M d, Y') }}</td>
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
                                                @php $names = $task->workers->pluck('name')->toArray(); @endphp
                                                {{ $names ? implode(', ', $names) : '—' }}
                                            </td>
                                            <td>{{ $task->cost !== null ? 'RM'.number_format($task->cost, 2) : '—' }}</td>
                                            <td>
                                                @if($task->attachment_path)
                                                    <a href="{{ asset('storage/'.$task->attachment_path) }}" target="_blank">View</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-info me-1">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this task?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    @if($viewMode === 'list' && $tasks->hasPages())
        <div class="card-footer bg-white">
            {{ $tasks->links() }}
        </div>
    @endif
</div>
<!-- Task Detail Modal -->
<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalTitle">Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="taskModalBody">
                <!-- Content will be dynamically populated -->
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const detailButtons = document.querySelectorAll('.task-chip-detail');
        const modalEl = document.getElementById('taskDetailModal');
        const modal = modalEl ? new bootstrap.Modal(modalEl) : null;

        detailButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                if (!modal) return;
                
                try {
                    const tasksData = JSON.parse(this.dataset.tasks || '[]');
                    const typeLabel = this.dataset.typeLabel || 'Task Details';
                    const modalBody = document.getElementById('taskModalBody');
                    
                    document.getElementById('taskModalTitle').textContent = typeLabel + ' Tasks';
                    
                    if (tasksData.length === 0) {
                        modalBody.innerHTML = '<p class="text-muted">No task details available.</p>';
                    } else if (tasksData.length === 1) {
                        // Single task - show details in simple layout
                        const task = tasksData[0];
                        modalBody.innerHTML = `
                            <dl class="row mb-0">
                                <dt class="col-4">Title</dt><dd class="col-8">${task.title}</dd>
                                <dt class="col-4">Type</dt><dd class="col-8">${task.type}</dd>
                                <dt class="col-4">Status</dt><dd class="col-8">${task.status}</dd>
                                <dt class="col-4">Date</dt><dd class="col-8">${task.date}</dd>
                                <dt class="col-4">Workers</dt><dd class="col-8">${task.workers}</dd>
                                <dt class="col-4">Cost</dt><dd class="col-8">${task.cost}</dd>
                                <dt class="col-4">Description</dt><dd class="col-8">${task.description}</dd>
                            </dl>
                        `;
                    } else {
                        // Multiple tasks - show in cards
                        let html = '<div class="d-flex flex-column gap-3">';
                        tasksData.forEach((task, index) => {
                            html += `
                                <div class="card">
                                    <div class="card-header bg-light py-2">
                                        <strong>Task ${index + 1}: ${task.title}</strong>
                                    </div>
                                    <div class="card-body py-2">
                                        <dl class="row mb-0 small">
                                            <dt class="col-4">Status</dt><dd class="col-8">${task.status}</dd>
                                            <dt class="col-4">Date</dt><dd class="col-8">${task.date}</dd>
                                            <dt class="col-4">Workers</dt><dd class="col-8">${task.workers}</dd>
                                            <dt class="col-4">Cost</dt><dd class="col-8">${task.cost}</dd>
                                            <dt class="col-4">Description</dt><dd class="col-8">${task.description}</dd>
                                        </dl>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        modalBody.innerHTML = html;
                    }
                    
                    modal.show();
                } catch (e) {
                    console.error('Error parsing task data:', e);
                }
            });
        });
    });
</script>
@endsection
@endsection
