<?php

namespace App\Http\Controllers;
use App\Models\Notification;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        // Newest notifications on top based on ID (insertion order)
        $notifications = Notification::where('user_id', auth()->id())
            ->orderByDesc('id')
            ->get();

        // Mark all as read when viewing the page
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);
        $notification->update(['is_read' => true]);
        return back();
    }

    public function destroy(Notification $notification)
    {
        // Ensure user can only delete their own notifications
        abort_unless($notification->user_id === auth()->id(), 403);

        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted.');
    }
}
