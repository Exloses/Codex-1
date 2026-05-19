<?php

namespace App\Notifications;

use App\Models\NewsletterSubscriber;

class NewsletterWelcomeNotification extends GlobalDropshipNotification
{
    public function __construct(NewsletterSubscriber $subscriber)
    {
        parent::__construct($subscriber, [
            'unsubscribeUrl' => route('newsletter.unsubscribe', $subscriber->token),
        ]);
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable)
    {
        $payload = $this->mailPayload($notifiable, [
            'name' => $notifiable->user?->name ?: 'there',
            'headline' => 'Welcome to GlobalDrop updates',
            'intro' => 'You will get curated drops, product stories, and useful shopping updates from GlobalDrop.',
        ]);

        return $this->makeMailMessage('Welcome to GlobalDrop updates', 'emails.newsletter-welcome', $payload);
    }
}
