<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;

class CurrencyController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function getRates()
    {
        return $this->placeholder(__METHOD__);
    }
}
