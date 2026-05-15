<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class SupportTicketReplyNotification extends GlobalDropshipNotification
{
    public function __construct(mixed $ticket = null, mixed $reply = null, array $data = [])
    {
        parent::__construct($ticket, array_replace(['reply' => $reply], $data));
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticketNumber = $this->value('ticket_number', 'Support ticket');
        $reply = $this->data['reply'] ?? null;
        $payload = $this->mailPayload($notifiable, [
            'ticketNumber' => $ticketNumber,
            'subject' => $this->value('subject', 'Support request'),
            'replyMessage' => data_get($reply, 'message', $this->data['reply_message'] ?? ''),
            'senderName' => $this->data['sender_name'] ?? data_get($reply, 'user.name', 'Support team'),
            'actionUrl' => $this->value('action_url', data_get($this->resource, 'id') ? url('/support/'.data_get($this->resource, 'id')) : url('/support')),
        ]);

        return $this->makeMailMessage("New reply on {$ticketNumber}", 'emails.support-ticket-reply', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        $ticketNumber = $this->value('ticket_number', 'Support ticket');

        return $this->makeDatabasePayload(
            'support_ticket_reply',
            'New support ticket reply',
            "A new reply was added to {$ticketNumber}.",
            $this->value('action_url', data_get($this->resource, 'id') ? url('/support/'.data_get($this->resource, 'id')) : url('/support')),
            [
                'ticket_number' => $ticketNumber,
                'subject' => $this->value('subject'),
            ]
        );
    }
}
