<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;

class CheckoutRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'address_id' => ['nullable', 'integer', 'exists:addresses,id'],
            'buyer_currency' => ['nullable', 'string', 'size:3'],
            'payment_method' => ['nullable', 'string', 'in:stripe,paypal'],
            'shipping_cost_usd' => ['nullable', 'numeric', 'min:0'],
            'discount_usd' => ['nullable', 'numeric', 'min:0'],
            'affiliate_code' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
