# Enhanced Activity Log System Documentation

## Overview
The enhanced Activity Log system provides comprehensive tracking of user activities, page views, interactions, and system events. It offers detailed insights into user behavior, system performance, and security monitoring.

## New Features

### 1. Automatic Activity Tracking
The system now automatically tracks:
- **Page Views**: Every page accessed by authenticated users
- **CRUD Operations**: Create, Update, Delete actions on all resources
- **Module Interactions**: Activities within specific modules (harvests, sales, customers, etc.)
- **Performance Metrics**: Request duration and slow request detection
- **Device & Browser Information**: Detailed tracking of user devices and browsers

### 2. Enhanced Data Captured
For each activity, the system now records:
- **URL**: Full URL of the request
- **HTTP Method**: GET, POST, PUT, DELETE
- **Referer**: Where the user came from
- **Module**: Which module/section was accessed (e.g., harvests, sales)
- **Resource Type & ID**: Specific resource being interacted with
- **Duration**: How long the request took (in milliseconds)
- **Session ID**: Track user sessions
- **IP Address & User Agent**: Device and location information
- **Properties**: Additional contextual data (status codes, route info, etc.)

### 3. Analytics Dashboard
Access comprehensive analytics at: **Activity Logs → Analytics**

Includes:
- Activity breakdown by event type
- Most active modules
- Top users by activity
- Activity patterns by hour of day
- Browser and device statistics
- Slow request detection
- Daily activity trends

### 4. Advanced Filtering
Filter logs by:
- Event type (page_view, create, update, delete, login, etc.)
- Module (harvests, sales, customers, etc.)
- HTTP method (GET, POST, PUT, DELETE)
- User
- Date range
- Search by URL, IP address, or description

## Technical Implementation

### New Database Columns
```sql
- url (string, 500): Full URL of the request
- http_method (string, 10): HTTP method used
- referer (string, 500): Referer URL
- module (string, 100): Module/section name
- resource_type (string, 100): Type of resource (e.g., Harvest, Sale)
- resource_id (bigint): ID of the specific resource
- duration_ms (integer): Request duration in milliseconds
- session_id (string, 100): User session identifier
```

### New Files Created

#### Middleware
- `app/Http/Middleware/TrackUserActivity.php` - Automatically tracks all user requests

#### Trait
- `app/Traits/LogsActivity.php` - Can be added to models to automatically log CRUD operations

### Event Types
The system now tracks these event types:

| Event Type | Description | Example |
|------------|-------------|---------|
| `page_view` | User viewed a page | Accessing dashboard, list pages |
| `create` | New record created | Creating a new harvest, sale |
| `update` | Record updated | Editing an existing customer |
| `delete` | Record deleted | Deleting a price entry |
| `login` | User logged in | Successful authentication |
| `logout` | User logged out | User signed out |
| `register` | New user registered | Account creation |
| `failed_login` | Failed login attempt | Invalid credentials |
| `action` | Custom action | Export, import, generate insights |
| `page_error` | Error page accessed | 404, 500 errors |

### Using the LogsActivity Trait

To automatically log CRUD operations for a model, add the trait:

```php
use App\Traits\LogsActivity;

class Harvest extends Model
{
    use LogsActivity;
    
    // Your model code...
}
```

This will automatically log:
- When a harvest is created (captures all attributes)
- When a harvest is updated (captures old and new values)
- When a harvest is deleted (captures final state)

### Middleware Configuration

The `TrackUserActivity` middleware is registered globally for all web routes in `bootstrap/app.php`:

```php
$middleware->web(append: [
    \App\Http\Middleware\CheckAccountStatus::class,
    \App\Http\Middleware\TrackUserActivity::class,
]);
```

**Excluded Routes:**
- Logout (prevents redirect loops)
- Pending account page
- Livewire/debugging endpoints
- AJAX polling requests
- API trend data endpoints

### Performance Considerations

The middleware tracks request duration and identifies slow requests:
- Requests > 1000ms are flagged as slow
- Requests > 2000ms appear in the analytics slow requests section
- Use this data to identify performance bottlenecks

### Routes

```php
// View all activity logs
GET /activity-logs

// View analytics dashboard
GET /activity-logs/analytics

// View specific log details
GET /activity-logs/{id}

// Export logs to CSV
GET /activity-logs/export/csv

// Cleanup old logs
POST /activity-logs/cleanup
```

All routes require admin access.

## Usage Examples

### Manual Logging

