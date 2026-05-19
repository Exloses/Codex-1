<?php

namespace App\Notifications;

use App\Models\NewsletterSubscriber;

class NewsletterBroadcastNotification extends GlobalDropshipNotification
{
    public function __construct(
        private readonly string $subject,
        private readonly string $message,
        NewsletterSubscriber $subscriber,
    ) {
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
            'subject' => $this->subject,
            'message' => $this->message,
        ]);

        return $this->makeMailMessage($this->subject, 'emails.newsletter-broadcast', $payload);
    }
}
