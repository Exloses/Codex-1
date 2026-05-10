<?php

namespace App\Http\Requests\Affiliate;

use App\Http\Requests\AuthorizedRequest;

class PayoutMethodRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:50'],
            'paypal_email' => ['nullable', 'email', 'max:255'],
            'wise_email' => ['nullable', 'email', 'max:255'],
            'wise_currency' => ['nullable', 'string', 'size:3'],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'bank_account' => ['nullable', 'string', 'max:120'],
            'bank_holder' => ['nullable', 'string', 'max:120'],
            'bank_country' => ['nullable', 'string', 'size:2'],
            'swift_code' => ['nullable', 'string', 'max:50'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }
}
