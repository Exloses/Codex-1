<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('store_name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_holder')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('balance_idr', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_id')->nullable();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('size_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('columns');
            $table->json('rows');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->string('name_id')->nullable();
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('description_id')->nullable();
            $table->decimal('vendor_price', 15, 2);
            $table->decimal('selling_price', 15, 2);
            $table->decimal('compare_price', 15, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('weight')->default(0);
            $table->string('sku')->unique()->nullable();
            $table->foreignId('size_guide_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('total_sales')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->json('videos')->nullable();
            $table->timestamps();
            $table->index(['is_active', 'is_featured']);
            $table->index(['vendor_id', 'is_active']);
            $table->index(['category_id', 'is_active']);
        });

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('products', function (Blueprint $table) {
                $table->index(['name', 'description']);
            });
        } else {
            Schema::table('products', function (Blueprint $table) {
                $table->fullText(['name', 'description']);
            });
        }

        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('product_attributes')->cascadeOnDelete();
            $table->string('value');
            $table->string('color_hex')->nullable();
            $table->integer('sort_order')->default(0);
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->json('combination');
            $table->string('sku')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('vendor_price', 15, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('phone');
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code');
            $table->string('country', 2);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number')->unique();
            $table->string('guest_email')->nullable();
            $table->string('guest_name')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('subtotal_usd', 15, 2);
            $table->decimal('shipping_cost_usd', 15, 2)->default(0);
            $table->decimal('discount_usd', 15, 2)->default(0);
            $table->decimal('total_usd', 15, 2);
            $table->string('buyer_currency', 3)->default('USD');
            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->decimal('total_buyer_currency', 15, 2);
            $table->string('payment_status')->default('unpaid');
            $table->string('payment_method')->nullable();
            $table->string('stripe_payment_id')->nullable();
            $table->string('paypal_order_id')->nullable();
            $table->string('affiliate_code')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['payment_status', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vendor_id')->constrained();
            $table->integer('quantity');
            $table->decimal('price_usd', 15, 2);
            $table->decimal('subtotal_usd', 15, 2);
            $table->text('custom_note')->nullable();
            $table->timestamps();
        });

        Schema::create('dropship_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained();
            $table->string('dropship_number')->unique();
            $table->string('status')->default('pending');
            $table->decimal('vendor_total_idr', 15, 2);
            $table->boolean('is_paid_to_vendor')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable();
            $table->string('shipping_label')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['vendor_id', 'status']);
        });

        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('countries');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('carrier');
            $table->integer('min_weight')->default(0);
            $table->integer('max_weight')->default(99999);
            $table->decimal('price_usd', 10, 2);
            $table->string('estimated_days');
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating');
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_id')->nullable();
            $table->string('image');
            $table->string('link')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type');
            $table->decimal('value', 10, 2);
            $table->decimal('min_order_usd', 10, 2)->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('balance')->default(0);
            $table->timestamps();
        });

        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->integer('points');
            $table->string('type');
            $table->string('description');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_email')->nullable();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('stock');
            $table->decimal('target_price_usd', 15, 2)->nullable();
            $table->boolean('is_notified')->default(false);
            $table->timestamps();
        });

        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('return_number')->unique();
            $table->string('reason');
            $table->text('description');
            $table->json('images')->nullable();
            $table->string('status')->default('pending');
            $table->string('refund_method')->nullable();
            $table->decimal('refund_amount_usd', 15, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('referral_code')->unique();
            $table->string('referral_link')->unique();
            $table->string('tier')->default('starter');
            $table->decimal('commission_rate', 5, 2)->default(5.00);
            $table->integer('total_clicks')->default(0);
            $table->integer('total_referrals')->default(0);
            $table->integer('total_sales')->default(0);
            $table->decimal('total_earned_usd', 15, 2)->default(0);
            $table->decimal('total_paid_usd', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('referer')->nullable();
            $table->boolean('is_unique')->default(true);
            $table->boolean('converted')->default(false);
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('clicked_at');
            $table->timestamps();
        });

        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained();
            $table->decimal('order_total_usd', 15, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_usd', 15, 2);
            $table->string('status')->default('pending');
            $table->timestamp('available_at');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('payout_id')->nullable();
            $table->timestamps();
        });

        Schema::create('affiliate_payout_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('paypal_email')->nullable();
            $table->string('wise_email')->nullable();
            $table->string('wise_currency')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_holder')->nullable();
            $table->string('bank_country')->nullable();
            $table->string('swift_code')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });

        Schema::create('affiliate_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained();
            $table->foreignId('payout_method_id')->constrained('affiliate_payout_methods');
            $table->decimal('amount_usd', 15, 2);
            $table->decimal('fee_usd', 15, 2)->default(0);
            $table->decimal('net_amount_usd', 15, 2);
            $table->string('status')->default('pending');
            $table->string('payout_type');
            $table->string('paypal_email')->nullable();
            $table->string('wise_email')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('transaction_ref')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_email')->nullable();
            $table->string('guest_name')->nullable();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ticket_number')->unique();
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('open');
            $table->string('priority')->default('normal');
            $table->timestamps();
        });

        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('message');
            $table->boolean('is_staff')->default(false);
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('active');
            $table->string('token')->unique();
            $table->timestamps();
        });

        Schema::create('product_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->text('question');
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });

        Schema::create('product_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('product_questions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->text('answer');
            $table->boolean('is_vendor')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('question');
            $table->text('answer');
            $table->string('language')->default('en');
            $table->integer('sort_order')->default(0);
            $table->integer('helpful_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('product_answers');
        Schema::dropIfExists('product_questions');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('affiliate_payouts');
        Schema::dropIfExists('affiliate_payout_methods');
        Schema::dropIfExists('affiliate_commissions');
        Schema::dropIfExists('affiliate_clicks');
        Schema::dropIfExists('affiliates');
        Schema::dropIfExists('return_requests');
        Schema::dropIfExists('stock_notifications');
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('loyalty_points');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('shipping_rates');
        Schema::dropIfExists('shipping_zones');
        Schema::dropIfExists('dropship_orders');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('products');
        Schema::dropIfExists('size_guides');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('vendors');
    }
};
