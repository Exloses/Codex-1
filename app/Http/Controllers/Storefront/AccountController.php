<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\AddressRequest;
use App\Models\Address;
use App\Models\Order;
use App\Services\OrderTrackingService;
use App\Services\ReturnRefundService;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function __construct(
        private readonly OrderTrackingService $trackingService,
        private readonly ReturnRefundService $returnRefundService,
    )
    {
    }

    public function index(): Response
    {
        return Inertia::render('Account/Index', ['user' => auth()->user()->load('vendor', 'loyaltyPoint')]);
    }

    public function updateProfile()
    {
        auth()->user()->update(request()->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'size:2'],
            'currency' => ['nullable', 'string', 'size:3'],
            'language' => ['nullable', 'string', 'max:5'],
        ]));

        return back();
    }

    public function orders(): Response
    {
        return Inertia::render('Account/Orders', [
            'orders' => auth()->user()->orders()
                ->select(['id', 'user_id', 'order_number', 'status', 'total_usd', 'payment_status', 'created_at'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function orderDetail(Order $order): Response
    {
        $this->authorize('view', $order);
        $currentReturn = $order->returnRequests()->latest()->first();

        return Inertia::render('Account/OrderDetail', [
            'order' => $order->load([
                'items:id,order_id,product_id,product_variant_id,vendor_id,quantity,price_usd,subtotal_usd',
                'items.product:id,name,slug,selling_price,compare_price,stock,average_rating',
                'items.productVariant:id,product_id,combination,price,stock,image',
                'dropshipOrders:id,order_id,vendor_id,dropship_number,status,tracking_number,carrier,shipped_at,delivered_at',
                'trackingEvents',
                'latestTrackingEvent',
            ]),
            'tracking' => $this->trackingService->payload($order),
            'returnEligibility' => [
                'can_request' => $this->returnRefundService->canCreateForOrder(auth()->user(), $order),
                'eligible_statuses' => ReturnRefundService::ELIGIBLE_ORDER_STATUSES,
            ],
            'currentReturn' => $currentReturn
                ? $this->returnRefundService->payload($currentReturn)
                : null,
        ]);
    }

    public function orderTracking(Order $order)
    {
        $this->authorize('view', $order);

        return $this->ok(['order' => $this->trackingService->payload($order)]);
    }

    public function addresses(): Response
    {
        return Inertia::render('Account/Addresses', [
            'addresses' => auth()->user()->addresses()
                ->select(['id', 'user_id', 'full_name', 'phone', 'address_line1', 'address_line2', 'city', 'state', 'postal_code', 'country', 'is_default'])
                ->latest()
                ->get(),
        ]);
    }

    public function storeAddress(AddressRequest $request)
    {
        $this->syncDefaultAddress($request);
        $address = $request->user()->addresses()->create($request->validated());

        return $this->ok(['address' => $address], 201);
    }

    public function updateAddress(AddressRequest $request, Address $address)
    {
        $this->authorize('update', $address);
        $this->syncDefaultAddress($request, $address);
        $address->update($request->validated());

        return $this->ok(['address' => $address]);
    }

    public function destroyAddress(Address $address)
    {
        $this->authorize('delete', $address);
        $address->delete();

        return $this->ok();
    }

    private function syncDefaultAddress(AddressRequest $request, ?Address $address = null): void
    {
        if ($request->boolean('is_default')) {
            $request->user()->addresses()
                ->when($address, fn ($query) => $query->whereKeyNot($address->id))
                ->update(['is_default' => false]);
        }
    }
}
