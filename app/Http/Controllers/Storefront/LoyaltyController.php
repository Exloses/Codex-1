<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class LoyaltyController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function index()
    {
        return $this->placeholder(__METHOD__);
    }
}
