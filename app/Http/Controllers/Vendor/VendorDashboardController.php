<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\VendorApplicationRequest;
use App\Models\DropshipOrder;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class VendorDashboardController extends Controller
{
    public function index(): Response
    {
        $vendor = auth()->user()->vendor;
        abort_if(! $vendor, 403, 'Vendor profile not found.');
        $this->authorize('manage', $vendor);

        return Inertia::render('Vendor/Dashboard', [
            'vendor' => $vendor,
            'stats' => [
                'products' => Product::query()->where('vendor_id', $vendor->id)->count(),
                'active_products' => Product::query()->where('vendor_id', $vendor->id)->where('is_active', true)->count(),
                'pending_orders' => DropshipOrder::query()->where('vendor_id', $vendor->id)->where('status', 'pending')->count(),
                'shipped_orders' => DropshipOrder::query()->where('vendor_id', $vendor->id)->where('status', 'shipped')->count(),
                'balance_idr' => $vendor->balance_idr,
            ],
            'recentOrders' => $vendor->dropshipOrders()
                ->select(['id', 'order_id', 'vendor_id', 'dropship_number', 'status', 'vendor_total_idr', 'created_at'])
                ->with('order:id,order_number,total_usd,status')
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    public function registerVendor(VendorApplicationRequest $request)
    {
        abort_if($request->user()->vendor()->exists(), 422, 'Vendor application already exists.');

        $vendor = Vendor::query()->create([
            'user_id' => $request->user()->id,
            'store_name' => $request->validated('store_name'),
            'slug' => $this->uniqueVendorSlug($request->validated('store_name')),
            'description' => $request->validated('description'),
            'province' => $request->validated('province'),
            'city' => $request->validated('city'),
            'is_approved' => false,
            'commission_rate' => 0,
            'balance_idr' => 0,
        ]);

        Role::findOrCreate('vendor');
        $request->user()->assignRole('vendor');

        return $this->ok(['vendor' => $vendor], 201);
    }

    private function uniqueVendorSlug(string $storeName): string
    {
        $base = Str::slug($storeName);
        $slug = $base;
        $counter = 2;

        while (Vendor::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
