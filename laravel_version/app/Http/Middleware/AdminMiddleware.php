<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Debug logging
        Log::info('AdminMiddleware: Checking auth for URL: ' . $request->url());
        Log::info('AdminMiddleware: Auth check result: ' . (Auth::guard('admin')->check() ? 'TRUE' : 'FALSE'));

        if (Auth::guard('admin')->user()) {
            Log::info('AdminMiddleware: User found: ' . Auth::guard('admin')->user()->sysUsername);
        } else {
            Log::info('AdminMiddleware: No user found');
        }

        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized admin access'], 401);
            }

            Log::info('AdminMiddleware: Redirecting to admin login');
            return redirect('/admin/login')->with('error', 'Please login to access admin panel.');
        }

        Log::info('AdminMiddleware: Auth passed, proceeding');
        return $next($request);
    }
}
