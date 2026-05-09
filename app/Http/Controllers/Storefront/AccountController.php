<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function index()
    {
        return $this->placeholder(__METHOD__);
    }

    public function updateProfile()
    {
        return $this->placeholder(__METHOD__);
    }

    public function orders()
    {
        return $this->placeholder(__METHOD__);
    }

    public function orderDetail()
    {
        return $this->placeholder(__METHOD__);
    }

    public function addresses()
    {
        return $this->placeholder(__METHOD__);
    }

    public function storeAddress()
    {
        return $this->placeholder(__METHOD__);
    }

    public function updateAddress()
    {
        return $this->placeholder(__METHOD__);
    }

    public function destroyAddress()
    {
        return $this->placeholder(__METHOD__);
    }
}
