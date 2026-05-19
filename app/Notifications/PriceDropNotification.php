<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\AnonymousNotifiable;

class PriceDropNotification extends GlobalDropshipNotification
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
        $productName = $this->value('name', $this->value('product.name', 'Watched product'));
        $newPrice = $this->value('new_price_usd', $this->value('selling_price', 0));
        $payload = $this->mailPayload($notifiable, [
            'productName' => $productName,
            'oldPrice' => $this->money($this->value('old_price_usd', $this->value('compare_price', $newPrice))),
            'newPrice' => $this->money($newPrice),
            'actionUrl' => $this->value('product_url', $this->productUrl($this->value('product', $this->resource))),
        ]);

        return $this->makeMailMessage("Price drop: {$productName}", 'emails.price-drop', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        $productName = $this->value('name', $this->value('product.name', 'Watched product'));

        return $this->makeDatabasePayload(
            'price_drop',
            'Price drop',
            "{$productName} has a new lower price.",
            $this->value('product_url', $this->productUrl($this->value('product', $this->resource))),
            ['product_name' => $productName, 'new_price_usd' => $this->value('new_price_usd', $this->value('selling_price'))]
        );
    }
}
