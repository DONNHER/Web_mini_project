<?php

namespace App\Listeners;

use Illuminate\Log\Events\MessageLogged;
use App\Models\Audit;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class LogErrorToAudit
{
    /**
     * Handle the event.
     */
    public function handle(MessageLogged $event)
    {
        // Only log errors and critical levels to the DB to avoid bloating
        if ($event->level !== 'error' && $event->level !== 'critical' && $event->level !== 'alert') {
            return;
        }

        try {
            $audit = new Audit();
            $audit->user_id = Auth::id();
            $audit->user_type = Auth::check() ? get_class(Auth::user()) : null;
            $audit->event = 'error_logged';
            $audit->auditable_type = 'System';
            $audit->auditable_id = 0;
            $audit->old_values = [];
            $audit->new_values = [
                'level' => $event->level,
                'message' => $event->message,
                'context' => $event->context,
            ];
            $audit->url = Request::fullUrl();
            $audit->ip_address = Request::ip();
            $audit->user_agent = Request::userAgent();
            $audit->save();
        } catch (\Exception $e) {
            // Silently fail to avoid infinite loops if saving the log fails
        }
    }
}
