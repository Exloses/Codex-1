<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;

class ReturnRequestForm extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'reason' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'refund_method' => ['nullable', 'string', 'max:100'],
            'images' => ['nullable', 'array'],
        ];
    }
}
