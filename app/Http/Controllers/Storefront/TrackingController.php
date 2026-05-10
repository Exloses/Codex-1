<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\TrackingRequest;
use App\Models\Order;
use Inertia\Inertia;
use Inertia\Response;

class TrackingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Storefront/TrackOrder');
    }

    public function track(TrackingRequest $request)
    {
        $order = Order::query()
            ->with('dropshipOrders')
            ->where('order_number', $request->validated('order_number'))
            ->when($request->filled('email'), fn ($query) => $query->where(function ($nested) use ($request) {
                $nested->where('guest_email', $request->validated('email'))
                    ->orWhereHas('user', fn ($user) => $user->where('email', $request->validated('email')));
            }))
            ->firstOrFail();

        return $this->ok(['order' => $order]);
    }
}
