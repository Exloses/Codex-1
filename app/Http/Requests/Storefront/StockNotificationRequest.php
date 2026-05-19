<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;

class StockNotificationRequest extends AuthorizedRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('guest_email')) {
            $this->merge([
                'guest_email' => strtolower(trim((string) $this->input('guest_email'))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'guest_email' => [$this->user() ? 'nullable' : 'required', 'email', 'max:255'],
        ];
    }
}
