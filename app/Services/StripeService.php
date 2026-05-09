<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Arr;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StripeService
{
    public function __construct(
        private readonly StripeClient $stripe,
        private readonly string $webhookSecret,
    ) {}

    public function createPaymentIntent(Order $order, array $metadata = []): array
    {
        $intent = $this->stripe->paymentIntents->create([
            'amount' => $this->toCents($order->total_usd),
            'currency' => 'usd',
            'metadata' => array_merge([
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
            ], $metadata),
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        $order->forceFill([
            'payment_method' => 'stripe',
            'stripe_payment_id' => $intent->id,
        ])->save();

        return $intent->toArray();
    }

    public function handleWebhook(string $payload, ?string $signature): array
    {
        try {
            $event = Webhook::constructEvent($payload, $signature ?? '', $this->webhookSecret);
        } catch (SignatureVerificationException $exception) {
            throw new BadRequestHttpException('Invalid Stripe webhook signature.', $exception);
        }

        return match ($event->type) {
            'payment_intent.succeeded' => $this->handlePaymentSuccess($event->data->object->toArray()),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object->toArray()),
            default => ['handled' => false, 'type' => $event->type],
        };
    }

    public function handlePaymentSuccess(array $paymentIntent): array
    {
        $order = $this->findOrder($paymentIntent);

        if ($order) {
            $order->forceFill([
                'status' => 'paid',
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
                'stripe_payment_id' => $paymentIntent['id'] ?? $order->stripe_payment_id,
            ])->save();
        }

        return ['handled' => true, 'status' => 'paid', 'order_id' => $order?->id];
    }

    public function handlePaymentFailed(array $paymentIntent): array
    {
        $order = $this->findOrder($paymentIntent);

        if ($order) {
            $order->forceFill([
                'payment_status' => 'failed',
                'payment_method' => 'stripe',
                'stripe_payment_id' => $paymentIntent['id'] ?? $order->stripe_payment_id,
            ])->save();
        }

        return ['handled' => true, 'status' => 'failed', 'order_id' => $order?->id];
    }

    private function findOrder(array $paymentIntent): ?Order
    {
        $orderId = Arr::get($paymentIntent, 'metadata.order_id');
        $paymentIntentId = $paymentIntent['id'] ?? null;

        if ($orderId) {
            return Order::query()->find($orderId);
        }

        if ($paymentIntentId) {
            return Order::query()->where('stripe_payment_id', $paymentIntentId)->first();
        }

        return null;
    }

    private function toCents(float|int|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }
}
