<?php

namespace App\Services;

use App\Models\NewsletterSubscriber;
use App\Models\User;
use App\Notifications\NewsletterBroadcastNotification;
use App\Notifications\NewsletterWelcomeNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsletterService
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_UNSUBSCRIBED = 'unsubscribed';

    public function subscribe(string $email, ?User $user = null): array
    {
        $email = $this->normalizeEmail($email);
        $sendWelcome = false;

        $subscriber = DB::transaction(function () use ($email, $user, &$sendWelcome) {
            $subscriber = NewsletterSubscriber::query()->where('email', $email)->lockForUpdate()->first();

            if (! $subscriber) {
                $sendWelcome = true;

                return NewsletterSubscriber::query()->create([
                    'email' => $email,
                    'user_id' => $user?->id,
                    'status' => self::STATUS_ACTIVE,
                    'token' => $this->newToken(),
                    'subscribed_at' => now(),
                    'unsubscribed_at' => null,
                ]);
            }

            $wasUnsubscribed = $subscriber->status !== self::STATUS_ACTIVE;
            $sendWelcome = $wasUnsubscribed;

            $subscriber->forceFill([
                'user_id' => $subscriber->user_id ?: $user?->id,
                'status' => self::STATUS_ACTIVE,
                'token' => $wasUnsubscribed ? $this->newToken() : $subscriber->token,
                'subscribed_at' => $wasUnsubscribed ? now() : ($subscriber->subscribed_at ?: now()),
                'unsubscribed_at' => null,
            ])->save();

            return $subscriber;
        });

        if ($sendWelcome) {
            $subscriber->notify(new NewsletterWelcomeNotification($subscriber));
        }

        return [
            'subscriber' => $subscriber,
            'sent_welcome' => $sendWelcome,
            'message' => $sendWelcome
                ? 'You are subscribed to GlobalDrop updates.'
                : 'You are already subscribed to GlobalDrop updates.',
        ];
    }

    public function unsubscribe(string $token): bool
    {
        if ($token === '' || strlen($token) < 32) {
            return false;
        }

        $subscriber = NewsletterSubscriber::query()
            ->where('token', $token)
            ->where('status', self::STATUS_ACTIVE)
            ->first();

        if (! $subscriber) {
            return false;
        }

        $subscriber->forceFill([
            'status' => self::STATUS_UNSUBSCRIBED,
            'unsubscribed_at' => now(),
        ])->save();

        return true;
    }

    public function broadcast(string $subject, string $message): int
    {
        $sent = 0;

        NewsletterSubscriber::query()
            ->where('status', self::STATUS_ACTIVE)
            ->orderBy('id')
            ->each(function (NewsletterSubscriber $subscriber) use ($subject, $message, &$sent) {
                $subscriber->notify(new NewsletterBroadcastNotification($subject, $message, $subscriber));
                $sent++;
            });

        return $sent;
    }

    public function normalizeEmail(string $email): string
    {
        return Str::lower(trim($email));
    }

    private function newToken(): string
    {
        return Str::random(64);
    }
}
