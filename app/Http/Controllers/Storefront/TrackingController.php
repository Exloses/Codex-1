<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\TrackingRequest;
use App\Models\Order;
use App\Services\OrderTrackingService;
use Inertia\Inertia;
use Inertia\Response;

class TrackingController extends Controller
{
    public function __construct(private readonly OrderTrackingService $trackingService)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Storefront/TrackOrder');
    }

    public function track(TrackingRequest $request)
    {
        $order = $this->findTrackableOrder($request);

        return $this->ok(['order' => $this->trackingService->payload($order)]);
    }

    public function status(TrackingRequest $request)
    {
        $order = $this->findTrackableOrder($request);

        return $this->ok(['order' => $this->trackingService->payload($order)]);
    }

    private function findTrackableOrder(TrackingRequest $request): Order
    {
        return Order::query()
            ->where('order_number', $request->validated('order_number'))
            ->where(function ($query) use ($request) {
                $email = $request->validated('email');

                $query->where('guest_email', $email)
                    ->orWhereHas('user', fn ($user) => $user->where('email', $email));
            })
            ->firstOrFail();
    }
}
