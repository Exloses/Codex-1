<?php

namespace App\Http\Requests\Affiliate;

use App\Http\Requests\AuthorizedRequest;

class PayoutRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'payout_method_id' => ['required', 'integer', 'exists:affiliate_payout_methods,id'],
            'amount_usd' => ['required', 'numeric', 'min:10'],
            'payout_type' => ['nullable', 'string', 'max:50'],
        ];
    }
}
