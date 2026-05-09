<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class ReturnController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function store()
    {
        return $this->placeholder(__METHOD__);
    }

    public function show()
    {
        return $this->placeholder(__METHOD__);
    }
}
