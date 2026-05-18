<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\ReviewRequest;
use App\Models\Review;
use App\Services\LoyaltyService;

class ReviewController extends Controller
{
    public function store(ReviewRequest $request, LoyaltyService $loyaltyService)
    {
        $review = Review::query()->create(array_merge($request->validated(), [
            'user_id' => $request->user()->id,
            'is_verified' => $request->user()->orders()->whereHas('items', fn ($query) => $query->where('product_id', $request->validated('product_id')))->exists(),
        ]));

        $loyaltyService->addBonusPoints($request->user(), "review:{$review->id}");

        return $this->ok(['review' => $review], 201);
    }
}
