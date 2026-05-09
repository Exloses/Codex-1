<?php

namespace App\Http\Controllers\Concerns;

trait ReturnsPlaceholderResponses
{
    protected function placeholder(string $action)
    {
        return response()->json([
            'message' => "{$action} placeholder. Full implementation will be added in Task 8.",
        ]);
    }
}
