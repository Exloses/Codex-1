<?php

namespace App\Http\Requests\Vendor;

use App\Http\Requests\AuthorizedRequest;

class VendorShipOrderRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'tracking_number' => ['required', 'string', 'max:255'],
            'carrier' => ['required', 'string', 'max:120'],
            'shipping_label' => ['nullable', 'string', 'max:255'],
        ];
    }
}
