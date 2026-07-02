<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use App\Models\Audit;
use Illuminate\Support\Facades\Request;

class AuditSubscriber
{
    public function handleUserLogin(Login $event)
    {
        $this->audit('login', $event->user);
    }

    public function handleUserLogout(Logout $event)
    {
        if ($event->user) {
            $this->audit('logout', $event->user);
        }
    }

    public function handleLoginFailed(Failed $event)
    {
        $this->audit('login_failed', null, [
            'email' => $event->credentials['email'] ?? 'unknown',
        ]);
    }

    public function handlePasswordReset(PasswordReset $event)
    {
        $this->audit('password_reset', $event->user);
    }

    public function handleEmailVerified(Verified $event)
    {
        $this->audit('email_verified', $event->user);
    }

    protected function audit($event, $user = null, array $data = [])
    {
        $audit = new Audit();
        $audit->user_id = $user ? $user->id : null;
        $audit->event = $event;
        $audit->auditable_type = $user ? get_class($user) : 'System';
        $audit->auditable_id = $user ? $user->id : 0;
        $audit->old_values = [];
        $audit->new_values = $data;
        $audit->url = Request::fullUrl();
        $audit->ip_address = Request::ip();
        $audit->user_agent = Request::userAgent();
        $audit->save();
    }

    public function subscribe($events)
    {
        return [
            Login::class => 'handleUserLogin',
            Logout::class => 'handleUserLogout',
            Failed::class => 'handleLoginFailed',
            PasswordReset::class => 'handlePasswordReset',
            Verified::class => 'handleEmailVerified',
        ];
    }
}
