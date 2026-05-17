<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Enums\OrderTrackingSource;
use App\Enums\OrderTrackingStatus;
use App\Http\Requests\Vendor\VendorShipOrderRequest;
use App\Http\Requests\Vendor\VendorTrackingEventRequest;
use App\Models\DropshipOrder;
use App\Services\OrderTrackingService;
use Inertia\Inertia;
use Inertia\Response;

class VendorOrderController extends Controller
{
    public function __construct(private readonly OrderTrackingService $trackingService)
    {
    }

    public function index(): Response
    {
        $vendor = auth()->user()->vendor;
        abort_if(! $vendor, 403, 'Vendor profile not found.');
        $this->authorize('manage', $vendor);

        return Inertia::render('Vendor/Orders/Index', [
            'orders' => $vendor->dropshipOrders()
                ->select(['id', 'order_id', 'vendor_id', 'dropship_number', 'status', 'vendor_total_idr', 'tracking_number', 'carrier', 'created_at'])
                ->with('order:id,order_number,total_usd,status,payment_status,created_at')
                ->with(['trackingEvents' => fn ($query) => $query->latestEvent()->limit(3)])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function confirm(DropshipOrder $dropship)
    {
        $this->authorizeDropship($dropship);

        $this->trackingService->record($dropship->order, OrderTrackingStatus::Processing, [
            'dropship_order' => $dropship,
            'source' => OrderTrackingSource::Vendor,
            'title' => 'Vendor confirmed fulfillment',
            'description' => 'The vendor confirmed this order is being prepared.',
        ]);

        return $this->ok(['dropship_order' => $dropship->fresh()]);
    }

    public function markShipped(VendorShipOrderRequest $request, DropshipOrder $dropship)
    {
        $this->authorizeDropship($dropship);

        $this->trackingService->record($dropship->order, OrderTrackingStatus::Shipped, [
            'dropship_order' => $dropship,
            'source' => OrderTrackingSource::Vendor,
            'title' => 'Package shipped',
            'description' => 'The vendor handed the package to the carrier.',
            'tracking_number' => $request->validated('tracking_number'),
            'carrier' => $request->validated('carrier'),
            'shipping_label' => $request->validated('shipping_label'),
            'location' => $request->validated('location'),
        ]);

        return $this->ok(['dropship_order' => $dropship->fresh()]);
    }

    public function addTrackingEvent(VendorTrackingEventRequest $request, DropshipOrder $dropship)
    {
        $this->authorizeDropship($dropship);

        $this->trackingService->record($dropship->order, $request->validated('status'), [
            'dropship_order' => $dropship,
            'source' => OrderTrackingSource::Vendor,
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'location' => $request->validated('location'),
            'tracking_number' => $request->validated('tracking_number'),
            'carrier' => $request->validated('carrier'),
            'shipping_label' => $request->validated('shipping_label'),
            'occurred_at' => $request->date('occurred_at'),
        ]);

        return back();
    }

    private function authorizeDropship(DropshipOrder $dropship): void
    {
        $vendor = auth()->user()->vendor;
        abort_if(! $vendor || $dropship->vendor_id !== $vendor->id, 403);
        $this->authorize('manage', $vendor);
    }
}
