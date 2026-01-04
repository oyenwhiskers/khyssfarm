<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Registered;

class LogSuccessfulRegistration
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        ActivityLog::log(
            'register',
            $event->user->id,
            "New user {$event->user->name} registered"
        );
    }
}
