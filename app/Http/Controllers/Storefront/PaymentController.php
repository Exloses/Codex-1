<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function createStripeIntent()
    {
        return $this->placeholder(__METHOD__);
    }

    public function createPayPalOrder()
    {
        return $this->placeholder(__METHOD__);
    }

    public function capturePayPalOrder()
    {
        return $this->placeholder(__METHOD__);
    }

    public function stripeWebhook()
    {
        return $this->placeholder(__METHOD__);
    }
}
