<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class SupportTicketCreatedNotification extends GlobalDropshipNotification
{
    public function __construct(mixed $ticket = null, array $data = [])
    {
        parent::__construct($ticket, $data);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticketNumber = $this->value('ticket_number', 'Support ticket');
        $payload = $this->mailPayload($notifiable, [
            'ticketNumber' => $ticketNumber,
            'subject' => $this->value('subject', 'Support request'),
            'status' => $this->value('status', 'open'),
            'priority' => $this->value('priority', 'normal'),
            'message' => $this->value('message', ''),
            'actionUrl' => $this->value('action_url', data_get($this->resource, 'id') ? url('/support/'.data_get($this->resource, 'id')) : url('/support')),
        ]);

        return $this->makeMailMessage("Support ticket {$ticketNumber} opened", 'emails.support-ticket-created', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        $ticketNumber = $this->value('ticket_number', 'Support ticket');

        return $this->makeDatabasePayload(
            'support_ticket_created',
            'Support ticket opened',
            "{$ticketNumber} has been opened.",
            $this->value('action_url', data_get($this->resource, 'id') ? url('/support/'.data_get($this->resource, 'id')) : url('/support')),
            [
                'ticket_number' => $ticketNumber,
                'subject' => $this->value('subject'),
                'status' => $this->value('status', 'open'),
                'priority' => $this->value('priority', 'normal'),
            ]
        );
    }
}
