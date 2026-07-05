<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a specific notification.
     */
    public function destroy($id)
    {
        Auth::user()->notifications()->findOrFail($id)->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification deleted.');
    }

    /**
     * Fetch unread notifications for the bell icon via AJAX.
     */
    public function getUnread()
    {
        $user = Auth::user();
        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $user->unreadNotifications()->latest()->take(5)->get()->map(function($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->data['title'] ?? 'System Notification',
                    'message' => $n->data['message'] ?? '',
                    'time' => $n->created_at->diffForHumans(),
                ];
            })
        ]);
    }

    /**
     * Show notification preferences.
     */
    public function preferences()
    {
        $user = Auth::user();
        $preferences = $user->notification_preferences ?? [
            'email' => true,
            'sms' => false,
            'in_app' => true,
            'reminders' => true,
            'security' => true,
        ];

        return view('profile.notifications', compact('user', 'preferences'));
    }

    /**
     * Update notification preferences.
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'email' => 'boolean',
            'sms' => 'boolean',
            'in_app' => 'boolean',
            'reminders' => 'boolean',
            'security' => 'boolean',
        ]);

        Auth::user()->update([
            'notification_preferences' => $validated
        ]);

        return back()->with('success', 'Notification preferences updated successfully.');
    }
}
