<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\VendorSettingsRequest;
use Inertia\Inertia;
use Inertia\Response;

class VendorSettingsController extends Controller
{
    public function index(): Response
    {
        $vendor = auth()->user()->vendor;
        abort_if(! $vendor, 403, 'Vendor profile not found.');
        $this->authorize('manage', $vendor);

        return Inertia::render('Vendor/Settings/Index', ['vendor' => $vendor]);
    }

    public function update(VendorSettingsRequest $request)
    {
        $vendor = $request->user()->vendor;
        abort_if(! $vendor, 403, 'Vendor profile not found.');
        $this->authorize('manage', $vendor);

        $vendor->update($request->validated());

        return redirect()->route('vendor.settings');
    }
}
