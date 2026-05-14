<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Manager or admin impersonating a manager
        if (Auth::user()->isManager() || $request->session()->has('impersonate')) {
            return $next($request);
        }

        abort(403, 'Accès non autorisé.');
    }
}
