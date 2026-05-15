<script setup>
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    order: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <StorefrontLayout>
        <Head title="Order placed" />
        <section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-zinc-200 bg-white p-8 text-center">
                <p class="text-sm font-semibold uppercase tracking-normal text-emerald-700">Order placed</p>
                <h1 class="mt-2 text-3xl font-bold tracking-normal">{{ order.order_number }}</h1>
                <p class="mt-3 text-sm text-zinc-600">Your order is ready for payment processing and vendor fulfillment.</p>
                <p v-if="order.guest_email" class="mt-2 text-sm text-zinc-600">
                    Track this guest order with {{ order.order_number }} and {{ order.guest_email }}.
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    <Link :href="route('track.index')" class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold">Track order</Link>
                    <Link :href="route('products.index')" class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">Continue shopping</Link>
                </div>
            </div>
        </section>
    </StorefrontLayout>
</template>
