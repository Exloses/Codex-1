<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;

    protected function ok(array $data = [], int $status = 200)
    {
        return response()->json(array_merge(['ok' => true], $data), $status);
    }
}
