<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;

class CartRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'product_id' => [$this->isMethod('post') ? 'required' : 'sometimes', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'custom_note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
