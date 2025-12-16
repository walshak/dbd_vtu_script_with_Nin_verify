<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$allowedTypes): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Convert string types to integers
        $allowedTypeIds = array_map(function($type) {
            return match(strtolower($type)) {
                'user', 'subscriber' => User::TYPE_USER,
                'agent' => User::TYPE_AGENT,
                'vendor' => User::TYPE_VENDOR,
                default => (int) $type
            };
        }, $allowedTypes);

        if (!in_array($user->sType, $allowedTypeIds)) {
            abort(403, 'Access denied. Your account type does not have permission for this action.');
        }

        return $next($request);
    }
}