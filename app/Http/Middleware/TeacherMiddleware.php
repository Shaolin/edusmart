<?php



namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TeacherMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'teacher') {
            return $next($request);
        }

        abort(403, 'Unauthorized access.');
    }
}
