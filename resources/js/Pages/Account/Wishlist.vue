<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import ProductCard from '@/Components/ProductCard.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({ items: { type: Array, default: () => [] } });

const removeItem = (item) => {
    router.delete(route('wishlist.destroy', item.id), {
        preserveScroll: true,
        preserveState: false,
    });
};

const moveToCart = (item) => {
    router.post(route('wishlist.move-to-cart', item.id), {}, {
        preserveScroll: true,
        preserveState: false,
    });
};
</script>

<template>
    <StorefrontLayout>
        <Head title="Wishlist" />
        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-normal">Wishlist</h1>
                    <p class="mt-2 text-sm text-zinc-600">Save products for later and move them to your cart when you are ready.</p>
                </div>
                <Link :href="route('products.index')" class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold hover:bg-zinc-50">
                    Continue shopping
                </Link>
            </div>

            <div v-if="items.length" class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div v-for="item in items" :key="item.id" class="space-y-3">
                    <ProductCard :product="item.product" />
                    <div class="grid grid-cols-2 gap-2">
                        <button class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white hover:bg-zinc-800" @click="moveToCart(item)">
                            Move to cart
                        </button>
                        <button class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50" @click="removeItem(item)">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
            <EmptyState v-else class="mt-6" title="No wishlist items" message="Products you save will appear here." />
        </section>
    </StorefrontLayout>
</template>
