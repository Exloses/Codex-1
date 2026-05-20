<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAnswer;
use App\Models\ProductQuestion;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\ProductQuestionAskedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductQATest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_buyer_can_ask_question_and_vendor_is_notified(): void
    {
        Notification::fake();
        [$product, $vendorUser] = $this->createProduct();
        $buyer = User::factory()->create();

        $response = $this->actingAs($buyer)
            ->postJson(route('products.questions.store', $product), [
                'question' => 'Is this handmade and suitable for daily use?',
            ])
            ->assertCreated()
            ->assertJsonPath('question.question', 'Is this handmade and suitable for daily use?')
            ->assertJsonPath('question.asker_label', 'Customer');

        $payload = $response->getContent();

        $this->assertStringNotContainsString('user_id', $payload);
        $this->assertStringNotContainsString($buyer->email, $payload);
        $this->assertStringNotContainsString('vendor_price', $payload);
        $this->assertStringNotContainsString('vendor_total_idr', $payload);
        $this->assertStringNotContainsString('bank_account', $payload);

        $this->assertDatabaseHas('product_questions', [
            'product_id' => $product->id,
            'user_id' => $buyer->id,
            'question' => 'Is this handmade and suitable for daily use?',
            'is_public' => true,
        ]);

        Notification::assertSentTo($vendorUser, ProductQuestionAskedNotification::class);
    }

    public function test_guest_cannot_ask_question(): void
    {
        [$product] = $this->createProduct();

        $this->postJson(route('products.questions.store', $product), [
            'question' => 'Can I ask as a guest?',
        ])->assertUnauthorized();

        $this->assertDatabaseCount('product_questions', 0);
    }

    public function test_vendor_owner_can_answer_question(): void
    {
        [$product, $vendorUser] = $this->createProduct();
        $question = $this->createQuestion($product);

        $this->actingAs($vendorUser)
            ->postJson(route('questions.answers.store', $question), [
                'answer' => 'Yes, this item is made by our Bandung workshop.',
            ])
            ->assertCreated()
            ->assertJsonPath('answer.is_vendor', true)
            ->assertJsonPath('answer.is_verified', true)
            ->assertJsonPath('answer.author_label', 'Vendor');

        $this->assertDatabaseHas('product_answers', [
            'question_id' => $question->id,
            'user_id' => $vendorUser->id,
            'answer' => 'Yes, this item is made by our Bandung workshop.',
            'is_vendor' => true,
            'is_verified' => true,
        ]);
    }

    public function test_unrelated_vendor_and_buyer_cannot_answer_question(): void
    {
        [$product] = $this->createProduct();
        [, $otherVendorUser] = $this->createProduct(['slug' => 'other-qa-product']);
        $buyer = User::factory()->create();
        $question = $this->createQuestion($product);

        $this->actingAs($otherVendorUser)
            ->postJson(route('questions.answers.store', $question), [
                'answer' => 'I should not be able to answer.',
            ])
            ->assertForbidden();

        $this->actingAs($buyer)
            ->postJson(route('questions.answers.store', $question), [
                'answer' => 'I should not be able to answer either.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('product_answers', 0);
    }

    public function test_admin_can_answer_question_as_verified_platform_answer(): void
    {
        Role::findOrCreate('admin');

        [$product] = $this->createProduct();
        $question = $this->createQuestion($product);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->postJson(route('questions.answers.store', $question), [
                'answer' => 'GlobalDrop has verified this product information.',
            ])
            ->assertCreated()
            ->assertJsonPath('answer.is_vendor', false)
            ->assertJsonPath('answer.is_verified', true)
            ->assertJsonPath('answer.author_label', 'GlobalDrop team');

        $this->assertDatabaseHas('product_answers', [
            'question_id' => $question->id,
            'user_id' => $admin->id,
            'is_vendor' => false,
            'is_verified' => true,
        ]);
    }

    public function test_cannot_ask_or_answer_for_inactive_product(): void
    {
        [$product, $vendorUser] = $this->createProduct(['is_active' => false]);
        $buyer = User::factory()->create();
        $question = $this->createQuestion($product);

        $this->actingAs($buyer)
            ->postJson(route('products.questions.store', $product), [
                'question' => 'Can I ask about inactive products?',
            ])
            ->assertNotFound();

        $this->actingAs($vendorUser)
            ->postJson(route('questions.answers.store', $question), [
                'answer' => 'Inactive product answer.',
            ])
            ->assertNotFound();
    }

    public function test_product_detail_renders_only_public_questions_and_safe_qa_payload(): void
    {
        [$product] = $this->createProduct(['vendor_price' => 12.34]);
        ProductVariant::query()->create([
            'product_id' => $product->id,
            'combination' => ['Color' => 'Black'],
            'sku' => 'QA-PRIVATE-VARIANT',
            'price' => 40,
            'vendor_price' => 9.99,
            'stock' => 5,
        ]);
        $publicQuestion = $this->createQuestion($product, [
            'question' => 'Is this product public?',
            'is_public' => true,
        ]);
        $privateQuestion = $this->createQuestion($product, [
            'question' => 'Private moderation question',
            'is_public' => false,
        ]);
        ProductAnswer::query()->create([
            'question_id' => $publicQuestion->id,
            'user_id' => User::factory()->create()->id,
            'answer' => 'Yes, this answer is public.',
            'is_vendor' => true,
            'is_verified' => true,
        ]);
        ProductAnswer::query()->create([
            'question_id' => $privateQuestion->id,
            'user_id' => User::factory()->create(['email' => 'private-answer@example.com'])->id,
            'answer' => 'This private answer should stay hidden.',
            'is_vendor' => true,
            'is_verified' => true,
        ]);

        $response = $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('Is this product public?', false)
            ->assertSee('Yes, this answer is public.', false)
            ->assertDontSee('Private moderation question', false)
            ->assertDontSee('This private answer should stay hidden', false);

        $payload = $response->getContent();

        $this->assertStringNotContainsString('vendor_price', $payload);
        $this->assertStringNotContainsString('vendor_total_idr', $payload);
        $this->assertStringNotContainsString('user_id', $payload);
        $this->assertStringNotContainsString('private-answer@example.com', $payload);
        $this->assertStringNotContainsString('bank_account', $payload);
    }

    private function createQuestion(Product $product, array $overrides = []): ProductQuestion
    {
        return ProductQuestion::query()->create(array_merge([
            'product_id' => $product->id,
            'user_id' => User::factory()->create()->id,
            'question' => 'Does this product match the photos?',
            'is_public' => true,
        ], $overrides));
    }

    private function createProduct(array $overrides = []): array
    {
        $suffix = uniqid();
        $vendorUser = User::factory()->create([
            'name' => 'QA Vendor '.$suffix,
            'email' => 'qa-vendor-'.$suffix.'@example.com',
        ]);
        $vendor = Vendor::query()->create([
            'user_id' => $vendorUser->id,
            'store_name' => 'QA Vendor Store '.$suffix,
            'slug' => 'qa-vendor-'.$suffix,
            'bank_name' => 'Private Bank',
            'bank_account' => 'PRIVATE-'.$suffix,
            'bank_holder' => 'Private Holder',
            'is_approved' => true,
            'commission_rate' => 10,
            'balance_idr' => 0,
        ]);
        $category = Category::query()->create([
            'name' => 'QA Category '.$suffix,
            'slug' => 'qa-category-'.$suffix,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $product = Product::query()->create(array_merge([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'QA Product '.$suffix,
            'slug' => 'qa-product-'.$suffix,
            'description' => 'A product used to validate question and answer behavior.',
            'vendor_price' => 20,
            'selling_price' => 55,
            'compare_price' => 70,
            'stock' => 8,
            'weight' => 1,
            'sku' => 'QA-SKU-'.$suffix,
            'is_active' => true,
            'is_featured' => false,
            'total_sales' => 0,
            'average_rating' => 0,
            'videos' => [],
        ], $overrides));

        return [$product, $vendorUser, $vendor];
    }
}
