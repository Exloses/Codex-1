<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;

class ProductQuestionRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return ['question' => ['required', 'string', 'max:2000']];
    }
}
