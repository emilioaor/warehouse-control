<?php

namespace App\Http\Middleware;

use App\Service\AlertService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $roles = $request->route()->getAction('roles');

        if ($roles && ! in_array(Auth::user()->role, $roles)) {

            AlertService::alertFail(__('alert.notPermissionAccess'));

            if ($request->wantsJson()) {
                return response()->json(['message' => __('alert.notPermissionAccess')], 403);
            }

            return redirect()->route('home');
        }

        return $next($request);
    }
}
