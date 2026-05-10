<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\AuthorizedRequest;

class ShippingRatesRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'destination' => ['required', 'array'],
            'parcel' => ['required', 'array'],
        ];
    }
}
