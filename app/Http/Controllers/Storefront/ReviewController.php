<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\ReviewRequest;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(ReviewRequest $request)
    {
        $review = Review::query()->create(array_merge($request->validated(), [
            'user_id' => $request->user()->id,
            'is_verified' => $request->user()->orders()->whereHas('items', fn ($query) => $query->where('product_id', $request->validated('product_id')))->exists(),
        ]));

        return $this->ok(['review' => $review], 201);
    }
}
