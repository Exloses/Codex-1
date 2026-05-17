<?php

namespace App\Events;

use App\Models\OrderTrackingEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderTrackingUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(public OrderTrackingEvent $trackingEvent)
    {
    }
}
