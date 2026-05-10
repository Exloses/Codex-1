<?php

namespace App\Http\Requests\Storefront;

class PriceAlertRequest extends StockNotificationRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'target_price_usd' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
