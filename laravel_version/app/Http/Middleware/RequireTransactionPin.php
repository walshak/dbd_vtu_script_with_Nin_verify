<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTransactionPin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if PIN is enabled for this user
        if ($user->sPinStatus == 1) {
            // For POST requests, validate the PIN
            if ($request->isMethod('POST')) {
                $transactionPin = $request->input('transaction_pin') ?? $request->input('kpin');
                
                if (!$transactionPin) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Transaction PIN is required'
                        ], 400);
                    }
                    
                    return back()->withErrors(['transaction_pin' => 'Transaction PIN is required']);
                }

                if ($user->sPin != $transactionPin) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Invalid transaction PIN'
                        ], 400);
                    }
                    
                    return back()->withErrors(['transaction_pin' => 'Invalid transaction PIN']);
                }
            }
        }

        return $next($request);
    }
}