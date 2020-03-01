<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
//use Illuminate\Support\Facades\Auth;



/**
 *  This middleware controls the user's access level, depending on the type of user:
 *  1) If current user in the forbidden area that didn't assign for his type, 
 *     this user is being redirected to the login form (compares the user type and 
 *     current route with the prefix route from the permitted route group)
 *  2) If current user already redirected from login to pseudo route /afterlogin 
 *     (HTTP\Controllers\Auth\LoginController - Property: $redirectTo) 
 *     (was confirmed authorisation) or he came to site root, this user is being redirected 
 *     to the dashboard with assigned user type 
 */
class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = Auth::user();

        if (!$user || !$user->is_admin) {
            return response()->json([
                'status'  => 'error',
                'code'    => 401,
                'message' => 'Недостаточно прав'
            ], 422);
        }

        return $next($request);
    }
}
