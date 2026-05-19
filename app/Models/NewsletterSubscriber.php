<?php

namespace App\Models;

use App\Services\NewsletterService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    use Notifiable;

    protected $guarded = [];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (NewsletterSubscriber $subscriber) {
            $subscriber->email = app(NewsletterService::class)->normalizeEmail($subscriber->email);
            $subscriber->status = $subscriber->status ?: NewsletterService::STATUS_ACTIVE;
            $subscriber->token = $subscriber->token ?: Str::random(64);
            $subscriber->subscribed_at ??= now();
        });

        static::updating(function (NewsletterSubscriber $subscriber) {
            if ($subscriber->isDirty('email')) {
                $subscriber->email = app(NewsletterService::class)->normalizeEmail($subscriber->email);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }
}
