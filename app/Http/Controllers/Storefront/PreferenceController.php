<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\PreferenceRequest;

class PreferenceController extends Controller
{
    public function setCurrency(PreferenceRequest $request)
    {
        return back()->withCookie(cookie('currency', strtoupper($request->validated('currency', 'USD')), 60 * 24 * 365));
    }

    public function setLanguage(PreferenceRequest $request)
    {
        return back()->withCookie(cookie('language', $request->validated('language', 'en'), 60 * 24 * 365));
    }
}
