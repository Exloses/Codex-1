<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\ReturnRequestForm;
use App\Models\ReturnRequest;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ReturnController extends Controller
{
    public function store(ReturnRequestForm $request)
    {
        $returnRequest = ReturnRequest::query()->create(array_merge($request->validated(), [
            'user_id' => $request->user()->id,
            'return_number' => 'RET-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
            'status' => 'pending',
        ]));

        return $this->ok(['return' => $returnRequest], 201);
    }

    public function show(ReturnRequest $return): Response
    {
        $this->authorize('view', $return);

        return Inertia::render('Account/Returns', ['returnRequest' => $return]);
    }
}
