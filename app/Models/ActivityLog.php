<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'event_type',
        'url',
        'http_method',
        'ip_address',
        'user_agent',
        'referer',
        'module',
        'resource_type',
        'resource_id',
        'description',
        'properties',
        'duration_ms',
        'session_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the user that owns the activity log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an activity.
     *
     * @param string $eventType
     * @param int|null $userId
     * @param string|null $description
     * @param array $properties
     * @return static
     */
    public static function log(string $eventType, ?int $userId = null, ?string $description = null, array $properties = []): self
    {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'event_type' => $eventType,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'description' => $description,
            'properties' => $properties,
        ]);
    }

    /**
     * Get event type badge color.
     *
     * @return string
     */
    public function getEventBadgeColor(): string
    {
        return match($this->event_type) {
            'login' => 'success',
            'logout' => 'info',
            'register' => 'primary',
            'failed_login' => 'danger',
            'password_reset' => 'warning',
            'create' => 'success',
            'update' => 'info',
            'delete' => 'danger',
            'page_view' => 'secondary',
            'page_error' => 'danger',
            'action' => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Get formatted event type.
     *
     * @return string
     */
    public function getFormattedEventType(): string
    {
        return ucwords(str_replace('_', ' ', $this->event_type));
    }

    /**
     * Get event icon.
     *
     * @return string
     */
    public function getEventIcon(): string
    {
        return match($this->event_type) {
            'login' => 'fa-sign-in-alt',
            'logout' => 'fa-sign-out-alt',
            'register' => 'fa-user-plus',
            'failed_login' => 'fa-exclamation-triangle',
            'password_reset' => 'fa-key',
            'create' => 'fa-plus-circle',
            'update' => 'fa-edit',
            'delete' => 'fa-trash',
            'page_view' => 'fa-eye',
            'page_error' => 'fa-exclamation-circle',
            'action' => 'fa-bolt',
            default => 'fa-info-circle',
        };
    }

    /**
     * Scope to filter by event type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope to filter by module
     */
    public function scopeInModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get only page views
     */
    public function scopePageViews($query)
    {
        return $query->where('event_type', 'page_view');
    }

    /**
     * Scope to get only CRUD operations
     */
    public function scopeCrudOperations($query)
    {
        return $query->whereIn('event_type', ['create', 'update', 'delete']);
    }

    /**
     * Scope to get authentication events
     */
    public function scopeAuthEvents($query)
    {
        return $query->whereIn('event_type', ['login', 'logout', 'register', 'failed_login', 'password_reset']);
    }

    /**
     * Scope to get slow requests (duration > threshold in milliseconds)
     */
    public function scopeSlowRequests($query, int $thresholdMs = 1000)
    {
        return $query->where('duration_ms', '>', $thresholdMs);
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDuration(): string
    {
        if (!$this->duration_ms) {
            return 'N/A';
        }

        if ($this->duration_ms < 1000) {
            return round($this->duration_ms) . ' ms';
        }

        return round($this->duration_ms / 1000, 2) . ' s';
    }

    /**
     * Get browser from user agent
     */
    public function getBrowser(): string
    {
        if (!$this->user_agent) {
            return 'Unknown';
        }

        if (str_contains($this->user_agent, 'Chrome')) return 'Chrome';
        if (str_contains($this->user_agent, 'Firefox')) return 'Firefox';
        if (str_contains($this->user_agent, 'Safari')) return 'Safari';
        if (str_contains($this->user_agent, 'Edge')) return 'Edge';
        if (str_contains($this->user_agent, 'Opera')) return 'Opera';

        return 'Other';
    }

    /**
     * Get device type from user agent
     */
    public function getDeviceType(): string
    {
        if (!$this->user_agent) {
            return 'Unknown';
        }

        if (str_contains($this->user_agent, 'Mobile')) return 'Mobile';
        if (str_contains($this->user_agent, 'Tablet')) return 'Tablet';

        return 'Desktop';
    }

    /**
     * Get activity statistics for a user
     */
    public static function getStatisticsForUser(int $userId, ?int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $logs = self::where('user_id', $userId)
            ->where('created_at', '>=', $startDate)
            ->get();

        return [
            'total_activities' => $logs->count(),
            'page_views' => $logs->where('event_type', 'page_view')->count(),
            'crud_operations' => $logs->whereIn('event_type', ['create', 'update', 'delete'])->count(),
            'average_duration' => $logs->avg('duration_ms'),
            'most_visited_module' => $logs->groupBy('module')->sortByDesc(fn($group) => $group->count())->keys()->first(),
            'browsers' => $logs->groupBy(fn($log) => (new self($log->toArray()))->getBrowser())->map->count()->toArray(),
            'devices' => $logs->groupBy(fn($log) => (new self($log->toArray()))->getDeviceType())->map->count()->toArray(),
        ];
    }
}
