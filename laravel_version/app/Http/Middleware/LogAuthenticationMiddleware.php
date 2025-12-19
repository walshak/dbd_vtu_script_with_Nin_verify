<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\LoggingService;

class LogAuthenticationMiddleware
{
    protected $loggingService;

    public function __construct(LoggingService $loggingService)
    {
        $this->loggingService = $loggingService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log authentication events
        $this->logAuthenticationEvent($request, $response);

        return $response;
    }

    /**
     * Log authentication-related events
     */
    protected function logAuthenticationEvent(Request $request, $response): void
    {
        try {
            $route = $request->route();

            if (!$route) {
                return;
            }

            $routeName = $route->getName();
            $statusCode = $response->getStatusCode();

            // Log login attempts
            if ($routeName === 'login' && $request->isMethod('post')) {
                $this->logLoginAttempt($request, $statusCode);
            }

            // Log logout events
            if ($routeName === 'logout' && $request->isMethod('post')) {
                $this->logLogoutEvent($request);
            }

            // Log registration attempts
            if ($routeName === 'register' && $request->isMethod('post')) {
                $this->logRegistrationAttempt($request, $statusCode);
            }

            // Log password reset attempts
            if (str_contains($routeName ?? '', 'password') && $request->isMethod('post')) {
                $this->logPasswordResetAttempt($request, $statusCode);
            }

            // Log failed authentication on protected routes
            if ($statusCode === 401 || $statusCode === 403) {
                $this->logUnauthorizedAccess($request);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log authentication event: ' . $e->getMessage());
        }
    }

    /**
     * Log login attempts
     */
    protected function logLoginAttempt(Request $request, int $statusCode): void
    {
        $success = $statusCode === 200;
        $username = $request->input('email') ?? $request->input('username') ?? $request->input('phone');

        $this->loggingService->logAuthentication(
            'login',
            $username,
            $success,
            [
                'login_method' => $this->getLoginMethod($request),
                'remember_me' => $request->boolean('remember'),
                'status_code' => $statusCode,
                'two_factor_enabled' => $this->checkTwoFactorEnabled($request),
                'device_info' => $this->getDeviceInfo($request)
            ]
        );

        // Log security event for failed logins
        if (!$success) {
            $this->loggingService->logSecurity(
                'failed_login_attempt',
                'medium',
                [
                    'username' => $username,
                    'attempt_count' => $this->getFailedAttemptCount($request->ip()),
                    'status_code' => $statusCode
                ],
                null
            );
        }
    }

    /**
     * Log logout events
     */
    protected function logLogoutEvent(Request $request): void
    {
        $user = Auth::user();

        $this->loggingService->logAuthentication(
            'logout',
            $user ? $user->email : 'unknown',
            true,
            [
                'user_id' => $user ? $user->id : null,
                'session_duration' => $this->calculateSessionDuration($request),
                'logout_method' => 'manual'
            ]
        );
    }

    /**
     * Log registration attempts
     */
    protected function logRegistrationAttempt(Request $request, int $statusCode): void
    {
        $success = $statusCode === 200 || $statusCode === 201;
        $email = $request->input('email');

        $this->loggingService->logAuthentication(
            'registration',
            $email,
            $success,
            [
                'registration_method' => 'email',
                'status_code' => $statusCode,
                'verification_required' => true,
                'referral_code' => $request->input('referral_code')
            ]
        );

        // Log security event for suspicious registration patterns
        if ($this->detectSuspiciousRegistration($request)) {
            $this->loggingService->logSecurity(
                'suspicious_registration',
                'low',
                [
                    'email' => $email,
                    'suspicious_indicators' => $this->getSuspiciousIndicators($request)
                ],
                null
            );
        }
    }

    /**
     * Log password reset attempts
     */
    protected function logPasswordResetAttempt(Request $request, int $statusCode): void
    {
        $success = $statusCode === 200;
        $email = $request->input('email');

        $this->loggingService->logAuthentication(
            'password_reset',
            $email,
            $success,
            [
                'reset_method' => 'email',
                'status_code' => $statusCode,
                'token_valid' => $success
            ]
        );
    }

    /**
     * Log unauthorized access attempts
     */
    protected function logUnauthorizedAccess(Request $request): void
    {
        $this->loggingService->logSecurity(
            'unauthorized_access_attempt',
            'medium',
            [
                'path' => $request->path(),
                'method' => $request->method(),
                'attempted_action' => $request->route() ? $request->route()->getActionName() : 'unknown',
                'referrer' => $request->header('referer'),
                'requires_authentication' => !Auth::check()
            ],
            Auth::user()->id
        );
    }

    /**
     * Get login method from request
     */
    protected function getLoginMethod(Request $request): string
    {
        if ($request->input('email')) {
            return 'email';
        } elseif ($request->input('phone')) {
            return 'phone';
        } elseif ($request->input('username')) {
            return 'username';
        }

        return 'unknown';
    }

    /**
     * Check if two-factor authentication is enabled
     */
    protected function checkTwoFactorEnabled(Request $request): bool
    {
        // This would check user's 2FA settings
        // For now, return false as default
        return false;
    }

    /**
     * Get device information from request
     */
    protected function getDeviceInfo(Request $request): array
    {
        return [
            'user_agent' => $request->userAgent(),
            'platform' => $this->getPlatform($request->userAgent()),
            'browser' => $this->getBrowser($request->userAgent()),
            'is_mobile' => $this->isMobile($request->userAgent())
        ];
    }

    /**
     * Get platform from user agent
     */
    protected function getPlatform(string $userAgent): string
    {
        if (stripos($userAgent, 'windows') !== false) return 'Windows';
        if (stripos($userAgent, 'mac') !== false) return 'Mac';
        if (stripos($userAgent, 'linux') !== false) return 'Linux';
        if (stripos($userAgent, 'android') !== false) return 'Android';
        if (stripos($userAgent, 'ios') !== false) return 'iOS';

        return 'Unknown';
    }

    /**
     * Get browser from user agent
     */
    protected function getBrowser(string $userAgent): string
    {
        if (stripos($userAgent, 'chrome') !== false) return 'Chrome';
        if (stripos($userAgent, 'firefox') !== false) return 'Firefox';
        if (stripos($userAgent, 'safari') !== false) return 'Safari';
        if (stripos($userAgent, 'edge') !== false) return 'Edge';
        if (stripos($userAgent, 'opera') !== false) return 'Opera';

        return 'Unknown';
    }

    /**
     * Check if request is from mobile device
     */
    protected function isMobile(string $userAgent): bool
    {
        return (bool) preg_match('/Mobile|Android|iPhone|iPad/', $userAgent);
    }

    /**
     * Get failed login attempt count for IP
     */
    protected function getFailedAttemptCount(string $ip): int
    {
        // This would typically query a cache or database
        // For now, return a mock value
        return rand(1, 5);
    }

    /**
     * Calculate session duration
     */
    protected function calculateSessionDuration(Request $request): ?int
    {
        // This would calculate time since login
        // For now, return null
        return null;
    }

    /**
     * Detect suspicious registration patterns
     */
    protected function detectSuspiciousRegistration(Request $request): bool
    {
        $indicators = 0;

        // Check for disposable email
        $email = $request->input('email');
        $disposableDomains = ['10minutemail.com', 'tempmail.org', 'guerrillamail.com'];
        foreach ($disposableDomains as $domain) {
            if (str_ends_with($email, $domain)) {
                $indicators++;
                break;
            }
        }

        // Check for rapid registrations from same IP
        $recentRegistrations = $this->getRecentRegistrationsFromIp($request->ip());
        if ($recentRegistrations > 3) {
            $indicators++;
        }

        return $indicators >= 1;
    }

    /**
     * Get suspicious registration indicators
     */
    protected function getSuspiciousIndicators(Request $request): array
    {
        $indicators = [];

        $email = $request->input('email');
        $disposableDomains = ['10minutemail.com', 'tempmail.org', 'guerrillamail.com'];

        foreach ($disposableDomains as $domain) {
            if (str_ends_with($email, $domain)) {
                $indicators[] = 'disposable_email';
                break;
            }
        }

        $recentRegistrations = $this->getRecentRegistrationsFromIp($request->ip());
        if ($recentRegistrations > 3) {
            $indicators[] = 'multiple_registrations_from_ip';
        }

        return $indicators;
    }

    /**
     * Get recent registrations from IP address
     */
    protected function getRecentRegistrationsFromIp(string $ip): int
    {
        // This would query recent registrations from the same IP
        // For now, return a mock value
        return rand(0, 5);
    }
}
