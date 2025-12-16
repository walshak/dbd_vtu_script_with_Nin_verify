<?php

namespace App\Http\Middleware;

use App\Models\FeatureToggle;
use App\Models\SiteSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MaintenanceModeMiddleware
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
        // Check if maintenance mode is enabled
        if (!FeatureToggle::isEnabled(FeatureToggle::FEATURE_MAINTENANCE_MODE)) {
            return $next($request);
        }

        // Allow access for admin users
        if ($this->isAdminUser($request)) {
            return $next($request);
        }

        // Check if IP is in allowed list
        if ($this->isAllowedIp($request)) {
            return $next($request);
        }

        // Check if maintenance period has ended
        if ($this->isMaintenanceExpired()) {
            FeatureToggle::disable(FeatureToggle::FEATURE_MAINTENANCE_MODE);
            SiteSettings::updateSetting('maintenance_end_time', null);
            return $next($request);
        }

        // Log maintenance mode access attempt
        Log::info('Maintenance mode access blocked', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl()
        ]);

        // Return maintenance response
        return $this->getMaintenanceResponse($request);
    }

    /**
     * Check if user is admin
     */
    private function isAdminUser(Request $request): bool
    {
        return auth()->guard('admin')->check();
    }

    /**
     * Check if IP is in allowed list
     */
    private function isAllowedIp(Request $request): bool
    {
        $allowedIps = SiteSettings::getSetting('maintenance_allowed_ips', []);
        
        if (is_string($allowedIps)) {
            $allowedIps = json_decode($allowedIps, true) ?: [];
        }

        if (empty($allowedIps)) {
            return false;
        }

        $userIp = $request->ip();
        
        foreach ($allowedIps as $allowedIp) {
            if ($this->ipMatches($userIp, trim($allowedIp))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP matches pattern (supports wildcards and CIDR)
     */
    private function ipMatches($userIp, $pattern): bool
    {
        // Exact match
        if ($userIp === $pattern) {
            return true;
        }

        // Wildcard patterns (e.g., 192.168.1.*)
        if (strpos($pattern, '*') !== false) {
            $pattern = str_replace('*', '.*', preg_quote($pattern, '/'));
            return preg_match('/^' . $pattern . '$/', $userIp);
        }

        // CIDR notation (e.g., 192.168.1.0/24)
        if (strpos($pattern, '/') !== false) {
            list($subnet, $bits) = explode('/', $pattern);
            $subnet = ip2long($subnet);
            $userIpLong = ip2long($userIp);
            $mask = -1 << (32 - $bits);
            
            return ($userIpLong & $mask) === ($subnet & $mask);
        }

        return false;
    }

    /**
     * Check if maintenance period has expired
     */
    private function isMaintenanceExpired(): bool
    {
        $endTime = SiteSettings::getSetting('maintenance_end_time');
        
        if (!$endTime) {
            return false;
        }

        return now()->greaterThan($endTime);
    }

    /**
     * Get maintenance response
     */
    private function getMaintenanceResponse(Request $request)
    {
        $message = SiteSettings::getSetting('maintenance_message', 'System maintenance in progress. Please try again later.');
        $endTime = SiteSettings::getSetting('maintenance_end_time');
        
        $data = [
            'message' => $message,
            'estimated_end' => $endTime ? $endTime->format('Y-m-d H:i:s') : null,
            'retry_after' => $endTime ? $endTime->diffInSeconds(now()) : 3600
        ];

        // Return JSON for API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Service temporarily unavailable',
                'data' => $data,
                'maintenance_mode' => true
            ], 503);
        }

        // Return maintenance page for web requests
        return response()->view('maintenance', $data, 503);
    }
}