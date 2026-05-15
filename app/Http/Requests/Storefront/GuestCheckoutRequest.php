<?php

namespace App\Http\Requests\Storefront;

class GuestCheckoutRequest extends CheckoutRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:50'],
            'guest_address_line1' => ['required', 'string', 'max:255'],
            'guest_address_line2' => ['nullable', 'string', 'max:255'],
            'guest_city' => ['required', 'string', 'max:120'],
            'guest_state' => ['nullable', 'string', 'max:120'],
            'guest_postal_code' => ['required', 'string', 'max:40'],
            'guest_country' => ['required', 'string', 'size:2'],
        ]);
    }
}
