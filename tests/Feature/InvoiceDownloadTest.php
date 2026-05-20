<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\DropshipOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InvoiceDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_owner_can_download_invoice_pdf(): void
    {
        [$buyer, $order] = $this->createInvoiceOrder();

        $response = $this->actingAs($buyer)->get(route('account.orders.invoice', $order));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString(
            "invoice-{$order->order_number}.pdf",
            (string) $response->headers->get('content-disposition')
        );
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_other_buyer_cannot_download_invoice(): void
    {
        [, $order] = $this->createInvoiceOrder();
        $otherBuyer = User::factory()->create();

        $this->actingAs($otherBuyer)
            ->get(route('account.orders.invoice', $order))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_for_invoice_route(): void
    {
        [, $order] = $this->createInvoiceOrder();

        $this->get(route('account.orders.invoice', $order))
            ->assertRedirect(route('login'));
    }

    public function test_guest_order_invoice_is_not_available_through_account_route_for_buyers(): void
    {
        [, $order] = $this->createInvoiceOrder(null, [
            'user_id' => null,
            'address_id' => null,
            'guest_name' => 'Guest Buyer',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '555-0199',
            'guest_address_line1' => '456 Guest Lane',
            'guest_city' => 'Melbourne',
            'guest_postal_code' => '3000',
            'guest_country' => 'AU',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('account.orders.invoice', $order))
            ->assertForbidden();
    }

    public function test_admin_can_download_invoice_when_policy_allows_admin_access(): void
    {
        [, $order] = $this->createInvoiceOrder();
        $admin = User::factory()->create();
        Role::findOrCreate('admin');
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('account.orders.invoice', $order))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_invoice_view_excludes_internal_vendor_financial_fields(): void
    {
        [, $order] = $this->createInvoiceOrder();
        $invoiceOrder = $this->loadInvoiceOrder($order);

        $html = View::make('invoices.order', [
            'order' => $invoiceOrder,
            'appName' => 'GlobalDropship',
            'supportEmail' => 'support@example.test',
        ])->render();

        $this->assertStringContainsString('INVOICE', $html);
        $this->assertStringContainsString($order->order_number, $html);
        $this->assertStringContainsString('Traveler Tote', $html);
        $this->assertStringContainsString('Color: Black, Size: M', $html);
        $this->assertStringNotContainsString('vendor_price', $html);
        $this->assertStringNotContainsString('vendor_total_idr', $html);
        $this->assertStringNotContainsString('supplier payout', strtolower($html));
        $this->assertStringNotContainsString('balance_idr', $html);
        $this->assertStringNotContainsString('admin_notes', $html);
    }

    public function test_invoice_view_handles_missing_variant_gracefully(): void
    {
        [$buyer, $order] = $this->createInvoiceOrder();
        $order->items()->update(['product_variant_id' => null]);

        $invoiceOrder = $this->loadInvoiceOrder($order->fresh());

        $html = View::make('invoices.order', [
            'order' => $invoiceOrder,
            'appName' => 'GlobalDropship',
            'supportEmail' => 'support@example.test',
        ])->render();

        $this->assertStringContainsString('Traveler Tote', $html);
        $this->assertStringContainsString($buyer->email, $html);
        $this->assertStringNotContainsString('Color: Black', $html);
    }

    private function createInvoiceOrder(?User $buyer = null, array $orderOverrides = []): array
    {
        $buyer ??= User::factory()->create([
            'name' => 'Sarah Buyer',
            'email' => 'sarah@example.test',
        ]);

        $vendorUser = User::factory()->create();
        $vendor = Vendor::query()->create([
            'user_id' => $vendorUser->id,
            'store_name' => 'Invoice Vendor',
            'slug' => 'invoice-vendor-'.uniqid(),
            'is_approved' => true,
            'commission_rate' => 10,
            'balance_idr' => 500000,
        ]);
        $category = Category::query()->create([
            'name' => 'Bags',
            'slug' => 'bags-'.uniqid(),
            'is_active' => true,
        ]);
        $product = Product::query()->create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Traveler Tote',
            'slug' => 'traveler-tote-'.uniqid(),
            'description' => 'A buyer-facing product description.',
            'vendor_price' => 12.34,
            'selling_price' => 49,
            'stock' => 10,
            'weight' => 500,
            'sku' => 'TOTE-'.uniqid(),
            'is_active' => true,
        ]);
        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'combination' => ['Color' => 'Black', 'Size' => 'M'],
            'sku' => 'TOTE-BLK-M-'.uniqid(),
            'price' => 52,
            'vendor_price' => 10.01,
            'stock' => 5,
        ]);
        $address = $buyer
            ? Address::query()->create([
                'user_id' => $buyer->id,
                'full_name' => 'Sarah Buyer',
                'phone' => '555-0100',
                'address_line1' => '123 Market Street',
                'address_line2' => 'Apt 5',
                'city' => 'Melbourne',
                'state' => 'VIC',
                'postal_code' => '3000',
                'country' => 'AU',
            ])
            : null;

        $order = Order::query()->create(array_merge([
            'user_id' => $buyer?->id,
            'address_id' => $address?->id,
            'order_number' => 'ORD-INV-'.uniqid(),
            'guest_email' => null,
            'guest_name' => null,
            'status' => 'processing',
            'subtotal_usd' => 104,
            'shipping_cost_usd' => 12,
            'discount_usd' => 4,
            'total_usd' => 112,
            'buyer_currency' => 'USD',
            'exchange_rate' => 1,
            'total_buyer_currency' => 112,
            'payment_status' => 'paid',
            'payment_method' => 'stripe',
            'notes' => 'Internal note that should not render.',
        ], $orderOverrides));

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'vendor_id' => $vendor->id,
            'quantity' => 2,
            'price_usd' => 52,
            'subtotal_usd' => 104,
            'custom_note' => 'Gift wrap',
        ]);

        DropshipOrder::query()->create([
            'order_id' => $order->id,
            'vendor_id' => $vendor->id,
            'dropship_number' => 'DS-INV-'.uniqid(),
            'status' => 'pending',
            'vendor_total_idr' => 999999,
            'is_paid_to_vendor' => false,
            'notes' => 'supplier payout should stay internal',
        ]);

        return [$buyer, $order];
    }

    private function loadInvoiceOrder(Order $order): Order
    {
        return Order::query()
            ->select([
                'id',
                'user_id',
                'address_id',
                'order_number',
                'guest_email',
                'guest_name',
                'guest_phone',
                'guest_address_line1',
                'guest_address_line2',
                'guest_city',
                'guest_state',
                'guest_postal_code',
                'guest_country',
                'status',
                'subtotal_usd',
                'shipping_cost_usd',
                'discount_usd',
                'total_usd',
                'buyer_currency',
                'payment_status',
                'payment_method',
                'created_at',
            ])
            ->with([
                'user:id,name,email',
                'address:id,full_name,phone,address_line1,address_line2,city,state,postal_code,country',
                'items:id,order_id,product_id,product_variant_id,quantity,price_usd,subtotal_usd',
                'items.product:id,name,slug',
                'items.productVariant:id,product_id,combination',
            ])
            ->findOrFail($order->id);
    }
}
