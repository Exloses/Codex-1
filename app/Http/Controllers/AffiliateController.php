<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;

class AffiliateController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function landing()
    {
        return $this->placeholder(__METHOD__);
    }

    public function track()
    {
        return $this->placeholder(__METHOD__);
    }

    public function register()
    {
        return $this->placeholder(__METHOD__);
    }

    public function dashboard()
    {
        return $this->placeholder(__METHOD__);
    }

    public function commissions()
    {
        return $this->placeholder(__METHOD__);
    }

    public function storePayoutMethod()
    {
        return $this->placeholder(__METHOD__);
    }

    public function requestPayout()
    {
        return $this->placeholder(__METHOD__);
    }

    public function payoutHistory()
    {
        return $this->placeholder(__METHOD__);
    }

    public function generateLink()
    {
        return $this->placeholder(__METHOD__);
    }
}
