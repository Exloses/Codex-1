<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\VendorShipOrderRequest;
use App\Models\DropshipOrder;
use Inertia\Inertia;
use Inertia\Response;

class VendorOrderController extends Controller
{
    public function index(): Response
    {
        $vendor = auth()->user()->vendor;
        abort_if(! $vendor, 403, 'Vendor profile not found.');
        $this->authorize('manage', $vendor);

        return Inertia::render('Vendor/Orders/Index', [
            'orders' => $vendor->dropshipOrders()
                ->select(['id', 'order_id', 'vendor_id', 'dropship_number', 'status', 'vendor_total_idr', 'tracking_number', 'carrier', 'created_at'])
                ->with('order:id,order_number,total_usd,status,payment_status,created_at')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function confirm(DropshipOrder $dropship)
    {
        $this->authorizeDropship($dropship);

        $dropship->update(['status' => 'confirmed']);

        return $this->ok(['dropship_order' => $dropship]);
    }

    public function markShipped(VendorShipOrderRequest $request, DropshipOrder $dropship)
    {
        $this->authorizeDropship($dropship);

        $dropship->update($request->validated() + [
            'status' => 'shipped',
            'shipped_at' => now(),
        ]);

        return $this->ok(['dropship_order' => $dropship]);
    }

    private function authorizeDropship(DropshipOrder $dropship): void
    {
        $vendor = auth()->user()->vendor;
        abort_if(! $vendor || $dropship->vendor_id !== $vendor->id, 403);
        $this->authorize('manage', $vendor);
    }
}
