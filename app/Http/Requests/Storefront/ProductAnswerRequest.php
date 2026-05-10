<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;

class ProductAnswerRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return ['answer' => ['required', 'string', 'max:3000']];
    }
}
