<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\PriceAlertRequest;
use App\Models\StockNotification;

class PriceAlertController extends Controller
{
    public function store(PriceAlertRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['type'] = 'price';

        $alert = StockNotification::query()->updateOrCreate([
            'user_id' => $data['user_id'],
            'product_id' => $data['product_id'],
            'product_variant_id' => $data['product_variant_id'] ?? null,
            'type' => 'price',
        ], $data + ['is_notified' => false]);

        return $this->ok(['alert' => $alert], 201);
    }
}
