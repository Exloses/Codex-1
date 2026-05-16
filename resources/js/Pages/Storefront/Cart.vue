<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});

const notes = reactive({});
const coupon = reactive({ code: '', loyaltyPoints: 0 });

const money = (value) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(value || 0));
const unitPrice = (item) => Number(item.product_variant?.price || item.productVariant?.price || item.product?.selling_price || 0);
const variant = (item) => item.productVariant || item.product_variant || null;
const variantText = (item) => Object.entries(variant(item)?.combination || {}).map(([key, value]) => `${key}: ${value}`).join(', ');
const stockLimit = (item) => Number(variant(item)?.stock ?? item.product?.stock ?? 99);
const subtotal = computed(() => props.items.reduce((sum, item) => sum + unitPrice(item) * Number(item.quantity || 0), 0));
const shipping = computed(() => (subtotal.value > 0 ? 12 : 0));
const total = computed(() => subtotal.value + shipping.value);

const updateQuantity = (item, quantity) => {
    router.put(route('cart.update', item.id), { quantity, custom_note: notes[item.id] || '' }, { preserveScroll: true });
};
</script>

<template>
    <StorefrontLayout>
        <Head title="Cart" />

        <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Cart</h1>
            <p class="mt-2 text-sm text-zinc-600">Review quantities, notes, coupon, and loyalty redemption before checkout.</p>

            <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_360px]">
                <div class="space-y-4">
                    <article v-for="item in items" :key="item.id" class="rounded-lg border border-zinc-200 bg-white p-4">
                        <div class="flex gap-4">
                            <img
                                :src="item.productVariant?.image || item.product_variant?.image || 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=500&q=80'"
                                :alt="item.product?.name"
                                class="h-24 w-24 rounded-md object-cover"
                            />
                            <div class="min-w-0 flex-1">
                                <Link :href="route('products.show', item.product?.slug)" class="font-semibold">{{ item.product?.name }}</Link>
                                <p class="mt-1 text-sm text-zinc-500">{{ money(unitPrice(item)) }}</p>
                                <p v-if="variantText(item)" class="mt-1 text-sm text-zinc-600">{{ variantText(item) }}</p>
                                <p class="mt-1 text-xs text-zinc-500">{{ stockLimit(item) }} available</p>
                                <textarea v-model="notes[item.id]" class="mt-3 w-full rounded-md border-zinc-300 text-sm" rows="2" placeholder="Custom note for this item"></textarea>
                            </div>
                            <div class="w-28">
                                <input
                                    type="number"
                                    min="1"
                                    :max="Math.max(stockLimit(item), 1)"
                                    :value="item.quantity"
                                    class="w-full rounded-md border-zinc-300 text-sm"
                                    @change="updateQuantity(item, Number($event.target.value))"
                                />
                                <button class="mt-3 text-sm font-semibold text-rose-700" @click="router.delete(route('cart.destroy', item.id), { preserveScroll: true })">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </article>

                    <EmptyState v-if="!items.length" title="Your cart is empty" message="Add products from the catalog to start checkout." />
                </div>

                <aside class="self-start rounded-lg border border-zinc-200 bg-white p-5">
                    <h2 class="text-lg font-bold">Order summary</h2>
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between"><span>Subtotal</span><span>{{ money(subtotal) }}</span></div>
                        <div class="flex justify-between"><span>Shipping estimate</span><span>{{ money(shipping) }}</span></div>
                        <div class="border-t border-zinc-200 pt-3">
                            <div class="flex justify-between text-base font-bold"><span>Total</span><span>{{ money(total) }}</span></div>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        <input v-model="coupon.code" class="w-full rounded-md border-zinc-300 text-sm" placeholder="Coupon code" />
                        <button class="w-full rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold" @click="router.post(route('checkout.apply-coupon'), { code: coupon.code }, { preserveScroll: true })">
                            Apply coupon
                        </button>
                        <input v-model.number="coupon.loyaltyPoints" type="number" min="0" class="w-full rounded-md border-zinc-300 text-sm" placeholder="Loyalty points" />
                        <button class="w-full rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold" @click="router.post(route('checkout.redeem-points'), { points: coupon.loyaltyPoints }, { preserveScroll: true })">
                            Redeem points
                        </button>
                    </div>

                    <Link :href="route('checkout.index')" class="mt-5 block rounded-md bg-zinc-950 px-4 py-3 text-center text-sm font-semibold text-white" :class="{ 'pointer-events-none opacity-50': !items.length }">
                        Checkout
                    </Link>
                </aside>
            </div>
        </section>
    </StorefrontLayout>
</template>
