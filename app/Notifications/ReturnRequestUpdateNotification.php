<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class ReturnRequestUpdateNotification extends GlobalDropshipNotification
{
    public function __construct(mixed $returnRequest = null, array $data = [])
    {
        parent::__construct($returnRequest, $data);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $returnNumber = $this->value('return_number', 'Return request');
        $status = $this->value('new_status', $this->value('status', 'updated'));
        $payload = $this->mailPayload($notifiable, [
            'returnNumber' => $returnNumber,
            'newStatus' => $status,
            'adminNotes' => $this->value('admin_notes', 'No additional notes from support.'),
            'actionUrl' => $this->value('return_url', data_get($this->resource, 'id') ? url('/returns/'.data_get($this->resource, 'id')) : url('/account/orders')),
        ]);

        return $this->makeMailMessage("Return {$returnNumber} updated", 'emails.return-request-update', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        $returnNumber = $this->value('return_number', 'Return request');
        $status = $this->value('new_status', $this->value('status', 'updated'));

        return $this->makeDatabasePayload(
            'return_request_update',
            'Return request updated',
            "{$returnNumber} status changed to {$status}.",
            $this->value('return_url', data_get($this->resource, 'id') ? url('/returns/'.data_get($this->resource, 'id')) : url('/account/orders')),
            ['return_number' => $returnNumber, 'status' => $status]
        );
    }
}
