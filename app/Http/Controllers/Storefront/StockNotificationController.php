<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\StockNotificationRequest;
use App\Services\ProductAlertService;

class StockNotificationController extends Controller
{
    public function store(StockNotificationRequest $request, ProductAlertService $alerts)
    {
        $result = $alerts->createStockAlert($request->validated(), $request->user());
        $alert = $result['alert'];

        if ($request->wantsJson()) {
            return $this->ok([
                'message' => $result['message'],
                'alert' => [
                    'type' => ProductAlertService::TYPE_STOCK,
                    'status' => 'active',
                    'created' => $alert->wasRecentlyCreated,
                ],
            ], $alert->wasRecentlyCreated ? 201 : 200);
        }

        return back()->with('status', $result['message']);
    }
}
