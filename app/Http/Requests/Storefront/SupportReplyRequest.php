<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;

class SupportReplyRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return ['message' => ['required', 'string', 'max:5000']];
    }
}
