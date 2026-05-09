<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PayPalService
{
    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $baseUrl,
    ) {}

    public function createOrder(Order $order, array $options = []): array
    {
        $response = $this->client()->post('/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $order->order_number,
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => number_format((float) $order->total_usd, 2, '.', ''),
                ],
            ]],
            'application_context' => $options['application_context'] ?? [
                'brand_name' => config('app.name'),
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'PAY_NOW',
            ],
        ])->throw()->json();

        $order->forceFill([
            'payment_method' => 'paypal',
            'paypal_order_id' => $response['id'] ?? null,
        ])->save();

        return $response;
    }

    public function captureOrder(string $paypalOrderId): array
    {
        $response = $this->client()
            ->post("/v2/checkout/orders/{$paypalOrderId}/capture")
            ->throw()
            ->json();

        $order = Order::query()->where('paypal_order_id', $paypalOrderId)->first();

        if (($response['status'] ?? null) === 'COMPLETED' && $order) {
            $order->forceFill([
                'status' => 'paid',
                'payment_status' => 'paid',
                'payment_method' => 'paypal',
            ])->save();
        }

        return $response;
    }

    public function handleWebhook(array $payload): array
    {
        $eventType = $payload['event_type'] ?? 'unknown';
        $paypalOrderId = data_get($payload, 'resource.supplementary_data.related_ids.order_id')
            ?? data_get($payload, 'resource.id');

        $order = $paypalOrderId
            ? Order::query()->where('paypal_order_id', $paypalOrderId)->first()
            : null;

        if ($order && in_array($eventType, ['CHECKOUT.ORDER.APPROVED', 'PAYMENT.CAPTURE.COMPLETED'], true)) {
            $order->forceFill([
                'status' => 'paid',
                'payment_status' => 'paid',
                'payment_method' => 'paypal',
            ])->save();
        }

        return ['handled' => (bool) $order, 'type' => $eventType, 'order_id' => $order?->id];
    }

    private function client(): PendingRequest
    {
        return Http::withToken($this->accessToken())
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->asJson()
            ->timeout(15);
    }

    private function accessToken(): string
    {
        return Cache::remember('paypal.access_token', now()->addMinutes(50), function () {
            return Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->baseUrl($this->baseUrl)
                ->asForm()
                ->post('/v1/oauth2/token', ['grant_type' => 'client_credentials'])
                ->throw()
                ->json('access_token');
        });
    }
}
