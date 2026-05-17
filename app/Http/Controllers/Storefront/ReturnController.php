<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\ReturnRequestForm;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Services\ReturnRefundService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReturnController extends Controller
{
    public function __construct(private readonly ReturnRefundService $returnRefundService)
    {
    }

    public function index(Request $request): Response
    {
        $returns = ReturnRequest::query()
            ->where('user_id', $request->user()->id)
            ->with('order:id,user_id,order_number,status,payment_status,payment_method,total_usd,created_at')
            ->latest()
            ->paginate(15)
            ->through(fn (ReturnRequest $returnRequest) => $this->returnRefundService->payload($returnRequest));

        return Inertia::render('Account/Returns', [
            'mode' => 'index',
            'returns' => $returns,
        ]);
    }

    public function create(Request $request, Order $order): Response
    {
        $this->authorize('create', [ReturnRequest::class, $order]);

        $order->load([
            'items:id,order_id,product_id,product_variant_id,vendor_id,quantity,price_usd,subtotal_usd',
            'items.product:id,name,slug,selling_price',
            'items.productVariant:id,product_id,combination,price,stock,image',
        ]);

        return Inertia::render('Account/Returns', [
            'mode' => 'create',
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'total_usd' => $order->total_usd,
                'items' => $order->items,
            ],
            'refundMethods' => ReturnRefundService::REFUND_METHODS,
        ]);
    }

    public function store(ReturnRequestForm $request)
    {
        $order = Order::query()->findOrFail($request->validated('order_id'));
        $returnRequest = $this->returnRefundService->create($request->user(), $order, $request->validated());

        if ($request->expectsJson()) {
            return $this->ok(['return' => $this->returnRefundService->payload($returnRequest)], 201);
        }

        return redirect()
            ->route('returns.show', $returnRequest)
            ->with('status', 'Return request submitted.');
    }

    public function show(ReturnRequest $return): Response
    {
        $this->authorize('view', $return);

        return Inertia::render('Account/Returns', [
            'mode' => 'show',
            'returnRequest' => $this->returnRefundService->payload($return),
        ]);
    }

    public function cancel(ReturnRequest $return): RedirectResponse
    {
        $this->authorize('cancel', $return);

        $this->returnRefundService->cancel($return);

        return back()->with('status', 'Return request cancelled.');
    }
}
