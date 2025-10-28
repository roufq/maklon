<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::forUser(Auth::id())
                                    ->latest()
                                    ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::forUser(Auth::id())
                   ->unread()
                   ->update(['read_at' => now()]);

        return redirect()->back()
                        ->with('success', 'All notifications marked as read.');
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return redirect()->back()
                        ->with('success', 'Notification deleted successfully.');
    }

    public function getUnreadCount()
    {
        $count = Notification::forUser(Auth::id())
                            ->unread()
                            ->count();

        return response()->json(['count' => $count]);
    }
}
