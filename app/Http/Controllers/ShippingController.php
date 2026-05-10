<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\ShippingRatesRequest;
use App\Services\EasyPostService;

class ShippingController extends Controller
{
    public function getRates(ShippingRatesRequest $request, EasyPostService $easyPostService)
    {
        $rates = $easyPostService->getRates(
            destinationAddress: $request->validated('destination'),
            parcel: $request->validated('parcel'),
        );

        return $this->ok(['rates' => $rates]);
    }
}
