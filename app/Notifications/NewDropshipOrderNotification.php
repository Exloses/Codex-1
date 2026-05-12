<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class NewDropshipOrderNotification extends GlobalDropshipNotification
{
    public function __construct(mixed $dropshipOrder = null, array $data = [])
    {
        parent::__construct($dropshipOrder, $data);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->value('order', $this->resource);
        $deadline = $this->value('deadline', now()->addDay()->format('M j, Y H:i'));
        $payload = $this->mailPayload($notifiable, [
            'dropshipNumber' => $this->value('dropship_number', 'New dropship order'),
            'items' => $this->orderItems($order, data_get($this->resource, 'vendor_id')),
            'shippingAddress' => $this->shippingAddress($order),
            'deadline' => $deadline,
            'actionUrl' => $this->value('vendor_order_url', url('/vendor/orders')),
        ]);

        return $this->makeMailMessage('New dropship order received', 'emails.new-dropship-order', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        $dropshipNumber = $this->value('dropship_number', 'New dropship order');

        return $this->makeDatabasePayload(
            'new_dropship_order',
            'New dropship order',
            "{$dropshipNumber} is ready for fulfillment.",
            $this->value('vendor_order_url', url('/vendor/orders')),
            ['dropship_number' => $dropshipNumber, 'deadline' => $this->value('deadline')]
        );
    }
}
