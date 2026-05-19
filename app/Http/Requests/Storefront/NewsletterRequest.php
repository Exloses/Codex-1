<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;

class NewsletterRequest extends AuthorizedRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => is_string($this->email) ? str($this->email)->lower()->trim()->toString() : $this->email,
        ]);
    }

    public function rules(): array
    {
        return ['email' => ['required', 'email', 'max:255']];
    }
}
