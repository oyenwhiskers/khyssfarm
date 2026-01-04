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
        'ip_address',
        'user_agent',
        'description',
        'properties',
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
}
