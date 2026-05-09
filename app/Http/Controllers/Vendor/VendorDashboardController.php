<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class VendorDashboardController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function index()
    {
        return $this->placeholder(__METHOD__);
    }

    public function registerVendor()
    {
        return $this->placeholder(__METHOD__);
    }
}
