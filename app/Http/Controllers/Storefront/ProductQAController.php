<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\ProductAnswerRequest;
use App\Http\Requests\Storefront\ProductQuestionRequest;
use App\Models\Product;
use App\Models\ProductAnswer;
use App\Models\ProductQuestion;

class ProductQAController extends Controller
{
    public function store(ProductQuestionRequest $request, Product $product)
    {
        $question = ProductQuestion::query()->create([
            'product_id' => $product->id,
            'user_id' => $request->user()->id,
            'question' => $request->validated('question'),
            'is_public' => true,
        ]);

        return $this->ok(['question' => $question], 201);
    }

    public function answer(ProductAnswerRequest $request, ProductQuestion $question)
    {
        $question->loadMissing('product');
        $this->authorize('manage', $question->product);

        $answer = ProductAnswer::query()->create([
            'question_id' => $question->id,
            'user_id' => $request->user()->id,
            'answer' => $request->validated('answer'),
            'is_vendor' => $request->user()->vendor?->id === $question->product->vendor_id,
            'is_verified' => true,
        ]);

        return $this->ok(['answer' => $answer], 201);
    }
}
