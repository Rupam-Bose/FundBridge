<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /** Return unread notifications as JSON (for polling) */
    public function list()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->take(15)
            ->get()
            ->map(fn($n) => [
                'id'       => $n->id,
                'type'     => $n->data['type']    ?? 'info',
                'title'    => $n->data['title']   ?? 'Notification',
                'body'     => $n->data['body']    ?? '',
                'url'      => $n->data['url']     ?? '#',
                'icon'     => $n->data['icon']    ?? 'fa-bell',
                'read'     => $n->read_at !== null,
                'time'     => $n->created_at->diffForHumans(),
            ]);

        $unreadCount = Auth::user()->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    /** Mark one notification as read */
    public function markRead(string $id)
    {
        Auth::user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    }

    /** Mark ALL notifications as read */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('status', 'All notifications marked as read.');
    }
}
