<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class ProductQAController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function store()
    {
        return $this->placeholder(__METHOD__);
    }

    public function answer()
    {
        return $this->placeholder(__METHOD__);
    }
}
