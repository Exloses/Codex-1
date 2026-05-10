<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\AuthorizedRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
