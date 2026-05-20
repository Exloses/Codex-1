<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class ProductQuestionAskedNotification extends GlobalDropshipNotification
{
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $payload = $this->mailPayload($notifiable, [
            'productName' => $this->value('product_name', 'your product'),
            'questionExcerpt' => $this->value('question_excerpt', 'A customer asked a product question.'),
            'actionUrl' => $this->value('action_url', url('/vendor/products')),
        ]);

        return $this->makeMailMessage('New product question', 'emails.product-question-asked', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        $productName = $this->value('product_name', 'your product');

        return $this->makeDatabasePayload(
            'product_question_asked',
            'New product question',
            "A customer asked about {$productName}.",
            $this->value('action_url', url('/vendor/products')),
            [
                'product_name' => $productName,
                'question_excerpt' => $this->value('question_excerpt'),
            ]
        );
    }
}
