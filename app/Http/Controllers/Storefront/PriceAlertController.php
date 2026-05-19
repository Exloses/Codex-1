<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\PriceAlertRequest;
use App\Services\ProductAlertService;

class PriceAlertController extends Controller
{
    public function store(PriceAlertRequest $request, ProductAlertService $alerts)
    {
        $result = $alerts->createPriceAlert($request->validated(), $request->user());
        $alert = $result['alert'];

        if ($request->wantsJson()) {
            return $this->ok([
                'message' => $result['message'],
                'alert' => [
                    'type' => ProductAlertService::TYPE_PRICE,
                    'status' => 'active',
                    'created' => $alert->wasRecentlyCreated,
                    'target_price_usd' => (string) $alert->target_price_usd,
                ],
            ], $alert->wasRecentlyCreated ? 201 : 200);
        }

        return back()->with('status', $result['message']);
    }
}
