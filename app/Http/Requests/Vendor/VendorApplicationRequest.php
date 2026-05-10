<?php

namespace App\Http\Requests\Vendor;

use App\Http\Requests\AuthorizedRequest;

class VendorApplicationRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'province' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
        ];
    }
}
