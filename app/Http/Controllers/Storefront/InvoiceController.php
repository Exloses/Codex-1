<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function download()
    {
        return $this->placeholder(__METHOD__);
    }
}
