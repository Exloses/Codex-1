<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_add_to_wishlist(): void
    {
        $product = $this->createWishlistProduct();

        $this->post(route('wishlist.store'), ['product_id' => $product->id])
            ->assertRedirect(route('login'));

        $this->post(route('wishlist.toggle', $product))
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('wishlists', 0);
    }

    public function test_authenticated_user_can_add_product_to_wishlist(): void
    {
        $buyer = User::factory()->create();
        $product = $this->createWishlistProduct();

        $this->actingAs($buyer)
            ->postJson(route('wishlist.store'), ['product_id' => $product->id])
            ->assertCreated()
            ->assertJsonPath('wishlisted', true);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $buyer->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_authenticated_user_can_toggle_product_in_wishlist(): void
    {
        $buyer = User::factory()->create();
        $product = $this->createWishlistProduct();

        $this->actingAs($buyer)
            ->postJson(route('wishlist.toggle', $product))
            ->assertCreated()
            ->assertJsonPath('wishlisted', true);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $buyer->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($buyer)
            ->postJson(route('wishlist.toggle', $product))
            ->assertOk()
            ->assertJsonPath('wishlisted', false);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $buyer->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_adding_same_product_twice_does_not_create_duplicates(): void
    {
        $buyer = User::factory()->create();
        $product = $this->createWishlistProduct();

        $this->actingAs($buyer)->postJson(route('wishlist.store'), ['product_id' => $product->id])->assertCreated();
        $this->actingAs($buyer)->postJson(route('wishlist.store'), ['product_id' => $product->id])->assertCreated();

        $this->assertSame(1, Wishlist::query()
            ->where('user_id', $buyer->id)
            ->where('product_id', $product->id)
            ->count());
    }

    public function test_authenticated_user_can_remove_wishlist_item(): void
    {
        $buyer = User::factory()->create();
        $product = $this->createWishlistProduct();
        $wishlist = Wishlist::query()->create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($buyer)
            ->deleteJson(route('wishlist.destroy', $wishlist))
            ->assertOk()
            ->assertJsonPath('wishlisted', false);

        $this->assertDatabaseMissing('wishlists', ['id' => $wishlist->id]);
    }

    public function test_user_cannot_remove_another_users_wishlist_item(): void
    {
        $owner = User::factory()->create();
        $otherBuyer = User::factory()->create();
        $product = $this->createWishlistProduct();
        $wishlist = Wishlist::query()->create([
            'user_id' => $owner->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($otherBuyer)
            ->deleteJson(route('wishlist.destroy', $wishlist))
            ->assertNotFound();

        $this->assertDatabaseHas('wishlists', ['id' => $wishlist->id]);
    }

    public function test_user_can_move_wishlist_item_to_cart(): void
    {
        $buyer = User::factory()->create();
        $product = $this->createWishlistProduct();
        $wishlist = Wishlist::query()->create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($buyer)
            ->postJson(route('wishlist.move-to-cart', $wishlist))
            ->assertCreated()
            ->assertJsonPath('wishlisted', false);

        $this->assertDatabaseMissing('wishlists', ['id' => $wishlist->id]);
        $this->assertSame(1, CartItem::query()
            ->where('user_id', $buyer->id)
            ->where('product_id', $product->id)
            ->count());
    }

    public function test_wishlist_index_only_shows_authenticated_users_active_products(): void
    {
        $buyer = User::factory()->create();
        $otherBuyer = User::factory()->create();
        $activeProduct = $this->createWishlistProduct(['name' => 'Visible Wish', 'slug' => 'visible-wish']);
        $inactiveProduct = $this->createWishlistProduct(['name' => 'Hidden Wish', 'slug' => 'hidden-wish', 'is_active' => false]);
        $otherProduct = $this->createWishlistProduct(['name' => 'Other Buyer Wish', 'slug' => 'other-buyer-wish']);

        Wishlist::query()->create(['user_id' => $buyer->id, 'product_id' => $activeProduct->id]);
        Wishlist::query()->create(['user_id' => $buyer->id, 'product_id' => $inactiveProduct->id]);
        Wishlist::query()->create(['user_id' => $otherBuyer->id, 'product_id' => $otherProduct->id]);

        $this->actingAs($buyer)
            ->get(route('account.wishlist'))
            ->assertOk()
            ->assertSee('visible-wish', false)
            ->assertDontSee('hidden-wish', false)
            ->assertDontSee('other-buyer-wish', false);
    }

    public function test_inactive_products_cannot_be_added_to_wishlist(): void
    {
        $buyer = User::factory()->create();
        $inactiveProduct = $this->createWishlistProduct(['is_active' => false]);

        $this->actingAs($buyer)
            ->postJson(route('wishlist.store'), ['product_id' => $inactiveProduct->id])
            ->assertUnprocessable();

        $this->actingAs($buyer)
            ->postJson(route('wishlist.toggle', $inactiveProduct))
            ->assertNotFound();

        $this->assertDatabaseCount('wishlists', 0);
    }

    public function test_storefront_product_payloads_do_not_expose_vendor_price(): void
    {
        $product = $this->createWishlistProduct([
            'name' => 'Private Cost Product',
            'slug' => 'private-cost-product',
            'vendor_price' => 12.34,
        ]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertDontSee('vendor_price', false);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertDontSee('vendor_price', false);
    }

    private function createWishlistProduct(array $overrides = []): Product
    {
        $vendorUser = User::factory()->create();
        $vendor = Vendor::query()->create([
            'user_id' => $vendorUser->id,
            'store_name' => 'Wishlist Vendor '.uniqid(),
            'slug' => 'wishlist-vendor-'.uniqid(),
            'is_approved' => true,
            'commission_rate' => 10,
            'balance_idr' => 0,
        ]);
        $category = Category::query()->create([
            'name' => 'Wishlist Category '.uniqid(),
            'slug' => 'wishlist-category-'.uniqid(),
            'is_active' => true,
            'sort_order' => 1,
        ]);

        return Product::query()->create(array_merge([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Wishlist Product '.uniqid(),
            'slug' => 'wishlist-product-'.uniqid(),
            'description' => 'A product used for wishlist tests.',
            'vendor_price' => 25,
            'selling_price' => 49,
            'compare_price' => 69,
            'stock' => 10,
            'weight' => 1,
            'sku' => 'SKU-WISH-'.uniqid(),
            'is_active' => true,
            'is_featured' => true,
            'total_sales' => 0,
            'average_rating' => 0,
            'videos' => [],
        ], $overrides));
    }
}