```php
use App\Models\ActivityLog;

// Log a custom event
ActivityLog::create([
    'user_id' => auth()->id(),
    'event_type' => 'export',
    'module' => 'harvests',
    'description' => 'User exported harvest data',
    'properties' => ['format' => 'CSV', 'count' => 150],
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'session_id' => session()->getId(),
]);
```

### Querying Logs

```php
// Get all page views for harvests module
$harvestViews = ActivityLog::pageViews()
    ->inModule('harvests')
    ->get();

// Get CRUD operations in last 7 days
$recentCrud = ActivityLog::crudOperations()
    ->betweenDates(now()->subDays(7), now())
    ->get();

// Find slow requests
$slowRequests = ActivityLog::slowRequests(2000) // > 2 seconds
    ->orderByDesc('duration_ms')
    ->get();

// Get authentication events
$authEvents = ActivityLog::authEvents()
    ->where('user_id', $userId)
    ->get();

// Get statistics for a user
$stats = ActivityLog::getStatisticsForUser($userId, 30);
// Returns: total_activities, page_views, crud_operations, 
//          average_duration, most_visited_module, browsers, devices
```

### Model Activity Logs

If using the `LogsActivity` trait on a model:

```php
// Get all activity logs for a specific harvest
$harvest = Harvest::find(1);
$activities = $harvest->activityLogs();

// Get the latest activity
$latestActivity = $harvest->latestActivity();
```

## Analytics Insights

The analytics dashboard provides:

### 1. Activity Breakdown
- Pie chart showing distribution of event types
- Helps identify main user activities

### 2. Module Popularity
- Bar chart of most accessed modules
- Identify which features are used most

### 3. User Activity
- Top 10 most active users
- Track engagement levels

### 4. Time Patterns
- Activity by hour of day
- Identify peak usage times

### 5. Technology Stack
- Browser distribution (Chrome, Firefox, Safari, etc.)
- Device types (Desktop, Mobile, Tablet)

### 6. Performance Monitoring
- List of slow requests (>2 seconds)
- Identify performance bottlenecks
- Average request duration

### 7. Daily Trends
- Activity count by day
- Spot unusual patterns or anomalies

## Best Practices

### 1. Regular Cleanup
- Clean old logs periodically to maintain database performance
- Recommended: Keep last 90-180 days for active monitoring
- Archive older logs if needed for compliance

### 2. Monitor Slow Requests
- Review slow requests weekly
- Optimize pages/queries that consistently appear
- Set alerts for critical slow endpoints

### 3. Security Monitoring
- Monitor failed login attempts
- Review unusual activity patterns
- Check for suspicious IP addresses

### 4. Privacy Compliance
- Activity logs may contain personal data
- Ensure proper data retention policies
- Implement access controls (admin-only)

### 5. Performance Impact
- Middleware adds minimal overhead (<50ms typically)
- Database writes are non-blocking
- Consider using queues for high-traffic applications

## Customization

### Adding Custom Event Types

In your controller:
```php
ActivityLog::create([
    'user_id' => auth()->id(),
    'event_type' => 'custom_action',
    'module' => 'reports',
    'description' => 'Generated monthly report',
    // ... other fields
]);
```

Then add badge color in `ActivityLog` model:
```php
public function getEventBadgeColor(): string
{
    return match($this->event_type) {
        // ... existing cases
        'custom_action' => 'warning',
        default => 'secondary',
    };
}
```

### Excluding Routes from Tracking

Edit `app/Http/Middleware/TrackUserActivity.php`:

```php
protected array $excludedRoutes = [
    'logout',
    'account.pending',
    'your.route.name', // Add your route
];

protected array $excludedPatterns = [
    'livewire/*',
    '_debugbar/*',
    'your-pattern/*', // Add your pattern
];
```

## Troubleshooting

### High Database Growth
- Implement regular cleanup (e.g., monthly)
- Consider archiving to separate table
- Exclude high-frequency, low-value routes

### Performance Issues
- Add database indexes on frequently queried columns
- Use pagination when displaying logs
- Implement caching for analytics data

### Missing Logs
- Check if route is in excluded list
- Verify user is authenticated
- Check middleware is registered

## Future Enhancements

Consider adding:
- Real-time activity feed
- Email alerts for suspicious activities
- Advanced anomaly detection
- Custom dashboards per user role
- Integration with external analytics tools
- Activity heatmaps
- User session replay capabilities

## Support

For issues or questions:
1. Review this documentation
2. Check the activity log views for examples
3. Review the source code in `app/Models/ActivityLog.php`
4. Contact system administrator
