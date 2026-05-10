<?php

namespace App\Http\Requests\Vendor;

use App\Http\Requests\AuthorizedRequest;

class VendorSettingsRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'store_name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'logo' => ['nullable', 'string', 'max:255'],
            'banner' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'bank_account' => ['nullable', 'string', 'max:120'],
            'bank_holder' => ['nullable', 'string', 'max:120'],
        ];
    }
}
