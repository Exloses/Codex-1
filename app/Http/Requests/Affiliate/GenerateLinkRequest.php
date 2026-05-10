<?php

namespace App\Http\Requests\Affiliate;

use App\Http\Requests\AuthorizedRequest;

class GenerateLinkRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return ['url' => ['nullable', 'url', 'max:2000']];
    }
}
