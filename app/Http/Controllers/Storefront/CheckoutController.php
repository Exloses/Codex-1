<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\CheckoutRequest;
use App\Http\Requests\Storefront\GuestCheckoutRequest;
use App\Jobs\ProcessOrderAfterPayment;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Storefront/Checkout', [
            'cartItems' => CartItem::query()
                ->select(['id', 'user_id', 'product_id', 'product_variant_id', 'quantity', 'updated_at'])
                ->with(['product:id,name,slug,selling_price,stock', 'productVariant:id,product_id,combination,price,stock'])
                ->where('user_id', auth()->id())
                ->get(),
            'addresses' => auth()->user()?->addresses()
                ->select(['id', 'user_id', 'full_name', 'phone', 'address_line1', 'address_line2', 'city', 'state', 'postal_code', 'country', 'is_default'])
                ->latest()
                ->get() ?? [],
        ]);
    }

    public function store(CheckoutRequest $request)
    {
        $order = $this->createOrderFromCart($request);

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order): Response
    {
        $this->authorize('view', $order);

        return Inertia::render('Storefront/CheckoutSuccess', [
            'order' => $order->load([
                'items:id,order_id,product_id,product_variant_id,vendor_id,quantity,price_usd,subtotal_usd',
                'items.product:id,name,slug,selling_price,compare_price,stock,average_rating',
            ]),
        ]);
    }

    public function applyCoupon()
    {
        return $this->ok(['discount_usd' => 0, 'message' => 'Coupon validation will be expanded in Task 8 follow-ups.']);
    }

    public function redeemPoints()
    {
        return $this->ok(['discount_usd' => 0, 'message' => 'Point redemption is available through LoyaltyService.']);
    }

    public function guestStore(GuestCheckoutRequest $request)
    {
        return response()->json([
            'message' => 'Guest checkout persistence requires nullable order user_id from Task 17. Authenticated checkout is ready.',
        ], 422);
    }

    private function createOrderFromCart(CheckoutRequest $request): Order
    {
        $user = $request->user();
        $cartItems = CartItem::query()
            ->with([
                'product:id,vendor_id,name,slug,selling_price,stock',
                'productVariant:id,product_id,price,stock',
            ])
            ->where('user_id', $user->id)
            ->get();
        abort_if($cartItems->isEmpty(), 422, 'Cart is empty.');

        return DB::transaction(function () use ($request, $user, $cartItems) {
            $subtotal = $cartItems->sum(fn ($item) => (float) ($item->productVariant?->price ?? $item->product->selling_price) * $item->quantity);
            $shipping = (float) $request->input('shipping_cost_usd', 0);
            $discount = (float) $request->input('discount_usd', 0);
            $total = max(0, $subtotal + $shipping - $discount);

            $order = Order::query()->create([
                'user_id' => $user->id,
                'address_id' => $request->input('address_id'),
                'order_number' => 'ORD-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'status' => 'pending',
                'subtotal_usd' => $subtotal,
                'shipping_cost_usd' => $shipping,
                'discount_usd' => $discount,
                'total_usd' => $total,
                'buyer_currency' => strtoupper($request->input('buyer_currency', $user->currency ?? 'USD')),
                'exchange_rate' => 1,
                'total_buyer_currency' => $total,
                'payment_status' => 'unpaid',
                'payment_method' => $request->input('payment_method'),
                'affiliate_code' => $request->input('affiliate_code'),
                'notes' => $request->input('notes'),
            ]);

            foreach ($cartItems as $item) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'vendor_id' => $item->product->vendor_id,
                    'quantity' => $item->quantity,
                    'price_usd' => $item->productVariant?->price ?? $item->product->selling_price,
                    'subtotal_usd' => (float) ($item->productVariant?->price ?? $item->product->selling_price) * $item->quantity,
                ]);
            }

            if ($order->payment_status === 'paid') {
                ProcessOrderAfterPayment::dispatch($order);
            }

            return $order;
        });
    }
}
