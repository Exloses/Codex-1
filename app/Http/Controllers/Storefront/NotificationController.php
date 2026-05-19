<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(Request $request): Response
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20)
            ->through(fn (DatabaseNotification $notification) => $this->notificationPayload($notification));

        return Inertia::render('Account/Notifications', [
            'notifications' => $notifications,
            'unreadCount' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function feed(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (DatabaseNotification $notification) => $this->notificationPayload($notification));

        return $this->ok([
            'notifications' => $notifications,
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(Request $request, DatabaseNotification $notification): JsonResponse
    {
        abort_unless(
            $notification->notifiable_type === $request->user()::class
            && (int) $notification->notifiable_id === (int) $request->user()->getKey(),
            404,
        );

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        return $this->ok([
            'notification' => $this->notificationPayload($notification->fresh()),
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return $this->ok([
            'unread_count' => 0,
        ]);
    }

    private function notificationPayload(DatabaseNotification $notification): array
    {
        $data = is_array($notification->data) ? $notification->data : [];
        $type = $this->safeText(data_get($data, 'type') ?: class_basename($notification->type));
        $title = $this->safeText(data_get($data, 'title') ?: str($type)->headline()->toString());
        $message = $this->safeText(data_get($data, 'message') ?: $title);

        return [
            'id' => $notification->id,
            'key' => str($type)->slug('-')->toString(),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $this->safeActionUrl(data_get($data, 'action_url') ?: data_get($data, 'url')),
            'read_at' => $notification->read_at?->toIso8601String(),
            'created_at' => $notification->created_at?->toIso8601String(),
            'created_at_human' => $notification->created_at?->diffForHumans(),
        ];
    }

    private function safeText(mixed $value): string
    {
        $text = trim(strip_tags((string) $value));

        return str($text)->limit(180, '...')->toString();
    }

    private function safeActionUrl(mixed $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '' || str_starts_with($url, '//')) {
            return null;
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        $parts = parse_url($url);
        $appParts = parse_url(config('app.url'));

        if (
            is_array($parts)
            && is_array($appParts)
            && ($parts['host'] ?? null) === ($appParts['host'] ?? null)
        ) {
            $path = $parts['path'] ?? '/';
            $query = isset($parts['query']) ? '?'.$parts['query'] : '';

            return $path.$query;
        }

        return null;
    }
}
