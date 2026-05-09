<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function show()
    {
        return $this->placeholder(__METHOD__);
    }
}
