<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

abstract class GlobalDropshipNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected mixed $resource = null,
        protected array $data = [],
    ) {
    }

    protected function makeMailMessage(string $subject, string $view, array $payload): MailMessage
    {
        return (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($subject)
            ->view($view, ['data' => $payload]);
    }

    protected function makeDatabasePayload(
        string $type,
        string $title,
        string $message,
        ?string $actionUrl,
        array $payload = [],
    ): array {
        return [
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'resource_type' => is_object($this->resource) ? $this->resource::class : null,
            'resource_id' => data_get($this->resource, 'id'),
            'data' => $payload,
        ];
    }

    protected function mailPayload(object $notifiable, array $payload): array
    {
        return array_replace([
            'brandName' => 'GlobalDropship',
            'logoText' => 'GD',
            'supportEmail' => config('mail.from.address'),
            'companyAddress' => 'GlobalDropship, Jakarta, Indonesia',
            'unsubscribeUrl' => url('/newsletter/unsubscribe/'.($this->data['unsubscribe_token'] ?? 'preferences')),
            'storeUrl' => url('/'),
            'accountUrl' => url('/account'),
            'recipientName' => data_get($notifiable, 'name', 'there'),
        ], $payload, $this->data);
    }

    protected function value(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return data_get($this->resource, $key, $default);
    }

    protected function money(mixed $value, string $currency = 'USD'): string
    {
        return $currency.' '.number_format((float) ($value ?? 0), 2);
    }

    protected function percent(mixed $value): string
    {
        return rtrim(rtrim(number_format((float) ($value ?? 0), 2), '0'), '.').'%';
    }

    protected function productUrl(mixed $product = null): string
    {
        $product ??= $this->resource;
        $slug = data_get($product, 'slug');

        return $slug ? url('/products/'.$slug) : url('/products/'.data_get($product, 'id', ''));
    }

    protected function orderItems(mixed $order = null, ?int $vendorId = null): array
    {
        $order ??= $this->resource;
        $items = $this->data['items'] ?? null;

        if ($items === null && is_object($order) && method_exists($order, 'items')) {
            $items = $order->items()->with('product')->get();
        }

        return collect($items ?? [])
            ->filter(fn ($item) => $vendorId === null || (int) data_get($item, 'vendor_id') === $vendorId)
            ->map(fn ($item) => [
                'name' => data_get($item, 'name')
                    ?? data_get($item, 'product.name')
                    ?? 'Product #'.data_get($item, 'product_id', ''),
                'quantity' => (int) data_get($item, 'quantity', 1),
                'price' => $this->money(data_get($item, 'price_usd', data_get($item, 'price', 0))),
                'subtotal' => $this->money(data_get($item, 'subtotal_usd', data_get($item, 'subtotal', 0))),
            ])
            ->values()
            ->all();
    }

    protected function shippingAddress(mixed $order = null): string
    {
        $order ??= $this->resource;
        $address = $this->data['shipping_address'] ?? data_get($order, 'address');

        if (! $address && is_object($order) && method_exists($order, 'address')) {
            $address = $order->address()->first();
        }

        if (is_string($address)) {
            return $address;
        }

        $lines = array_filter([
            data_get($address, 'full_name'),
            data_get($address, 'phone'),
            data_get($address, 'address_line1'),
            data_get($address, 'address_line2'),
            trim(implode(', ', array_filter([
                data_get($address, 'city'),
                data_get($address, 'state'),
                data_get($address, 'postal_code'),
            ]))),
            data_get($address, 'country'),
        ]);

        return $lines ? implode("\n", $lines) : 'Shipping address will be confirmed shortly.';
    }

    protected function readableDate(mixed $date): string
    {
        if ($date instanceof \DateTimeInterface) {
            return $date->format('M j, Y');
        }

        return (string) ($date ?: now()->format('M j, Y'));
    }
}
