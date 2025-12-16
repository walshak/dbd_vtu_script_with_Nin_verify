<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsVerified
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
        $user = Auth::user();

        if ($user && !$user->sEmailVerified) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Please verify your email address'], 403);
            }

            return redirect('/email/verify')
                ->with('warning', 'Please verify your email address to continue.');
        }

        return $next($request);
    }
}
