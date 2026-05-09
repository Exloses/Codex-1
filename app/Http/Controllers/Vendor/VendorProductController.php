<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class VendorProductController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function index()
    {
        return $this->placeholder(__METHOD__);
    }

    public function create()
    {
        return $this->placeholder(__METHOD__);
    }

    public function store()
    {
        return $this->placeholder(__METHOD__);
    }

    public function show()
    {
        return $this->placeholder(__METHOD__);
    }

    public function edit()
    {
        return $this->placeholder(__METHOD__);
    }

    public function update()
    {
        return $this->placeholder(__METHOD__);
    }

    public function destroy()
    {
        return $this->placeholder(__METHOD__);
    }
}
