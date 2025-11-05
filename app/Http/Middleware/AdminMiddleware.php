<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || strtolower(auth()->user()->role) !== 'admin') {
            abort(403, 'Admins only.');
        }

        return $next($request);
    }
}
