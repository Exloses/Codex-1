<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;

class PreferenceRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'currency' => ['nullable', 'string', 'size:3'],
            'language' => ['nullable', 'string', 'max:5'],
        ];
    }
}
