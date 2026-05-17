<?php

namespace App\Http\Requests\Vendor;

use App\Enums\OrderTrackingStatus;
use App\Http\Requests\AuthorizedRequest;
use Illuminate\Validation\Rule;

class VendorTrackingEventRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in([
                OrderTrackingStatus::Processing->value,
                OrderTrackingStatus::Shipped->value,
                OrderTrackingStatus::InTransit->value,
                OrderTrackingStatus::OutForDelivery->value,
                OrderTrackingStatus::Delivered->value,
                OrderTrackingStatus::Failed->value,
                OrderTrackingStatus::Returned->value,
            ])],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'location' => ['nullable', 'string', 'max:255'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
            'carrier' => ['nullable', 'string', 'max:120'],
            'shipping_label' => ['nullable', 'string', 'max:255'],
            'occurred_at' => ['nullable', 'date'],
        ];
    }
}
