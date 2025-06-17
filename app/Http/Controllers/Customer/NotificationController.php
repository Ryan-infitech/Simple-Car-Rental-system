<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('customer');
    }

    public function index(Request $request)
    {
        $query = auth()->user()->notifications();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by read status
        if ($request->filled('read_status')) {
            if ($request->read_status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->read_status === 'read') {
                $query->where('is_read', true);
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get counts for tabs
        $counts = [
            'all' => auth()->user()->notifications()->count(),
            'unread' => auth()->user()->notifications()->where('is_read', false)->count(),
            'booking' => auth()->user()->notifications()->where('type', 'booking')->count(),
            'payment' => auth()->user()->notifications()->where('type', 'payment')->count(),
            'general' => auth()->user()->notifications()->where('type', 'general')->count(),
        ];

        return view('customer.notifications.index', compact('notifications', 'counts'));
    }

    public function show(Notification $notification)
    {
        // Check if notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            return redirect()->route('customer.notifications.index')
                ->with('error', 'Notifikasi tidak ditemukan.');
        }

        // Mark as read if not already read
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return view('customer.notifications.show', compact('notification'));
    }

    public function markAsRead(Notification $notification)
    {
        // Check if notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Notifikasi tidak ditemukan.'], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil ditandai sebagai dibaca.'
        ]);
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi berhasil ditandai sebagai dibaca.');
    }

    public function destroy(Notification $notification)
    {
        // Check if notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            return back()->with('error', 'Notifikasi tidak ditemukan.');
        }

        $notification->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark_read,delete',
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:notifications,id'
        ]);

        $notifications = auth()->user()->notifications()
            ->whereIn('id', $request->notification_ids);

        switch ($request->action) {
            case 'mark_read':
                $notifications->update(['is_read' => true]);
                $message = 'Notifikasi berhasil ditandai sebagai dibaca.';
                break;
            case 'delete':
                $notifications->delete();
                $message = 'Notifikasi berhasil dihapus.';
                break;
        }

        return back()->with('success', $message);
    }

    public function getUnreadCount()
    {
        $count = auth()->user()->notifications()
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function getRecent()
    {
        $notifications = auth()->user()->notifications()
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json($notifications);
    }
}
