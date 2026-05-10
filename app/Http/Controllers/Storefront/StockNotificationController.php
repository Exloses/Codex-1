<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\StockNotificationRequest;
use App\Models\StockNotification;

class StockNotificationController extends Controller
{
    public function store(StockNotificationRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['type'] = 'stock';

        $notification = StockNotification::query()->updateOrCreate([
            'user_id' => $data['user_id'],
            'product_id' => $data['product_id'],
            'product_variant_id' => $data['product_variant_id'] ?? null,
            'type' => 'stock',
        ], $data + ['is_notified' => false]);

        return $this->ok(['notification' => $notification], 201);
    }
}
