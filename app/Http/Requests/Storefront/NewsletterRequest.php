<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;

class NewsletterRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return ['email' => ['required', 'email', 'max:255']];
    }
}
