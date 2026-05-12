<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class OrderConfirmationNotification extends GlobalDropshipNotification
{
    public function __construct(mixed $order = null, array $data = [])
    {
        parent::__construct($order, $data);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = $this->value('order_number', 'Pending order');
        $payload = $this->mailPayload($notifiable, [
            'orderNumber' => $orderNumber,
            'items' => $this->orderItems(),
            'total' => $this->money($this->value('total_usd', $this->value('total', 0))),
            'shippingAddress' => $this->shippingAddress(),
            'actionUrl' => $this->value('track_url', url('/track-order')),
        ]);

        return $this->makeMailMessage("Order {$orderNumber} confirmed", 'emails.order-confirmation', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        $orderNumber = $this->value('order_number', 'Pending order');

        return $this->makeDatabasePayload(
            'order_confirmation',
            'Order confirmed',
            "Your order {$orderNumber} has been confirmed.",
            $this->value('track_url', url('/track-order')),
            ['order_number' => $orderNumber, 'total' => $this->value('total_usd')]
        );
    }
}
