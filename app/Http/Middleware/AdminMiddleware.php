<?php

namespace App\Http\Middleware;

use App\Service\AlertService;
use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! Auth::user()->isAdmin()) {
            AlertService::alertFail(__('alert.notPermissionAccess'));

            return redirect()->route('home');
        }

        return $next($request);
    }
}
