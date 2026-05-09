<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function index()
    {
        return $this->placeholder(__METHOD__);
    }

    public function store()
    {
        return $this->placeholder(__METHOD__);
    }

    public function success()
    {
        return $this->placeholder(__METHOD__);
    }

    public function applyCoupon()
    {
        return $this->placeholder(__METHOD__);
    }

    public function redeemPoints()
    {
        return $this->placeholder(__METHOD__);
    }

    public function guestStore()
    {
        return $this->placeholder(__METHOD__);
    }
}
