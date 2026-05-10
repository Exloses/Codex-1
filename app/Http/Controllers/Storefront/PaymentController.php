<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\PaymentRequest;
use App\Jobs\ProcessOrderAfterPayment;
use App\Models\Order;
use App\Services\PayPalService;
use App\Services\StripeService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function createStripeIntent(PaymentRequest $request, StripeService $stripeService)
    {
        $order = Order::query()->findOrFail($request->validated('order_id'));
        $this->authorize('view', $order);

        return $this->ok(['payment_intent' => $stripeService->createPaymentIntent($order)]);
    }

    public function createPayPalOrder(PaymentRequest $request, PayPalService $payPalService)
    {
        $order = Order::query()->findOrFail($request->validated('order_id'));
        $this->authorize('view', $order);

        return $this->ok(['paypal_order' => $payPalService->createOrder($order)]);
    }

    public function capturePayPalOrder(PaymentRequest $request, PayPalService $payPalService)
    {
        $response = $payPalService->captureOrder($request->validated('paypal_order_id'));

        return $this->ok(['capture' => $response]);
    }

    public function stripeWebhook(Request $request, StripeService $stripeService)
    {
        $result = $stripeService->handleWebhook($request->getContent(), $request->header('Stripe-Signature'));

        if (($result['status'] ?? null) === 'paid' && isset($result['order_id'])) {
            $order = Order::query()->find($result['order_id']);
            if ($order) {
                ProcessOrderAfterPayment::dispatch($order);
            }
        }

        return $this->ok(['webhook' => $result]);
    }
}
