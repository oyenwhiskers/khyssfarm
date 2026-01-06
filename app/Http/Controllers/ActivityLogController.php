<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by event type
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by HTTP method
        if ($request->filled('http_method')) {
            $query->where('http_method', $request->http_method);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by IP, description, or URL
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(50)->withQueryString();

        // Get distinct values for filters
        $eventTypes = ActivityLog::select('event_type')
            ->distinct()
            ->orderBy('event_type')
            ->pluck('event_type');

        $modules = ActivityLog::select('module')
            ->distinct()
            ->whereNotNull('module')
            ->orderBy('module')
            ->pluck('module');

        $httpMethods = ActivityLog::select('http_method')
            ->distinct()
            ->whereNotNull('http_method')
            ->orderBy('http_method')
            ->pluck('http_method');

        // Get users for filter
        $users = \App\Models\User::orderBy('name')->get();

        // Get statistics
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'page_views' => ActivityLog::where('event_type', 'page_view')->count(),
            'crud_operations' => ActivityLog::whereIn('event_type', ['create', 'update', 'delete'])->count(),
            'avg_duration' => ActivityLog::whereNotNull('duration_ms')->avg('duration_ms'),
        ];

        return view('activity-logs.index', compact('logs', 'eventTypes', 'modules', 'httpMethods', 'users', 'stats'));
    }

    /**
     * Display the specified activity log.
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');
        return view('activity-logs.show', compact('activityLog'));
    }

    /**
     * Delete old activity logs.
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $date = now()->subDays($request->days);
        $count = ActivityLog::where('created_at', '<', $date)->delete();

        return redirect()->route('activity-logs.index')
            ->with('success', "Deleted {$count} activity logs older than {$request->days} days.");
    }

    /**
     * Export activity logs.
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        $filename = 'activity_logs_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'User', 'Event Type', 'Module', 'HTTP Method', 
                'URL', 'IP Address', 'Browser', 'Device', 
                'Duration', 'Description', 'Date/Time'
            ]);

            // Add data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user ? $log->user->name : 'Guest',
                    $log->getFormattedEventType(),
                    $log->module ?? 'N/A',
                    $log->http_method ?? 'N/A',
                    $log->url ?? 'N/A',
                    $log->ip_address,
                    $log->getBrowser(),
                    $log->getDeviceType(),
                    $log->getFormattedDuration(),
                    $log->description,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display analytics and insights.
     */
    public function analytics(Request $request)
    {
        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);

        // Activity by event type
        $activityByType = ActivityLog::where('created_at', '>=', $startDate)
            ->selectRaw('event_type, COUNT(*) as count')
            ->groupBy('event_type')
            ->orderByDesc('count')
            ->get();

        // Activity by module
        $activityByModule = ActivityLog::where('created_at', '>=', $startDate)
            ->whereNotNull('module')
            ->selectRaw('module, COUNT(*) as count')
            ->groupBy('module')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Activity by user (top 10)
        $topUsers = ActivityLog::where('created_at', '>=', $startDate)
            ->with('user')
            ->selectRaw('user_id, COUNT(*) as count')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Activity by hour of day
        $activityByHour = ActivityLog::where('created_at', '>=', $startDate)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Browser statistics
        $browserStats = ActivityLog::where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(fn($log) => $log->getBrowser())
            ->map->count()
            ->sortDesc();

        // Device statistics
        $deviceStats = ActivityLog::where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(fn($log) => $log->getDeviceType())
            ->map->count()
            ->sortDesc();

        // Slow requests (> 2 seconds)
        $slowRequests = ActivityLog::where('created_at', '>=', $startDate)
            ->where('duration_ms', '>', 2000)
            ->orderByDesc('duration_ms')
            ->limit(20)
            ->get();

        // Daily activity trend
        $dailyTrend = ActivityLog::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('activity-logs.analytics', compact(
            'activityByType',
            'activityByModule',
            'topUsers',
            'activityByHour',
            'browserStats',
            'deviceStats',
            'slowRequests',
            'dailyTrend',
            'days'
        ));
    }
}
