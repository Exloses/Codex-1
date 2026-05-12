<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class OrderShippedNotification extends GlobalDropshipNotification
{
    public function __construct(mixed $shipment = null, array $data = [])
    {
        parent::__construct($shipment, $data);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $trackingNumber = $this->value('tracking_number', 'Pending tracking');
        $payload = $this->mailPayload($notifiable, [
            'orderNumber' => $this->value('order.order_number', $this->value('dropship_number', 'Your order')),
            'trackingNumber' => $trackingNumber,
            'carrier' => $this->value('carrier', 'Carrier update pending'),
            'estimatedArrival' => $this->value('estimated_arrival', $this->value('estimated_delivery', '3-7 business days')),
            'actionUrl' => $this->value('tracking_url', url('/track-order')),
        ]);

        return $this->makeMailMessage('Your order is on the way', 'emails.order-shipped', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        $trackingNumber = $this->value('tracking_number', 'Pending tracking');

        return $this->makeDatabasePayload(
            'order_shipped',
            'Order shipped',
            "Tracking number: {$trackingNumber}.",
            $this->value('tracking_url', url('/track-order')),
            ['tracking_number' => $trackingNumber, 'carrier' => $this->value('carrier')]
        );
    }
}
