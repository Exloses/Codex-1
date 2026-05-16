<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;
use Illuminate\Validation\Rule;

class WishlistRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where('is_active', true),
            ],
        ];
    }
}
