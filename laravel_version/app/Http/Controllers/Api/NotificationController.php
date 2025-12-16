<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get user notifications
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);
            $type = $request->get('type'); // all, read, unread
            
            $query = Notification::where('user_id', $user->id)
                                ->orderBy('created_at', 'desc');

            if ($type === 'unread') {
                $query->whereNull('read_at');
            } elseif ($type === 'read') {
                $query->whereNotNull('read_at');
            }

            $notifications = $query->paginate($limit, ['*'], 'page', $page);

            $formattedNotifications = $notifications->getCollection()->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'category' => $notification->category,
                    'data' => $notification->data,
                    'is_read' => !is_null($notification->read_at),
                    'created_at' => $notification->created_at->toISOString(),
                    'read_at' => $notification->read_at?->toISOString()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => $formattedNotifications,
                    'pagination' => [
                        'current_page' => $notifications->currentPage(),
                        'per_page' => $notifications->perPage(),
                        'total' => $notifications->total(),
                        'last_page' => $notifications->lastPage(),
                        'has_more' => $notifications->hasMorePages()
                    ],
                    'summary' => [
                        'total_unread' => Notification::where('user_id', $user->id)
                                                   ->whereNull('read_at')
                                                   ->count()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get notifications failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notifications'
            ], 500);
        }
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $user = $request->user();
            
            $unreadCount = Notification::where('user_id', $user->id)
                                     ->whereNull('read_at')
                                     ->count();

            $categoryCounts = Notification::where('user_id', $user->id)
                                        ->whereNull('read_at')
                                        ->selectRaw('category, COUNT(*) as count')
                                        ->groupBy('category')
                                        ->pluck('count', 'category')
                                        ->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_unread' => $unreadCount,
                    'by_category' => $categoryCounts
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get unread count failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get unread count'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $notificationId)
    {
        try {
            $user = $request->user();
            
            $notification = Notification::where('id', $notificationId)
                                      ->where('user_id', $user->id)
                                      ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            if (!$notification->read_at) {
                $notification->update(['read_at' => now()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);

        } catch (\Exception $e) {
            Log::error('Mark as read failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = $request->user();
            
            $updatedCount = Notification::where('user_id', $user->id)
                                      ->whereNull('read_at')
                                      ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'data' => [
                    'updated_count' => $updatedCount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Mark all as read failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read'
            ], 500);
        }
    }

    /**
     * Delete a notification
     */
    public function delete(Request $request, $notificationId)
    {
        try {
            $user = $request->user();
            
            $notification = Notification::where('id', $notificationId)
                                      ->where('user_id', $user->id)
                                      ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Delete notification failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification'
            ], 500);
        }
    }

    /**
     * Get notification preferences
     */
    public function getPreferences(Request $request)
    {
        try {
            $user = $request->user();
            
            $preferences = [
                'push_notifications' => $user->notification_preferences['push'] ?? true,
                'email_notifications' => $user->notification_preferences['email'] ?? true,
                'sms_notifications' => $user->notification_preferences['sms'] ?? true,
                'marketing_notifications' => $user->notification_preferences['marketing'] ?? false,
                'notification_types' => [
                    'transaction_success' => $user->notification_preferences['types']['transaction_success'] ?? true,
                    'transaction_failed' => $user->notification_preferences['types']['transaction_failed'] ?? true,
                    'wallet_funded' => $user->notification_preferences['types']['wallet_funded'] ?? true,
                    'security_alerts' => $user->notification_preferences['types']['security_alerts'] ?? true,
                    'system_updates' => $user->notification_preferences['types']['system_updates'] ?? true,
                    'promotional' => $user->notification_preferences['types']['promotional'] ?? false
                ],
                'quiet_hours' => [
                    'enabled' => $user->notification_preferences['quiet_hours']['enabled'] ?? false,
                    'start_time' => $user->notification_preferences['quiet_hours']['start'] ?? '22:00',
                    'end_time' => $user->notification_preferences['quiet_hours']['end'] ?? '07:00'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'preferences' => $preferences
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get notification preferences failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notification preferences'
            ], 500);
        }
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'push_notifications' => 'boolean',
                'email_notifications' => 'boolean',
                'sms_notifications' => 'boolean',
                'marketing_notifications' => 'boolean',
                'notification_types' => 'array',
                'notification_types.*' => 'boolean',
                'quiet_hours' => 'array',
                'quiet_hours.enabled' => 'boolean',
                'quiet_hours.start_time' => 'string|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
                'quiet_hours.end_time' => 'string|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $currentPreferences = $user->notification_preferences ?? [];

            $newPreferences = array_merge($currentPreferences, [
                'push' => $request->get('push_notifications', $currentPreferences['push'] ?? true),
                'email' => $request->get('email_notifications', $currentPreferences['email'] ?? true),
                'sms' => $request->get('sms_notifications', $currentPreferences['sms'] ?? true),
                'marketing' => $request->get('marketing_notifications', $currentPreferences['marketing'] ?? false),
                'types' => array_merge(
                    $currentPreferences['types'] ?? [],
                    $request->get('notification_types', [])
                ),
                'quiet_hours' => array_merge(
                    $currentPreferences['quiet_hours'] ?? [
                        'enabled' => false,
                        'start' => '22:00',
                        'end' => '07:00'
                    ],
                    $request->get('quiet_hours', [])
                )
            ]);

            $user->update(['notification_preferences' => $newPreferences]);

            Log::info('Notification preferences updated', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Notification preferences updated successfully',
                'data' => [
                    'preferences' => $newPreferences
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Update notification preferences failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notification preferences'
            ], 500);
        }
    }

    /**
     * Test notification (for debugging)
     */
    public function testNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:push,email,sms',
                'title' => 'required|string|max:255',
                'message' => 'required|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Send test notification
            $notification = $this->notificationService->sendNotification(
                $user,
                $request->title,
                $request->message,
                'test',
                [$request->type]
            );

            return response()->json([
                'success' => true,
                'message' => 'Test notification sent successfully',
                'data' => [
                    'notification_id' => $notification->id
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Test notification failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification'
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats(Request $request)
    {
        try {
            $user = $request->user();
            
            $stats = [
                'total_notifications' => Notification::where('user_id', $user->id)->count(),
                'unread_notifications' => Notification::where('user_id', $user->id)->whereNull('read_at')->count(),
                'notifications_this_week' => Notification::where('user_id', $user->id)
                                                        ->where('created_at', '>=', now()->subWeek())
                                                        ->count(),
                'by_category' => Notification::where('user_id', $user->id)
                                            ->selectRaw('category, COUNT(*) as count')
                                            ->groupBy('category')
                                            ->pluck('count', 'category')
                                            ->toArray(),
                'by_type' => Notification::where('user_id', $user->id)
                                       ->selectRaw('type, COUNT(*) as count')
                                       ->groupBy('type')
                                       ->pluck('count', 'type')
                                       ->toArray()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Get notification stats failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notification statistics'
            ], 500);
        }
    }
}