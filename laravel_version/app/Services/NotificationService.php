<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification to user
     */
    public function sendNotification(User $user, string $title, string $message, string $type = 'info', array $channels = ['push'])
    {
        try {
            // Create notification record
            $notification = Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'category' => 'general',
                'channels' => $channels,
                'data' => []
            ]);

            // Send via requested channels
            foreach ($channels as $channel) {
                $this->sendViaChannel($user, $notification, $channel);
            }

            return $notification;

        } catch (\Exception $e) {
            Log::error('Notification sending failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send welcome notification
     */
    public function sendWelcomeNotification(User $user)
    {
        return $this->sendNotification(
            $user,
            'Welcome to ' . config('app.name'),
            'Thank you for joining us! Start exploring our services.',
            'welcome',
            ['push', 'email']
        );
    }

    /**
     * Send via specific channel
     */
    private function sendViaChannel(User $user, Notification $notification, string $channel)
    {
        switch ($channel) {
            case 'push':
                $this->sendPushNotification($user, $notification);
                break;
            case 'email':
                $this->sendEmailNotification($user, $notification);
                break;
            case 'sms':
                $this->sendSmsNotification($user, $notification);
                break;
        }
    }

    /**
     * Send push notification
     */
    private function sendPushNotification(User $user, Notification $notification)
    {
        if ($user->fcm_token) {
            // Implementation would use FCM service
            Log::info('Push notification sent', [
                'user_id' => $user->id,
                'notification_id' => $notification->id
            ]);
        }
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification(User $user, Notification $notification)
    {
        // Implementation would use mail service
        Log::info('Email notification sent', [
            'user_id' => $user->id,
            'notification_id' => $notification->id
        ]);
    }

    /**
     * Send SMS notification
     */
    private function sendSmsNotification(User $user, Notification $notification)
    {
        if ($user->phone) {
            // Implementation would use SMS service
            Log::info('SMS notification sent', [
                'user_id' => $user->id,
                'notification_id' => $notification->id
            ]);
        }
    }
}