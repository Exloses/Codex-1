<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import ProductCard from '@/Components/ProductCard.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({ items: { type: Array, default: () => [] } });
</script>

<template>
    <StorefrontLayout>
        <Head title="Wishlist" />
        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Wishlist</h1>
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard v-for="item in items" :key="item.id" :product="item.product || item" />
            </div>
            <EmptyState v-if="!items.length" class="mt-6" title="No wishlist items" />
        </section>
    </StorefrontLayout>
</template>
