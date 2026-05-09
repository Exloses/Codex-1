<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class CartController extends Controller
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

    public function update()
    {
        return $this->placeholder(__METHOD__);
    }

    public function destroy()
    {
        return $this->placeholder(__METHOD__);
    }
}
