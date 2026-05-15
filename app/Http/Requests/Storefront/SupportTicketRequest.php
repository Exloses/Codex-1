<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;
use App\Models\SupportTicket;
use Illuminate\Validation\Rule;

class SupportTicketRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'priority' => ['nullable', 'string', Rule::in(SupportTicket::priorities())],
            'order_id' => [
                'nullable',
                'integer',
                Rule::exists('orders', 'id')->where(fn ($query) => $query->where('user_id', $this->user()->id)),
            ],
        ];
    }
}
