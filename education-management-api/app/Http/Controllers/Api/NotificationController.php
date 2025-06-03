<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request): JsonResponse
    {
        $notifications = $this->notificationService->getRecentNotifications(
            Auth::user(),
            $request->get('limit', 10)
        );

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $this->notificationService->getUnreadCount(Auth::user())
        ]);
    }

    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'count' => $this->notificationService->getUnreadCount(Auth::user())
        ]);
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'unread_count' => $this->notificationService->getUnreadCount(Auth::user())
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        $count = $this->notificationService->markAllAsRead(Auth::user());

        return response()->json([
            'message' => "{$count} notifications marked as read",
            'unread_count' => 0
        ]);
    }

    public function markAsUnread(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->markAsUnread();

        return response()->json([
            'message' => 'Notification marked as unread',
            'unread_count' => $this->notificationService->getUnreadCount(Auth::user())
        ]);
    }

    public function delete(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted',
            'unread_count' => $this->notificationService->getUnreadCount(Auth::user())
        ]);
    }

    public function deleteAll(): JsonResponse
    {
        Notification::where('user_id', Auth::id())->delete();

        return response()->json([
            'message' => 'All notifications deleted',
            'unread_count' => 0
        ]);
    }

    public function getByType(string $type): JsonResponse
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->where('type', $type)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['notifications' => $notifications]);
    }

    public function getHighPriority(): JsonResponse
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->where('priority', '>=', Notification::PRIORITY_HIGH)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['notifications' => $notifications]);
    }
}