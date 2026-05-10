<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;

class PaymentRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'paypal_order_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
