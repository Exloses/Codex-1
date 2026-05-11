<script setup>
import { HeartIcon, ShoppingBagIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';

defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const money = (value) =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(Number(value || 0));
</script>

<template>
    <article class="group overflow-hidden rounded-lg border border-zinc-200 bg-white">
        <Link :href="route('products.show', product.slug)" class="block aspect-[4/3] bg-zinc-100">
            <img
                :src="product.image || `https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&q=80`"
                :alt="product.name"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
            />
        </Link>
        <div class="p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <Link :href="route('products.show', product.slug)" class="line-clamp-2 text-sm font-semibold text-zinc-950 hover:text-emerald-700">
                        {{ product.name }}
                    </Link>
                    <p class="mt-1 text-xs text-zinc-500">{{ product.category?.name || 'Global catalog' }}</p>
                </div>
                <button class="rounded-md p-2 text-zinc-500 hover:bg-rose-50 hover:text-rose-600" title="Wishlist">
                    <HeartIcon class="h-5 w-5" />
                </button>
            </div>

            <div class="mt-4 flex items-end justify-between gap-3">
                <div>
                    <p class="text-base font-bold">{{ money(product.selling_price) }}</p>
                    <p v-if="product.compare_price" class="text-xs text-zinc-400 line-through">{{ money(product.compare_price) }}</p>
                </div>
                <button class="rounded-md bg-zinc-950 p-2 text-white hover:bg-zinc-800" title="Add to cart">
                    <ShoppingBagIcon class="h-5 w-5" />
                </button>
            </div>
        </div>
    </article>
</template>
