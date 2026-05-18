<?php

namespace App\Services;

use App\Enums\ReturnRequestStatus;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Notifications\ReturnRequestUpdateNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ReturnRefundService
{
    public const ELIGIBLE_ORDER_STATUSES = ['shipped', 'in_transit', 'out_for_delivery', 'delivered', 'returned'];
    public const BLOCKED_ORDER_STATUSES = ['pending', 'cancelled', 'failed'];
    public const REFUND_METHODS = ['original_payment', 'store_credit', 'manual'];

    public function __construct(
        private readonly StripeService $stripeService,
        private readonly PayPalService $payPalService,
    ) {}

    public function create(User $user, Order $order, array $data): ReturnRequest
    {
        $this->ensureEligible($user, $order);

        return DB::transaction(function () use ($user, $order, $data) {
            $returnRequest = ReturnRequest::query()->create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'return_number' => $this->generateReturnNumber(),
                'reason' => $data['reason'],
                'description' => $data['description'],
                'images' => $this->storeImages($data['images'] ?? []),
                'status' => ReturnRequestStatus::Pending,
                'refund_method' => $data['refund_method'] ?? 'original_payment',
                'refund_amount_usd' => null,
            ]);

            $this->notify($returnRequest, 'Return request submitted', 'pending');

            return $returnRequest->fresh(['order', 'user']);
        });
    }

    public function markUnderReview(ReturnRequest $returnRequest, ?string $adminNotes = null): ReturnRequest
    {
        return $this->transition($returnRequest, ReturnRequestStatus::UnderReview, $adminNotes);
    }

    public function approve(ReturnRequest $returnRequest, ?float $refundAmountUsd = null, ?string $adminNotes = null): ReturnRequest
    {
        return DB::transaction(function () use ($returnRequest, $refundAmountUsd, $adminNotes) {
            $returnRequest->loadMissing('order');
            $amount = $refundAmountUsd ?? (float) $returnRequest->order->total_usd;

            $returnRequest->forceFill([
                'status' => ReturnRequestStatus::Approved,
                'refund_amount_usd' => min($amount, (float) $returnRequest->order->total_usd),
                'admin_notes' => $adminNotes ?? $returnRequest->admin_notes,
                'refund_error' => null,
            ])->save();

            $this->notify($returnRequest, 'Return request approved', 'approved');

            return $returnRequest->fresh(['order', 'user']);
        });
    }

    public function reject(ReturnRequest $returnRequest, string $adminNotes): ReturnRequest
    {
        return $this->transition($returnRequest, ReturnRequestStatus::Rejected, $adminNotes, true);
    }

    public function cancel(ReturnRequest $returnRequest): ReturnRequest
    {
        if ($returnRequest->statusValue() === ReturnRequestStatus::Refunded->value) {
            throw new \InvalidArgumentException('Refunded return requests cannot be cancelled.');
        }

        return $this->transition($returnRequest, ReturnRequestStatus::Cancelled, 'Cancelled by customer.', true);
    }

    public function processRefund(ReturnRequest $returnRequest, ?string $adminNotes = null): ReturnRequest
    {
        return DB::transaction(function () use ($returnRequest, $adminNotes) {
            $returnRequest->loadMissing('order');
            $order = $returnRequest->order;

            if (! in_array($returnRequest->statusValue(), [ReturnRequestStatus::Approved->value, ReturnRequestStatus::RefundPending->value], true)) {
                throw new \InvalidArgumentException('Only approved returns can be refunded.');
            }

            $amount = (float) ($returnRequest->refund_amount_usd ?: $order->total_usd);
            $refund = match ($order->payment_method) {
                'stripe' => $this->stripeService->refund($order, $amount),
                'paypal' => $this->payPalService->refund($order, $amount),
                default => [
                    'status' => 'pending',
                    'reference' => 'MANUAL-'.Str::upper(Str::random(10)),
                    'message' => 'Manual refund required because the order payment method is unavailable or unsupported.',
                ],
            };

            $status = ($refund['status'] ?? null) === 'succeeded'
                ? ReturnRequestStatus::Refunded
                : ReturnRequestStatus::RefundPending;

            $returnRequest->forceFill([
                'status' => $status,
                'refund_amount_usd' => min($amount, (float) $order->total_usd),
                'refund_reference' => $refund['reference'] ?? null,
                'refund_processed_at' => $status === ReturnRequestStatus::Refunded ? now() : null,
                'refund_error' => $refund['message'] ?? null,
                'admin_notes' => $adminNotes ?? $returnRequest->admin_notes,
                'resolved_at' => $status === ReturnRequestStatus::Refunded ? now() : $returnRequest->resolved_at,
            ])->save();

            if ($status === ReturnRequestStatus::Refunded) {
                $order->forceFill(['status' => 'returned'])->save();
            }

            $this->notify($returnRequest, 'Return refund update', $status->value);

            return $returnRequest->fresh(['order', 'user']);
        });
    }

    public function canCreateForOrder(User $user, Order $order): bool
    {
        if ((int) $order->user_id !== (int) $user->id) {
            return false;
        }

        if ($order->payment_status !== 'paid') {
            return false;
        }

        if (! in_array($order->status, self::ELIGIBLE_ORDER_STATUSES, true)) {
            return false;
        }

        return ! $order->returnRequests()
            ->whereIn('status', ReturnRequestStatus::activeValues())
            ->exists();
    }

    public function ensureEligible(User $user, Order $order): void
    {
        if (! $this->canCreateForOrder($user, $order)) {
            throw new \InvalidArgumentException('This order is not eligible for a return request.');
        }
    }

    public function payload(ReturnRequest $returnRequest): array
    {
        $returnRequest->loadMissing('order:id,user_id,order_number,status,payment_status,payment_method,total_usd,created_at');

        return [
            'id' => $returnRequest->id,
            'return_number' => $returnRequest->return_number,
            'reason' => $returnRequest->reason,
            'description' => $returnRequest->description,
            'images' => $returnRequest->images ?? [],
            'status' => $returnRequest->statusValue(),
            'status_label' => ReturnRequestStatus::fromValue($returnRequest->statusValue())->label(),
            'refund_method' => $returnRequest->refund_method,
            'refund_amount_usd' => $returnRequest->refund_amount_usd,
            'refund_reference' => $returnRequest->refund_reference,
            'refund_processed_at' => $returnRequest->refund_processed_at?->toISOString(),
            'refund_error' => $returnRequest->refund_error,
            'admin_notes' => $returnRequest->admin_notes,
            'resolved_at' => $returnRequest->resolved_at?->toISOString(),
            'created_at' => $returnRequest->created_at?->toISOString(),
            'order' => [
                'id' => $returnRequest->order->id,
                'order_number' => $returnRequest->order->order_number,
                'status' => $returnRequest->order->status,
                'payment_status' => $returnRequest->order->payment_status,
                'payment_method' => $returnRequest->order->payment_method,
                'total_usd' => $returnRequest->order->total_usd,
            ],
        ];
    }

    private function transition(ReturnRequest $returnRequest, ReturnRequestStatus $status, ?string $adminNotes = null, bool $resolve = false): ReturnRequest
    {
        return DB::transaction(function () use ($returnRequest, $status, $adminNotes, $resolve) {
            $returnRequest->forceFill([
                'status' => $status,
                'admin_notes' => $adminNotes ?? $returnRequest->admin_notes,
                'resolved_at' => $resolve ? now() : $returnRequest->resolved_at,
            ])->save();

            $this->notify($returnRequest, 'Return request updated', $status->value);

            return $returnRequest->fresh(['order', 'user']);
        });
    }

    private function storeImages(array $images): array
    {
        return collect($images)
            ->filter(fn ($image) => $image instanceof UploadedFile)
            ->map(fn (UploadedFile $image) => $image->store('returns', 'public'))
            ->values()
            ->all();
    }

    private function notify(ReturnRequest $returnRequest, string $title, string $status): void
    {
        $returnRequest->loadMissing('user');

        if (! $returnRequest->user) {
            return;
        }

        Notification::send($returnRequest->user, new ReturnRequestUpdateNotification($returnRequest, [
            'title' => $title,
            'new_status' => $status,
            'return_url' => route('returns.show', $returnRequest),
            'admin_notes' => $returnRequest->admin_notes ?: 'No additional notes from support.',
        ]));
    }

    private function generateReturnNumber(): string
    {
        do {
            $number = 'RET-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
        } while (ReturnRequest::query()->where('return_number', $number)->exists());

        return $number;
    }
}
