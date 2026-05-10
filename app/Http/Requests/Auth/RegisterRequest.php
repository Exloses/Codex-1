<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\AuthorizedRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'country' => ['nullable', 'string', 'size:2'],
            'currency' => ['nullable', 'string', 'size:3'],
            'language' => ['nullable', 'string', 'max:5'],
        ];
    }
}
