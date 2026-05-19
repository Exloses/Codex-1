<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\AnonymousNotifiable;

class StockAvailableNotification extends GlobalDropshipNotification
{
    public function __construct(mixed $product = null, array $data = [])
    {
        parent::__construct($product, $data);
    }

    public function via(object $notifiable): array
    {
        if ($notifiable instanceof AnonymousNotifiable) {
            return ['mail'];
        }

        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $productName = $this->value('name', $this->value('product.name', 'Requested product'));
        $payload = $this->mailPayload($notifiable, [
            'productName' => $productName,
            'actionUrl' => $this->value('product_url', $this->productUrl($this->value('product', $this->resource))),
        ]);

        return $this->makeMailMessage("{$productName} is back in stock", 'emails.stock-available', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        $productName = $this->value('name', $this->value('product.name', 'Requested product'));

        return $this->makeDatabasePayload(
            'stock_available',
            'Stock available',
            "{$productName} is available again.",
            $this->value('product_url', $this->productUrl($this->value('product', $this->resource))),
            ['product_name' => $productName]
        );
    }
}
