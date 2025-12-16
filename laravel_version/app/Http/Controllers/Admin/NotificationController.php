<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Show notifications page
     */
    public function index()
    {
        $notificationStatus = $this->getNotificationStatus();
        $notifications = $this->getNotifications();

        return view('admin.notifications.index', compact('notificationStatus', 'notifications'));
    }

    /**
     * Show messages page
     */
    public function messages()
    {
        $messages = Contact::orderBy('dPosted', 'desc')->get();

        return view('admin.notifications.messages', compact('messages'));
    }

    /**
     * Get notification status
     */
    public function getNotificationStatus()
    {
        $config = Configuration::where('config_key', 'notificationStatus')->first();

        return (object) [
            'notificationStatus' => $config ? $config->config_value : 'On'
        ];
    }

    /**
     * Update notification status
     */
    public function updateNotificationStatus(Request $request)
    {
        $request->validate([
            'notificationstatus' => 'required|in:On,Off'
        ]);

        try {
            Configuration::updateOrCreate(
                ['config_key' => 'notificationStatus'],
                ['config_value' => $request->notificationstatus]
            );

            Log::info('Notification status updated', [
                'admin_id' => Auth::user()->id,
                'status' => $request->notificationstatus
            ]);

            return back()->with('success', 'Notification status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Notification status update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update notification status.']);
        }
    }

    /**
     * Get all notifications (using Contact table for now as per PHP system)
     */
    public function getNotifications()
    {
        return Contact::orderBy('dPosted', 'desc')->get()->map(function ($contact) {
            return (object) [
                'msgId' => $contact->msgId,
                'subject' => $contact->subject,
                'message' => $contact->message,
                'msgfor' => 3, // General for contact messages
                'dPosted' => $contact->dPosted
            ];
        });
    }

    /**
     * Add new notification
     */
    public function addNotification(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:200',
            'message' => 'required|string',
            'msgfor' => 'required|integer|in:1,2,3'
        ]);

        try {
            // For now, store in contact table as per PHP system structure
            Contact::create([
                'sId' => 0, // System message
                'name' => 'System',
                'contact' => 'system@vtu.com',
                'subject' => $request->subject,
                'message' => $request->message,
                'dPosted' => now()
            ]);

            Log::info('Notification added', [
                'admin_id' => Auth::user()->id,
                'subject' => $request->subject,
                'msgfor' => $request->msgfor
            ]);

            return back()->with('success', 'Notification added successfully.');
        } catch (\Exception $e) {
            Log::error('Add notification error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to add notification.']);
        }
    }

    /**
     * Delete notification
     */
    public function deleteNotification(Request $request)
    {
        $request->validate([
            'msgId' => 'required|integer'
        ]);

        try {
            $notification = Contact::find($request->msgId);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found.'
                ]);
            }

            $notification->delete();

            Log::info('Notification deleted', [
                'admin_id' => Auth::user()->id,
                'msgId' => $request->msgId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete notification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification.'
            ]);
        }
    }

    /**
     * Send email to user
     */
    public function sendEmailToUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:200',
            'message' => 'required|string'
        ]);

        try {
            // Find user
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->withErrors(['email' => 'User not found with this email address.']);
            }

            // Send email using Laravel's Mail facade
            $emailData = [
                'subject' => $request->subject,
                'message' => $request->message,
                'user' => $user
            ];

            // For now, just log the email (in production, you'd use Mail::send)
            Log::info('Email sent to user', [
                'admin_id' => Auth::user()->id,
                'to_email' => $request->email,
                'subject' => $request->subject
            ]);

            // You would uncomment this in production with proper email configuration
            /*
            Mail::send('emails.admin-notification', $emailData, function ($message) use ($request, $user) {
                $message->to($request->email, $user->name)
                       ->subject($request->subject)
                       ->from(config('mail.from.address'), config('mail.from.name'));
            });
            */

            return back()->with('success', 'Email sent successfully to ' . $request->email);
        } catch (\Exception $e) {
            Log::error('Send email error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to send email.']);
        }
    }

    /**
     * Get contact messages for admin
     */
    public function getContactMessages()
    {
        $messages = Contact::with('user')
            ->orderBy('dPosted', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Delete contact message
     */
    public function deleteContactMessage(Request $request)
    {
        $request->validate([
            'msgId' => 'required|integer'
        ]);

        try {
            $message = Contact::find($request->msgId);

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found.'
                ]);
            }

            $message->delete();

            Log::info('Contact message deleted', [
                'admin_id' => Auth::user()->id,
                'msgId' => $request->msgId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete contact message error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete message.'
            ]);
        }
    }

    /**
     * Send bulk notification
     */
    public function sendBulkNotification(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:200',
            'message' => 'required|string',
            'target_audience' => 'required|in:all,subscribers,agents',
            'send_email' => 'boolean',
            'send_sms' => 'boolean'
        ]);

        try {
            $targetAudience = $request->target_audience;
            $sentCount = 0;

            // Get target users
            $users = collect();

            if ($targetAudience === 'all') {
                $users = User::active()->get();
            } elseif ($targetAudience === 'subscribers') {
                $users = User::where('sUserType', 'User')->active()->get();
            } elseif ($targetAudience === 'agents') {
                $users = User::where('sUserType', 'Agent')->active()->get();
            }

            // Create notification record
            Contact::create([
                'sId' => 0,
                'name' => 'System Notification',
                'contact' => 'admin@vtu.com',
                'subject' => $request->subject,
                'message' => $request->message,
                'dPosted' => now()
            ]);

            // Send notifications
            foreach ($users as $user) {
                if ($request->send_email && $user->email) {
                    // Log email sending (implement actual email sending in production)
                    Log::info('Bulk email notification', [
                        'to' => $user->email,
                        'subject' => $request->subject
                    ]);
                    $sentCount++;
                }

                if ($request->send_sms && $user->phone) {
                    // Log SMS sending (implement actual SMS sending in production)
                    Log::info('Bulk SMS notification', [
                        'to' => $user->phone,
                        'message' => $request->subject
                    ]);
                }
            }

            Log::info('Bulk notification sent', [
                'admin_id' => Auth::user()->id,
                'target_audience' => $targetAudience,
                'sent_count' => $sentCount
            ]);

            return back()->with('success', "Bulk notification sent to {$sentCount} users successfully.");
        } catch (\Exception $e) {
            Log::error('Bulk notification error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to send bulk notification.']);
        }
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStats()
    {
        try {
            $stats = [
                'total_notifications' => Contact::count(),
                'recent_messages' => Contact::where('dPosted', '>=', now()->subDays(7))->count(),
                'notification_status' => $this->getNotificationStatus()->notificationStatus,
                'active_users' => User::active()->count(),
                'subscribers' => User::where('sUserType', 'User')->active()->count(),
                'agents' => User::where('sUserType', 'Agent')->active()->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Get notification stats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notification statistics.'
            ]);
        }
    }

    /**
     * Preview notification template
     */
    public function previewNotification(Request $request)
    {
        $request->validate([
            'subject' => 'required|string',
            'message' => 'required|string',
            'template_type' => 'required|in:email,sms,push'
        ]);

        try {
            $preview = [
                'subject' => $request->subject,
                'message' => $request->message,
                'template_type' => $request->template_type,
                'preview_html' => $this->generateNotificationHTML($request->subject, $request->message, $request->template_type)
            ];

            return response()->json([
                'success' => true,
                'preview' => $preview
            ]);
        } catch (\Exception $e) {
            Log::error('Preview notification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate preview.'
            ]);
        }
    }

    /**
     * Generate notification HTML for preview
     */
    private function generateNotificationHTML($subject, $message, $type)
    {
        $html = '';

        if ($type === 'email') {
            $html = '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <h2 style="color: #333;">' . htmlspecialchars($subject) . '</h2>
                    <div style="background: #f9f9f9; padding: 20px; border-radius: 5px;">
                        ' . nl2br(htmlspecialchars($message)) . '
                    </div>
                    <footer style="margin-top: 20px; color: #666; font-size: 12px;">
                        <p>This is a notification from VTU System</p>
                    </footer>
                </div>
            ';
        } elseif ($type === 'sms') {
            $html = '<div style="font-family: monospace; background: #333; color: #fff; padding: 10px; border-radius: 5px;">' .
                htmlspecialchars($subject . ': ' . $message) . '</div>';
        } else {
            $html = '<div style="background: #007bff; color: white; padding: 15px; border-radius: 5px;">' .
                '<strong>' . htmlspecialchars($subject) . '</strong><br>' .
                htmlspecialchars($message) . '</div>';
        }

        return $html;
    }
}
