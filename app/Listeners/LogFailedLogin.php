<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        $email = isset($event->credentials['email']) ? $event->credentials['email'] : 'unknown';
        
        ActivityLog::log(
            'failed_login',
            null,
            "Failed login attempt for email: {$email}",
            [
                'email' => isset($event->credentials['email']) ? $event->credentials['email'] : null,
            ]
        );
    }
}
