<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class SocialAuthController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function redirect()
    {
        return $this->placeholder(__METHOD__);
    }

    public function callback()
    {
        return $this->placeholder(__METHOD__);
    }
}
