<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Worker::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('contact', 'like', '%' . $search . '%')
                  ->orWhere('notes', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $workers = $query->latest()->paginate(20);
        $workers->appends($request->query());

        return view('workers.index', [
            'workers' => $workers,
            'roles' => Worker::ROLES,
            'statuses' => Worker::STATUSES,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('workers.create', [
            'roles' => Worker::ROLES,
            'statuses' => Worker::STATUSES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:' . implode(',', array_keys(Worker::ROLES)),
            'contact' => 'nullable|string|max:255',
            'status' => 'required|in:' . implode(',', array_keys(Worker::STATUSES)),
            'notes' => 'nullable|string',
        ]);

        Worker::create($validated);

        return redirect()->route('workers.index')
            ->with('success', 'Worker record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Worker $worker, Request $request)
    {
        $worker->load('tasks.workers');

        // Get filter parameters
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $statusFilter = $request->get('status');

        // Get all tasks for this worker
        $tasksQuery = $worker->tasks();

        if ($statusFilter) {
            $tasksQuery->where('status', $statusFilter);
        }

        $allTasks = $tasksQuery->get();

        // Calculate statistics
        $totalEarnings = $allTasks->sum(function($task) use ($worker) {
            $workerCount = $task->workers->count();
            return $workerCount > 0 ? ($task->cost / $workerCount) : 0;
        });

        $completedTasks = $allTasks->where('status', 'completed')->count();
        $pendingTasks = $allTasks->whereIn('status', ['planned', 'in_progress'])->count();

        // Monthly earnings for chart
        $monthlyEarnings = $allTasks->groupBy(function($task) {
            return $task->work_date->format('Y-m');
        })->map(function($tasks) use ($worker) {
            return $tasks->sum(function($task) use ($worker) {
                $workerCount = $task->workers->count();
                return $workerCount > 0 ? ($task->cost / $workerCount) : 0;
            });
        })->sortKeys()->take(6);

        return view('workers.show', [
            'worker' => $worker,
            'tasks' => $allTasks->take(20),
            'totalEarnings' => $totalEarnings,
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
            'monthlyEarnings' => $monthlyEarnings,
            'roles' => Worker::ROLES,
            'statuses' => Worker::STATUSES,
            'taskStatuses' => \App\Models\Task::STATUSES,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Worker $worker)
    {
        return view('workers.edit', [
            'worker' => $worker,
            'roles' => Worker::ROLES,
            'statuses' => Worker::STATUSES,
        ]);
    }

    /**
     * Generate payslip for a worker for a specific month
     */
    public function payslip(Worker $worker, Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $tasks = $worker->tasks()
            ->whereYear('work_date', $year)
            ->whereMonth('work_date', $month)
            ->with('workers')
            ->get();

        $totalEarnings = $tasks->sum(function($task) use ($worker) {
            $workerCount = $task->workers->count();
            return $workerCount > 0 ? ($task->cost / $workerCount) : 0;
        });

        $monthName = \Carbon\Carbon::create($year, $month, 1)->format('F Y');

        return view('workers.payslip', [
            'worker' => $worker,
            'tasks' => $tasks,
            'totalEarnings' => $totalEarnings,
            'year' => $year,
            'month' => $month,
            'monthName' => $monthName,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Worker $worker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:' . implode(',', array_keys(Worker::ROLES)),
            'contact' => 'nullable|string|max:255',
            'status' => 'required|in:' . implode(',', array_keys(Worker::STATUSES)),
            'notes' => 'nullable|string',
        ]);

        $worker->update($validated);

        return redirect()->route('workers.index')
            ->with('success', 'Worker record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Worker $worker)
    {
        $worker->delete();

        return redirect()->route('workers.index')
            ->with('success', 'Worker record deleted successfully.');
    }
}
