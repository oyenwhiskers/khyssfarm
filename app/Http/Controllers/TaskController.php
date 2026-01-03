<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Worker;
use App\Services\TaskTitleGeneratorService;
use App\Services\CostSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with('workers');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('work_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('work_date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Collect all tasks for grouped view
        $allTasks = (clone $query)->orderByDesc('work_date')->get();

        // Group tasks by month/year
        $tasksByMonth = $allTasks->groupBy(function ($task) {
            return $task->work_date->format('Y-m');
        })->sortByDesc(function ($group) {
            return $group->first()->work_date;
        });

        // Monthly stats for headers
        $monthlyStats = [];
        $maxMonthlyCount = 0;

        foreach ($tasksByMonth as $monthKey => $monthTasks) {
            $monthCount = $monthTasks->count();
            $monthlyStats[$monthKey] = [
                'date' => $monthTasks->first()->work_date,
                'count' => $monthCount,
                'completed' => $monthTasks->where('status', 'completed')->count(),
                'cost' => $monthTasks->sum('cost'),
            ];

            if ($monthCount > $maxMonthlyCount) {
                $maxMonthlyCount = $monthCount;
            }
        }

        // Paginated list for list view
        $tasks = (clone $query)->orderByDesc('work_date')->paginate(20);
        $tasks->appends($request->query());

        $viewMode = $request->get('view', 'grouped');
        $totalTasks = $allTasks->count();
        $completedTasks = $allTasks->where('status', 'completed')->count();
        $totalCost = $allTasks->sum('cost');

        // Calendar view data
        $calendarMonth = $request->get('month', Carbon::now()->format('Y-m'));
        try {
            $monthDate = Carbon::parse($calendarMonth . '-01');
        } catch (\Exception $e) {
            $monthDate = Carbon::now();
        }

        $monthStart = $monthDate->copy()->startOfMonth();
        $monthEnd = $monthDate->copy()->endOfMonth();
        $gridStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);
        $calendarRange = CarbonPeriod::create($gridStart, $gridEnd);

        $tasksForCalendar = $allTasks->filter(function ($task) use ($monthStart, $monthEnd) {
            return $task->work_date->betweenIncluded($monthStart, $monthEnd);
        });

        $tasksByDay = $tasksForCalendar->groupBy(function ($task) {
            return $task->work_date->toDateString();
        });

        $calendarMonthLabel = $monthStart->format('F Y');
        $calendarPrev = $monthStart->copy()->subMonth()->format('Y-m');
        $calendarNext = $monthStart->copy()->addMonth()->format('Y-m');

        return view('tasks.index', [
            'tasks' => $tasks,
            'tasksByMonth' => $tasksByMonth,
            'monthlyStats' => $monthlyStats,
            'maxMonthlyCount' => $maxMonthlyCount,
            'viewMode' => $viewMode,
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'totalCost' => $totalCost,
            'calendarRange' => $calendarRange,
            'calendarMonthLabel' => $calendarMonthLabel,
            'calendarMonthValue' => $monthStart->format('Y-m'),
            'calendarPrev' => $calendarPrev,
            'calendarNext' => $calendarNext,
            'tasksByDay' => $tasksByDay,
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
            'statuses' => Task::STATUSES,
            'types' => Task::TYPES,
        ]);
    }

    public function create()
    {
        return view('tasks.create', [
            'statuses' => Task::STATUSES,
            'types' => Task::TYPES,
            'workers' => Worker::orderBy('name')->get(),
        ]);
    }

    public function show(Task $task)
    {
        $task->load('workers');

        return view('tasks.show', [
            'task' => $task,
            'statuses' => Task::STATUSES,
            'types' => Task::TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Task::TYPES)),
            'work_date' => 'required|date',
            'status' => 'required|in:' . implode(',', array_keys(Task::STATUSES)),
            'quantity_kg' => 'nullable|numeric|min:0',
            'tree_count' => 'nullable|integer|min:0',
            'rate' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'mark_complete' => 'nullable|boolean',
            'workers' => 'array',
            'workers.*' => 'exists:workers,id',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        // For preset tasks, auto-generate title if not provided
        if (TaskTitleGeneratorService::shouldAutoGenerateTitle($validated['type'])) {
            $workDate = Carbon::parse($validated['work_date']);
            $validated['title'] = TaskTitleGeneratorService::generateTitle($validated['type'], $workDate);
        } elseif (empty($validated['title'])) {
            // Non-preset tasks must have a title
            throw ValidationException::withMessages(['title' => 'Task title is required.']);
        }

        // For harvesting tasks, if mark_complete is checked, set status to completed
        if ($validated['type'] === 'harvesting' && $request->boolean('mark_complete')) {
            $validated['status'] = 'completed';
        }

        $workers = collect($validated['workers'] ?? []);
        $computed = $this->computeCostAndRules($validated['type'], $workers, $validated['quantity_kg'] ?? null, $validated['rate'] ?? null, $validated['tree_count'] ?? null, $validated['cost'] ?? null);

        $task = new Task(array_merge($validated, $computed));

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('tasks', 'public');
            $task->attachment_path = $path;
        }

        $task->save();

        if ($workers->isNotEmpty()) {
            $task->workers()->sync($workers);
        }

        // Sync cost after workers are attached
        CostSyncService::syncTaskCost($task);

        return redirect()->route('tasks.index')->with('success', 'Task recorded successfully.');
    }

    public function edit(Task $task)
    {
        $task->load('workers');

        return view('tasks.edit', [
            'task' => $task,
            'statuses' => Task::STATUSES,
            'types' => Task::TYPES,
            'workers' => Worker::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Task::TYPES)),
            'work_date' => 'required|date',
            'status' => 'required|in:' . implode(',', array_keys(Task::STATUSES)),
            'quantity_kg' => 'nullable|numeric|min:0',
            'tree_count' => 'nullable|integer|min:0',
            'rate' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'workers' => 'array',
            'workers.*' => 'exists:workers,id',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        // For preset tasks, auto-generate title if not provided
        if (TaskTitleGeneratorService::shouldAutoGenerateTitle($validated['type'])) {
            if (empty($validated['title'])) {
                $workDate = Carbon::parse($validated['work_date']);
                $validated['title'] = TaskTitleGeneratorService::generateTitle($validated['type'], $workDate);
            }
        } elseif (empty($validated['title'])) {
            // Non-preset tasks must have a title
            throw ValidationException::withMessages(['title' => 'Task title is required.']);
        }

        $workers = collect($validated['workers'] ?? []);
        $computed = $this->computeCostAndRules($validated['type'], $workers, $validated['quantity_kg'] ?? null, $validated['rate'] ?? null, $validated['tree_count'] ?? null, $validated['cost'] ?? null);

        $task->fill(array_merge($validated, $computed));

        if ($request->hasFile('attachment')) {
            if ($task->attachment_path) {
                Storage::disk('public')->delete($task->attachment_path);
            }
            $path = $request->file('attachment')->store('tasks', 'public');
            $task->attachment_path = $path;
        }

        $task->save();

        $task->workers()->sync($workers);

        // Sync cost after workers are attached
        CostSyncService::syncTaskCost($task);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        if ($task->attachment_path) {
            Storage::disk('public')->delete($task->attachment_path);
        }

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    private function computeCostAndRules(string $type, $workers, $quantityKg, $rate, $treeCount = null, $manualCost = null): array
    {
        $workerCount = $workers ? $workers->count() : 0;

        switch ($type) {
            case 'harvesting':
                if ($workerCount !== 1) {
                    throw ValidationException::withMessages([
                        'workers' => 'Harvesting requires exactly one worker.',
                    ]);
                }
                if ($quantityKg === null || $quantityKg <= 0) {
                    throw ValidationException::withMessages([
                        'quantity_kg' => 'Harvesting requires quantity in kg.',
                    ]);
                }
                if ($rate === null || $rate <= 0) {
                    throw ValidationException::withMessages([
                        'rate' => 'Harvesting requires a rate per kg.',
                    ]);
                }
                $cost = $manualCost !== null ? (float) $manualCost : ((float) $quantityKg * (float) $rate);
                return [
                    'tree_count' => null,
                    'cost' => round($cost, 2),
                ];

            case 'spraying':
            case 'grass_cut':
            case 'sanitation':
                if ($workerCount < 1) {
                    throw ValidationException::withMessages([
                        'workers' => 'At least one worker is required.',
                    ]);
                }
                if ($rate === null || $rate <= 0) {
                    throw ValidationException::withMessages([
                        'rate' => 'A job rate is required.',
                    ]);
                }
                $cost = $manualCost !== null ? (float) $manualCost : ((float) $rate * max($workerCount, 1));
                return [
                    'quantity_kg' => null,
                    'tree_count' => null,
                    'cost' => round($cost, 2),
                ];

            case 'maintenance':
                if ($workerCount < 1) {
                    throw ValidationException::withMessages([
                        'workers' => 'At least one worker is required.',
                    ]);
                }
                if ($rate === null || $rate <= 0) {
                    throw ValidationException::withMessages([
                        'rate' => 'A job rate is required.',
                    ]);
                }
                $cost = $manualCost !== null ? (float) $manualCost : ((float) $rate * max($workerCount, 1));
                return [
                    'quantity_kg' => null,
                    'tree_count' => null,
                    'cost' => round($cost, 2),
                ];

            case 'manuring':
                if ($workerCount < 1) {
                    throw ValidationException::withMessages([
                        'workers' => 'At least one worker is required.',
                    ]);
                }
                if ($treeCount === null || $treeCount <= 0) {
                    throw ValidationException::withMessages([
                        'tree_count' => 'Manuring requires number of trees.',
                    ]);
                }
                if ($rate === null || $rate <= 0) {
                    throw ValidationException::withMessages([
                        'rate' => 'Rate per tree is required.',
                    ]);
                }
                $cost = $manualCost !== null ? (float) $manualCost : ((float) $rate * (float) $treeCount);
                return [
                    'quantity_kg' => null,
                    'tree_count' => (int) $treeCount,
                    'cost' => round($cost, 2),
                ];

            default:
                return [
                    'tree_count' => null,
                ];
        }
    }

    /**
     * Get the next job number for a given task type and date
     * Used for preview of auto-generated titles
     */
    public function getNextJobNumber(Request $request)
    {
        $type = $request->query('type');
        $date = $request->query('date');

        if (!$type || !$date) {
            return response()->json(['error' => 'Missing type or date parameter'], 400);
        }

        // Validate inputs
        if (!in_array($type, array_keys(Task::TYPES))) {
            return response()->json(['error' => 'Invalid task type'], 400);
        }

        try {
            $workDate = Carbon::parse($date);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $jobNumber = TaskTitleGeneratorService::getNextJobNumber($type, $workDate);

        return response()->json(['job_number' => $jobNumber]);
    }
}
