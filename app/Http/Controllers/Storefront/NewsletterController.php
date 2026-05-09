<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class NewsletterController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function subscribe()
    {
        return $this->placeholder(__METHOD__);
    }

    public function unsubscribe()
    {
        return $this->placeholder(__METHOD__);
    }
}
