<?php

namespace App\Http\Requests\Vendor;

use App\Http\Requests\AuthorizedRequest;

class WithdrawalRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'amount_idr' => ['required', 'numeric', 'min:10000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
