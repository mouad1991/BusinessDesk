<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Allow admin or admin impersonating a manager to access admin routes
        if (Auth::user()->isAdmin() && !$request->session()->has('impersonate')) {
            return $next($request);
        }

        abort(403, 'Accès non autorisé.');
    }
}
