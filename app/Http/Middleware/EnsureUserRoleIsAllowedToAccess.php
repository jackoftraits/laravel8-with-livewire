<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class EnsureUserRoleIsAllowedToAccess
{
    // dashboard, pages, navigation-menus

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $userRole = auth()->user()->role;
            $currentRouteName = Route::currentRouteName();

            if (in_array($currentRouteName, $this->userAccessRole()[$userRole])) {
                return $next($request);
            } else {
                abort(403, 'Unauthorized action.');
            }
        } catch (\Throwable $th) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * The list of accessible resource for a specific user.
     * We will store this in the database late.
     *
     * @return void
     */
    private function userAccessRole()
    {
        return [
            'user' => [
                'dashboard',
            ],
            'admin' => [
                'pages',
                'navigation-menus',
            ],
        ];
    }
}
