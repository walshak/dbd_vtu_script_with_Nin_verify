<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DeviceController extends Controller
{
    /**
     * Register a new device
     */
    public function registerDevice(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required|string',
                'device_name' => 'required|string|max:255',
                'device_type' => 'required|in:android,ios,web',
                'fcm_token' => 'nullable|string',
                'app_version' => 'nullable|string',
                'os_version' => 'nullable|string',
                'device_info' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if device already exists
            $existingDevice = UserDevice::where('device_id', $request->device_id)->first();

            if ($existingDevice) {
                // Update existing device
                $existingDevice->update([
                    'device_name' => $request->device_name,
                    'fcm_token' => $request->fcm_token,
                    'app_version' => $request->app_version,
                    'os_version' => $request->os_version,
                    'device_info' => $request->device_info,
                    'last_active_at' => now(),
                    'ip_address' => $request->ip()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Device updated successfully',
                    'data' => [
                        'device_id' => $existingDevice->device_id,
                        'status' => 'updated'
                    ]
                ]);
            }

            // Create new device
            $device = UserDevice::create([
                'device_id' => $request->device_id,
                'device_name' => $request->device_name,
                'device_type' => $request->device_type,
                'fcm_token' => $request->fcm_token,
                'app_version' => $request->app_version,
                'os_version' => $request->os_version,
                'device_info' => $request->device_info,
                'status' => 'active',
                'first_seen_at' => now(),
                'last_active_at' => now(),
                'ip_address' => $request->ip()
            ]);

            Log::info('Device registered', [
                'device_id' => $device->device_id,
                'device_type' => $device->device_type
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Device registered successfully',
                'data' => [
                    'device_id' => $device->device_id,
                    'status' => 'registered'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Device registration failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Device registration failed'
            ], 500);
        }
    }

    /**
     * Update FCM token for push notifications
     */
    public function updateFcmToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required|string',
                'fcm_token' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $device = UserDevice::where('device_id', $request->device_id)->first();

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found'
                ], 404);
            }

            $device->update([
                'fcm_token' => $request->fcm_token,
                'last_active_at' => now()
            ]);

            // Also update user's fcm_token if user is authenticated
            if (auth()->check()) {
                auth()->user()->update(['fcm_token' => $request->fcm_token]);
            }

            return response()->json([
                'success' => true,
                'message' => 'FCM token updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('FCM token update failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'FCM token update failed'
            ], 500);
        }
    }

    /**
     * Get device information
     */
    public function getDeviceInfo(Request $request)
    {
        try {
            $user = $request->user();
            $devices = UserDevice::where('user_id', $user->id)
                ->orderBy('last_active_at', 'desc')
                ->get()
                ->map(function ($device) {
                    return [
                        'id' => $device->id,
                        'device_id' => $device->device_id,
                        'device_name' => $device->device_name,
                        'device_type' => $device->device_type,
                        'app_version' => $device->app_version,
                        'os_version' => $device->os_version,
                        'status' => $device->status,
                        'is_current' => $device->device_id === $request->header('Device-ID'),
                        'first_seen_at' => $device->first_seen_at?->toISOString(),
                        'last_active_at' => $device->last_active_at?->toISOString(),
                        'location' => $device->location
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'devices' => $devices,
                    'total_devices' => $devices->count(),
                    'active_devices' => $devices->where('status', 'active')->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get device info failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve device information'
            ], 500);
        }
    }

    /**
     * Update device preferences
     */
    public function updatePreferences(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required|string',
                'preferences' => 'required|array',
                'preferences.notifications' => 'boolean',
                'preferences.biometric_auth' => 'boolean',
                'preferences.auto_lock_timeout' => 'integer|min:30|max:3600',
                'preferences.theme' => 'in:light,dark,auto'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $device = UserDevice::where('device_id', $request->device_id)
                               ->where('user_id', $user->id)
                               ->first();

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found'
                ], 404);
            }

            $device->update([
                'preferences' => $request->preferences,
                'last_active_at' => now()
            ]);

            Log::info('Device preferences updated', [
                'user_id' => $user->id,
                'device_id' => $device->device_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Preferences updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Update preferences failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update preferences'
            ], 500);
        }
    }

    /**
     * Setup biometric authentication
     */
    public function setupBiometric(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required|string',
                'biometric_type' => 'required|in:fingerprint,face_id,iris',
                'biometric_data' => 'required|string',
                'pin' => 'required|string|size:4'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Verify PIN
            if (!$user->transaction_pin || !Hash::check($request->pin, $user->transaction_pin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid PIN'
                ], 400);
            }

            $device = UserDevice::where('device_id', $request->device_id)
                               ->where('user_id', $user->id)
                               ->first();

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found'
                ], 404);
            }

            // Generate biometric token
            $biometricToken = Hash::make($request->biometric_data . $user->id . now()->timestamp);

            $device->update([
                'biometric_enabled' => true,
                'biometric_type' => $request->biometric_type,
                'biometric_token' => $biometricToken,
                'biometric_setup_at' => now()
            ]);

            Log::info('Biometric authentication setup', [
                'user_id' => $user->id,
                'device_id' => $device->device_id,
                'biometric_type' => $request->biometric_type
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Biometric authentication setup successfully',
                'data' => [
                    'biometric_token' => $biometricToken
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Biometric setup failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Biometric setup failed'
            ], 500);
        }
    }

    /**
     * Get active sessions
     */
    public function getActiveSessions(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get active tokens
            $sessions = $user->tokens()
                            ->where('last_used_at', '>', now()->subDays(30))
                            ->orderBy('last_used_at', 'desc')
                            ->get()
                            ->map(function ($token) use ($request) {
                                return [
                                    'id' => $token->id,
                                    'name' => $token->name,
                                    'is_current' => $token->id === $request->user()->currentAccessToken()->id,
                                    'last_used_at' => $token->last_used_at?->toISOString(),
                                    'created_at' => $token->created_at?->toISOString(),
                                    'expires_at' => $token->expires_at?->toISOString()
                                ];
                            });

            return response()->json([
                'success' => true,
                'data' => [
                    'sessions' => $sessions,
                    'total_sessions' => $sessions->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get active sessions failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve active sessions'
            ], 500);
        }
    }

    /**
     * Revoke a session
     */
    public function revokeSession(Request $request, $sessionId)
    {
        try {
            $user = $request->user();
            $currentTokenId = $request->user()->currentAccessToken()->id;

            if ($sessionId == $currentTokenId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot revoke current session'
                ], 400);
            }

            $token = $user->tokens()->where('id', $sessionId)->first();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            $token->delete();

            Log::info('Session revoked', [
                'user_id' => $user->id,
                'session_id' => $sessionId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Session revoked successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Session revocation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke session'
            ], 500);
        }
    }

    /**
     * Device security check
     */
    public function securityCheck(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required|string',
                'security_features' => 'required|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $device = UserDevice::where('device_id', $request->device_id)->first();

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found'
                ], 404);
            }

            $securityScore = $this->calculateSecurityScore($request->security_features);
            $recommendations = $this->getSecurityRecommendations($request->security_features);

            $device->update([
                'security_score' => $securityScore,
                'security_features' => $request->security_features,
                'last_security_check' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'security_score' => $securityScore,
                    'security_level' => $this->getSecurityLevel($securityScore),
                    'recommendations' => $recommendations,
                    'last_check' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Security check failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Security check failed'
            ], 500);
        }
    }

    /**
     * Calculate security score based on device features
     */
    private function calculateSecurityScore(array $features): int
    {
        $score = 0;
        
        if ($features['screen_lock'] ?? false) $score += 20;
        if ($features['biometric_auth'] ?? false) $score += 25;
        if ($features['app_lock'] ?? false) $score += 15;
        if ($features['auto_lock'] ?? false) $score += 10;
        if ($features['encrypted_storage'] ?? false) $score += 20;
        if ($features['secure_boot'] ?? false) $score += 10;

        return min(100, $score);
    }

    /**
     * Get security level based on score
     */
    private function getSecurityLevel(int $score): string
    {
        if ($score >= 80) return 'high';
        if ($score >= 60) return 'medium';
        if ($score >= 40) return 'low';
        return 'very_low';
    }

    /**
     * Get security recommendations
     */
    private function getSecurityRecommendations(array $features): array
    {
        $recommendations = [];

        if (!($features['screen_lock'] ?? false)) {
            $recommendations[] = 'Enable screen lock for basic device security';
        }

        if (!($features['biometric_auth'] ?? false)) {
            $recommendations[] = 'Enable biometric authentication for enhanced security';
        }

        if (!($features['app_lock'] ?? false)) {
            $recommendations[] = 'Enable app-specific lock for additional protection';
        }

        if (!($features['auto_lock'] ?? false)) {
            $recommendations[] = 'Enable auto-lock to secure device when idle';
        }

        return $recommendations;
    }
}