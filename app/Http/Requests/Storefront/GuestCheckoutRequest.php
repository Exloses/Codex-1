<?php

namespace App\Http\Requests\Storefront;

class GuestCheckoutRequest extends CheckoutRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_name' => ['required', 'string', 'max:255'],
        ]);
    }
}
