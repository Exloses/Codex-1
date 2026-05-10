<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\AddressRequest;
use App\Models\Address;
use App\Models\Order;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Account/Index', ['user' => auth()->user()->load('vendor', 'loyaltyPoint')]);
    }

    public function updateProfile()
    {
        auth()->user()->update(request()->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'size:2'],
            'currency' => ['nullable', 'string', 'size:3'],
            'language' => ['nullable', 'string', 'max:5'],
        ]));

        return back();
    }

    public function orders(): Response
    {
        return Inertia::render('Account/Orders', [
            'orders' => auth()->user()->orders()->latest()->paginate(15),
        ]);
    }

    public function orderDetail(Order $order): Response
    {
        $this->authorize('view', $order);

        return Inertia::render('Account/OrderDetail', [
            'order' => $order->load([
                'items.product:id,name,slug,selling_price,compare_price,stock,average_rating',
                'dropshipOrders',
            ]),
        ]);
    }

    public function addresses(): Response
    {
        return Inertia::render('Account/Addresses', ['addresses' => auth()->user()->addresses()->latest()->get()]);
    }

    public function storeAddress(AddressRequest $request)
    {
        $this->syncDefaultAddress($request);
        $address = $request->user()->addresses()->create($request->validated());

        return $this->ok(['address' => $address], 201);
    }

    public function updateAddress(AddressRequest $request, Address $address)
    {
        $this->authorize('update', $address);
        $this->syncDefaultAddress($request, $address);
        $address->update($request->validated());

        return $this->ok(['address' => $address]);
    }

    public function destroyAddress(Address $address)
    {
        $this->authorize('delete', $address);
        $address->delete();

        return $this->ok();
    }

    private function syncDefaultAddress(AddressRequest $request, ?Address $address = null): void
    {
        if ($request->boolean('is_default')) {
            $request->user()->addresses()
                ->when($address, fn ($query) => $query->whereKeyNot($address->id))
                ->update(['is_default' => false]);
        }
    }
}
