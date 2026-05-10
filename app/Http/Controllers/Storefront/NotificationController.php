<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Account/Notifications', [
            'notifications' => auth()->user()->notifications()->latest()->paginate(20),
            'unreadCount' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return $this->ok();
    }
}
