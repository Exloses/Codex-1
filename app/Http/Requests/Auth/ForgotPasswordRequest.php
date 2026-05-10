<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\AuthorizedRequest;

class ForgotPasswordRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return ['email' => ['required', 'email']];
    }
}
