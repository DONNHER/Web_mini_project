<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Audit;
use Illuminate\Support\Facades\Auth;

class LogAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only log GET requests to avoid duplicating CRUD audits
        if ($request->isMethod('GET') && ! $request->ajax()) {
            $this->logAccess($request);
        }

        return $response;
    }

    protected function logAccess(Request $request)
    {
        $audit = new Audit();
        $audit->user_id = Auth::id();
        $audit->user_type = Auth::check() ? get_class(Auth::user()) : null;
        $audit->event = 'accessed';
        $audit->auditable_type = 'Page';
        $audit->auditable_id = 0;
        $audit->old_values = [];
        $audit->new_values = [
            'path' => $request->path(),
            'method' => $request->method(),
        ];
        $audit->url = $request->fullUrl();
        $audit->ip_address = $request->ip();
        $audit->user_agent = $request->userAgent();
        $audit->save();
    }
}
