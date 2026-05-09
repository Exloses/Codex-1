<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class PreferenceController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function setCurrency()
    {
        return $this->placeholder(__METHOD__);
    }

    public function setLanguage()
    {
        return $this->placeholder(__METHOD__);
    }
}
