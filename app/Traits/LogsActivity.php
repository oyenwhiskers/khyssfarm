<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    protected static function bootLogsActivity(): void
    {
        // Log when a model is created
        static::created(function ($model) {
            if (Auth::check()) {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'event_type' => 'create',
                    'module' => $model->getTable(),
                    'resource_type' => class_basename($model),
                    'resource_id' => $model->getKey(),
                    'description' => Auth::user()->name . ' created ' . class_basename($model) . ' #' . $model->getKey(),
                    'properties' => [
                        'attributes' => $model->getAttributes(),
                    ],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => session()->getId(),
                ]);
            }
        });

        // Log when a model is updated
        static::updated(function ($model) {
            if (Auth::check()) {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'event_type' => 'update',
                    'module' => $model->getTable(),
                    'resource_type' => class_basename($model),
                    'resource_id' => $model->getKey(),
                    'description' => Auth::user()->name . ' updated ' . class_basename($model) . ' #' . $model->getKey(),
                    'properties' => [
                        'old' => $model->getOriginal(),
                        'new' => $model->getAttributes(),
                        'changes' => $model->getChanges(),
                    ],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => session()->getId(),
                ]);
            }
        });

        // Log when a model is deleted
        static::deleted(function ($model) {
            if (Auth::check()) {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'event_type' => 'delete',
                    'module' => $model->getTable(),
                    'resource_type' => class_basename($model),
                    'resource_id' => $model->getKey(),
                    'description' => Auth::user()->name . ' deleted ' . class_basename($model) . ' #' . $model->getKey(),
                    'properties' => [
                        'attributes' => $model->getAttributes(),
                    ],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => session()->getId(),
                ]);
            }
        });
    }

    /**
     * Get all activity logs for this model instance
     */
    public function activityLogs()
    {
        return ActivityLog::where('resource_type', class_basename($this))
            ->where('resource_id', $this->getKey())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get the latest activity log for this model instance
     */
    public function latestActivity()
    {
        return ActivityLog::where('resource_type', class_basename($this))
            ->where('resource_id', $this->getKey())
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
