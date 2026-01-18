<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If user is not logged in, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        // Check if user has completed KYC
        if (!$user->hasCompletedKyc()) {
            // If it's an AJAX request, return JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'KYC verification required to access this feature.',
                    'requires_kyc' => true,
                    'kyc_url' => route('kyc.verification')
                ], 403);
            }

            // Otherwise redirect to KYC verification page with message
            return redirect()->route('kyc.verification')
                ->with('warning', 'Please complete KYC verification to access this feature.');
        }

        return $next($request);
    }
}
