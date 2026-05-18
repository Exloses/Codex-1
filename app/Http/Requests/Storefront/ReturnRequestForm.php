<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;
use App\Models\Order;
use App\Services\ReturnRefundService;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ReturnRequestForm extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'reason' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'refund_method' => ['nullable', 'string', Rule::in(ReturnRefundService::REFUND_METHODS)],
            'images' => ['nullable', 'array', 'max:4'],
            'images.*' => ['image', 'max:4096'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $order = Order::query()->find($this->input('order_id'));

            if (! $order || ! app(ReturnRefundService::class)->canCreateForOrder($this->user(), $order)) {
                $validator->errors()->add('order_id', 'This order is not eligible for a return request.');
            }
        });
    }
}
