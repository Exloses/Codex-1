<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\ProductAnswerRequest;
use App\Http\Requests\Storefront\ProductQuestionRequest;
use App\Models\Product;
use App\Models\ProductAnswer;
use App\Models\ProductQuestion;
use App\Notifications\ProductQuestionAskedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ProductQAController extends Controller
{
    public function store(ProductQuestionRequest $request, Product $product): JsonResponse|RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $question = ProductQuestion::query()->create([
            'product_id' => $product->id,
            'user_id' => $request->user()->id,
            'question' => $request->validated('question'),
            'is_public' => true,
        ]);

        $this->notifyVendor($product, $question);

        if ($request->expectsJson()) {
            return $this->ok(['question' => $this->questionPayload($question)], 201);
        }

        return back()->with('status', 'Your question was posted. The vendor will be notified.');
    }

    public function answer(ProductAnswerRequest $request, ProductQuestion $question): JsonResponse|RedirectResponse
    {
        $question->loadMissing('product.vendor.user');
        $product = $question->product;

        abort_unless($product && $product->is_active, 404);

        $this->authorize('manage', $product);

        $answer = ProductAnswer::query()->create([
            'question_id' => $question->id,
            'user_id' => $request->user()->id,
            'answer' => $request->validated('answer'),
            'is_vendor' => $request->user()->vendor?->id === $product->vendor_id,
            'is_verified' => true,
        ]);

        if ($request->expectsJson()) {
            return $this->ok(['answer' => $this->answerPayload($answer)], 201);
        }

        return back()->with('status', 'Your answer was posted.');
    }

    private function notifyVendor(Product $product, ProductQuestion $question): void
    {
        $product->loadMissing('vendor.user');
        $vendorUser = $product->vendor?->user;

        if (! $vendorUser) {
            return;
        }

        $vendorUser->notify(new ProductQuestionAskedNotification($question, [
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'question_excerpt' => str($question->question)->limit(180)->toString(),
            'action_url' => url('/products/'.$product->slug),
        ]));
    }

    private function questionPayload(ProductQuestion $question): array
    {
        return [
            'id' => $question->id,
            'question' => $question->question,
            'is_public' => (bool) $question->is_public,
            'asker_label' => 'Customer',
            'created_at' => $question->created_at?->toISOString(),
        ];
    }

    private function answerPayload(ProductAnswer $answer): array
    {
        return [
            'id' => $answer->id,
            'answer' => $answer->answer,
            'is_vendor' => (bool) $answer->is_vendor,
            'is_verified' => (bool) $answer->is_verified,
            'author_label' => $answer->is_vendor ? 'Vendor' : 'GlobalDrop team',
            'created_at' => $answer->created_at?->toISOString(),
        ];
    }
}
