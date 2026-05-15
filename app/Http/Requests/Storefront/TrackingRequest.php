<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;

class TrackingRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'order_number' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
        ];
    }
}
