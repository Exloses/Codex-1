<?php

namespace App\Services;

use App\Enums\OrderTrackingSource;
use App\Enums\OrderTrackingStatus;
use App\Events\OrderTrackingUpdated;
use App\Models\DropshipOrder;
use App\Models\Order;
use App\Models\OrderTrackingEvent;
use App\Notifications\OrderShippedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OrderTrackingService
{
    public function record(Order $order, string|OrderTrackingStatus $status, array $attributes = []): OrderTrackingEvent
    {
        $status = $status instanceof OrderTrackingStatus ? $status : OrderTrackingStatus::fromValue($status);
        $source = $this->source($attributes['source'] ?? OrderTrackingSource::System);
        $dropshipOrder = $attributes['dropship_order'] ?? null;
        $dropshipOrderId = $attributes['dropship_order_id'] ?? $dropshipOrder?->id;

        if ($dropshipOrderId) {
            $dropshipOrder = $dropshipOrder instanceof DropshipOrder
                ? $dropshipOrder
                : DropshipOrder::query()->findOrFail($dropshipOrderId);

            if ((int) $dropshipOrder->order_id !== (int) $order->id) {
                throw new \InvalidArgumentException('The dropship order does not belong to the order being tracked.');
            }
        }

        return DB::transaction(function () use ($order, $status, $source, $attributes, $dropshipOrder) {
            $metadata = $attributes['metadata'] ?? [];
            foreach (['tracking_number', 'carrier', 'shipping_label'] as $field) {
                if (array_key_exists($field, $attributes)) {
                    $metadata[$field] = $attributes[$field];
                }
            }

            $event = OrderTrackingEvent::query()->create([
                'order_id' => $order->id,
                'dropship_order_id' => $dropshipOrder?->id,
                'status' => $status,
                'title' => $attributes['title'] ?? $status->label(),
                'description' => $attributes['description'] ?? $status->description(),
                'location' => $attributes['location'] ?? null,
                'occurred_at' => $attributes['occurred_at'] ?? now(),
                'source' => $source,
                'metadata' => $metadata ?: null,
            ]);

            $this->syncOrderState($order, $status);

            if ($dropshipOrder) {
                $this->syncDropshipState($dropshipOrder, $status, $metadata);
            }

            OrderTrackingUpdated::dispatch($event);
            $this->notifyImportantUpdate($event);

            return $event;
        });
    }

    public function payload(Order $order): array
    {
        $order->loadMissing([
            'trackingEvents',
            'latestTrackingEvent',
            'dropshipOrders:id,order_id,dropship_number,status,tracking_number,carrier,shipped_at,delivered_at',
        ]);

        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'latest_tracking_status' => $order->latestTrackingEvent?->status?->value ?? $order->status,
            'latest_tracking_label' => $order->latestTrackingEvent?->title,
            'tracking_events' => $order->trackingEvents->map(fn (OrderTrackingEvent $event) => $this->eventPayload($event))->values(),
            'dropship_orders' => $order->dropshipOrders->map(fn (DropshipOrder $dropshipOrder) => [
                'id' => $dropshipOrder->id,
                'dropship_number' => $dropshipOrder->dropship_number,
                'status' => $dropshipOrder->status,
                'tracking_number' => $dropshipOrder->tracking_number,
                'carrier' => $dropshipOrder->carrier,
                'shipped_at' => $dropshipOrder->shipped_at?->toISOString(),
                'delivered_at' => $dropshipOrder->delivered_at?->toISOString(),
            ])->values(),
        ];
    }

    public function eventPayload(OrderTrackingEvent $event): array
    {
        return [
            'id' => $event->id,
            'dropship_order_id' => $event->dropship_order_id,
            'status' => $event->status->value,
            'label' => $event->title,
            'title' => $event->title,
            'description' => $event->description,
            'location' => $event->location,
            'occurred_at' => $event->occurred_at?->toISOString(),
            'occurred_at_human' => $event->occurred_at?->format('M j, Y H:i'),
            'source' => $event->source->value,
            'metadata' => $event->metadata ?? [],
        ];
    }

    private function source(string|OrderTrackingSource $source): OrderTrackingSource
    {
        return $source instanceof OrderTrackingSource ? $source : OrderTrackingSource::fromValue($source);
    }

    private function syncOrderState(Order $order, OrderTrackingStatus $status): void
    {
        $updates = [];

        if ($orderStatus = $status->orderStatus()) {
            $updates['status'] = $orderStatus;
        }

        if ($status === OrderTrackingStatus::Paid) {
            $updates['payment_status'] = 'paid';
        }

        if ($updates) {
            $order->forceFill($updates)->save();
        }
    }

    private function syncDropshipState(DropshipOrder $dropshipOrder, OrderTrackingStatus $status, array $metadata): void
    {
        $updates = [];

        if ($dropshipStatus = $status->dropshipStatus()) {
            $updates['status'] = $dropshipStatus;
        }

        if ($status === OrderTrackingStatus::Shipped || $status === OrderTrackingStatus::InTransit) {
            $updates['shipped_at'] = $dropshipOrder->shipped_at ?? now();
        }

        if ($status === OrderTrackingStatus::Delivered) {
            $updates['delivered_at'] = $dropshipOrder->delivered_at ?? now();
        }

        foreach (['tracking_number', 'carrier', 'shipping_label'] as $field) {
            if (! empty($metadata[$field])) {
                $updates[$field] = $metadata[$field];
            }
        }

        if ($updates) {
            $dropshipOrder->forceFill($updates)->save();
        }
    }

    private function notifyImportantUpdate(OrderTrackingEvent $event): void
    {
        $event->loadMissing('order.user', 'dropshipOrder');
        $user = $event->order->user;

        if (! $user) {
            return;
        }

        if ($event->status === OrderTrackingStatus::Shipped) {
            Notification::send($user, new OrderShippedNotification($event->dropshipOrder, [
                'tracking_url' => route('track.index'),
                'tracking_number' => $event->metadata['tracking_number'] ?? $event->dropshipOrder?->tracking_number,
                'carrier' => $event->metadata['carrier'] ?? $event->dropshipOrder?->carrier,
            ]));

            return;
        }

        if (in_array($event->status, [OrderTrackingStatus::Delivered, OrderTrackingStatus::Failed], true)) {
            Log::info('Important order tracking update recorded.', [
                'order_id' => $event->order_id,
                'tracking_event_id' => $event->id,
                'status' => $event->status->value,
            ]);
        }
    }
}
