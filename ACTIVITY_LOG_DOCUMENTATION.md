# Activity Log System Documentation

## Overview
The Activity Log system tracks user authentication activities including login, logout, registration, and failed login attempts. This provides an audit trail for security monitoring and compliance.

## Features

### 1. Automatic Event Logging
The system automatically logs the following events:
- **Login**: Successful user login
- **Logout**: User logout
- **Registration**: New user registration
- **Failed Login**: Failed login attempts

### 2. Logged Information
For each activity, the system records:
- User ID (if authenticated)
- Event type
- IP address
- User agent (browser/device information)
- Description
- Timestamp
- Additional properties (JSON data)

### 3. Web Interface
Access the activity logs through the navigation menu:
**System & Security → Activity Logs**

#### Available Features:
- **View Logs**: Browse all activity logs with pagination
- **Filter Logs**: Filter by:
  - Event type
  - User
  - Date range
  - Search by IP address or description
- **View Details**: Click on any log to see full details
- **Export**: Download filtered logs as CSV
- **Cleanup**: Delete old logs to manage database size

## Technical Implementation

### Database Schema
```sql
Table: activity_logs
- id (primary key)
- user_id (foreign key to users table, nullable)
- event_type (string)
- ip_address (string, nullable)
- user_agent (text, nullable)
- description (text, nullable)
- properties (json, nullable)
- created_at (timestamp)
```

### Files Created
1. **Migration**: `database/migrations/2026_01_04_000001_create_activity_logs_table.php`
2. **Model**: `app/Models/ActivityLog.php`
3. **Controller**: `app/Http/Controllers/ActivityLogController.php`
4. **Event Listeners**:
   - `app/Listeners/LogSuccessfulLogin.php`
   - `app/Listeners/LogSuccessfulLogout.php`
   - `app/Listeners/LogSuccessfulRegistration.php`
   - `app/Listeners/LogFailedLogin.php`
5. **Views**:
   - `resources/views/activity-logs/index.blade.php`
   - `resources/views/activity-logs/show.blade.php`

### Routes
- `GET /activity-logs` - List all activity logs
- `GET /activity-logs/{id}` - View specific log details
- `POST /activity-logs/cleanup` - Delete old logs
- `GET /activity-logs/export/csv` - Export logs to CSV

## Usage Examples

### Manual Logging
You can manually log custom activities in your code:

```php
use App\Models\ActivityLog;

// Log with authenticated user
ActivityLog::log('custom_event', auth()->id(), 'User performed custom action');

// Log with specific user
ActivityLog::log('data_export', $userId, 'User exported customer data', [
    'export_type' => 'customers',
    'record_count' => 150
]);

// Log without user (system event)
ActivityLog::log('system_backup', null, 'Automated backup completed', [
    'backup_size' => '250MB',
    'backup_file' => 'backup_2026_01_04.sql'
]);
```

### Querying Logs
```php
// Get all login events for a user
$loginLogs = ActivityLog::where('user_id', $userId)
    ->where('event_type', 'login')
    ->orderBy('created_at', 'desc')
    ->get();

// Get failed login attempts from specific IP
$failedAttempts = ActivityLog::where('event_type', 'failed_login')
    ->where('ip_address', $ipAddress)
    ->whereDate('created_at', '>=', now()->subHours(24))
    ->count();

// Get recent activity for security dashboard
$recentActivity = ActivityLog::with('user')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();
```

## Security Best Practices

1. **Regular Cleanup**: Schedule periodic cleanup of old logs to prevent database bloat
   ```bash
   # In your scheduler (app/Console/Kernel.php)
   $schedule->command('db:query', ['DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)'])
       ->monthly();
   ```

2. **Monitor Failed Logins**: Set up alerts for multiple failed login attempts from same IP
3. **Export Regularly**: Export logs for long-term archival before cleanup
4. **Restrict Access**: Only administrators should have access to activity logs

## Maintenance

### Database Cleanup
To manually clean up old logs:
1. Go to Activity Logs page
2. Click "Cleanup Old Logs" button
3. Specify number of days (e.g., 90)
4. Confirm deletion

### Export Logs
1. Apply desired filters on the Activity Logs page
2. Click "Export CSV" button
3. File will download with current date/time in filename

## Future Enhancements

Consider adding:
- Email alerts for suspicious activity
- Dashboard widget showing recent activity
- Geographic location tracking from IP addresses
- Session management and concurrent login detection
- API access logging
- Data modification audit trail (who changed what)
- User activity timeline view
- Advanced analytics and reporting

## Related Events

Laravel provides many other authentication events that can be logged:
- `Illuminate\Auth\Events\PasswordReset`
- `Illuminate\Auth\Events\Verified` (email verification)
- `Illuminate\Auth\Events\Attempting` (login attempt)
- `Illuminate\Auth\Events\Lockout` (too many failed attempts)

To log additional events, create new listeners and register them in `AppServiceProvider.php`.
