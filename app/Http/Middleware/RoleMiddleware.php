<?php

namespace App\Http\Middleware;

use Closure;
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
    public function handle($request, Closure $next, ... $roles) {
        if (!Auth::check()) { // Check if user is not logged in, prompt 401 error
            abort(401);
        } else {
            for ($i = 0; $i < Auth::user()->roles->count(); $i++) { // Allow user for next request if user has role
                if (in_array(Auth::user()->roles[$i]->slug, $roles)) {
                    return $next($request);
                }
            }

            foreach ($roles as $role) { // If user does not have the role, prompt 403 error
                if (!Auth::user()->hasRole($role)) {
                    abort(403);
                }
            }
        }
    }
}
