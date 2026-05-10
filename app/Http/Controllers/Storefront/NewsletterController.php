<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\NewsletterRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(NewsletterRequest $request)
    {
        $subscriber = NewsletterSubscriber::query()->updateOrCreate(
            ['email' => $request->validated('email')],
            [
                'user_id' => $request->user()?->id,
                'status' => 'active',
                'token' => Str::random(40),
            ],
        );

        return $this->ok(['subscriber' => $subscriber]);
    }

    public function unsubscribe(string $token)
    {
        NewsletterSubscriber::query()->where('token', $token)->update(['status' => 'unsubscribed']);

        return $this->ok(['message' => 'You have been unsubscribed.']);
    }
}
