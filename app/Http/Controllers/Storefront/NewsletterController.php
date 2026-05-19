<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\NewsletterRequest;
use App\Services\NewsletterService;

class NewsletterController extends Controller
{
    public function subscribe(NewsletterRequest $request, NewsletterService $newsletter)
    {
        $result = $newsletter->subscribe($request->validated('email'), $request->user());

        if ($request->wantsJson()) {
            return $this->ok([
                'message' => $result['message'],
                'subscribed' => true,
            ]);
        }

        return back()->with('status', $result['message']);
    }

    public function unsubscribe(string $token, NewsletterService $newsletter)
    {
        $updated = $newsletter->unsubscribe($token);
        $message = $updated
            ? 'You have been unsubscribed from GlobalDrop newsletter emails.'
            : 'We could not update that newsletter preference link. It may have already been used or expired.';

        return response()->view('newsletter.unsubscribed', [
            'message' => $message,
        ], $updated ? 200 : 404);
    }
}
