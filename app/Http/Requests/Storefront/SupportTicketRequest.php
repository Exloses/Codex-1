<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;

class SupportTicketRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:100'],
            'priority' => ['nullable', 'string', 'max:50'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
        ];
    }
}
