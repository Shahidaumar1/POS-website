<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is an admin
        if (auth()->user() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Redirect to home if not an admin
        return redirect('/home')->with('error', 'You do not have admin access');
    }
}
