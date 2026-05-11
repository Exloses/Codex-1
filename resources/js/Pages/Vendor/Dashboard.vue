<script setup>
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    vendor: { type: Object, required: true },
    stats: { type: Object, default: () => ({}) },
    recentOrders: { type: Array, default: () => [] },
});
</script>

<template>
    <StorefrontLayout>
        <Head title="Vendor dashboard" />
        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div><p class="text-sm text-zinc-500">Vendor</p><h1 class="text-3xl font-bold tracking-normal">{{ vendor.store_name }}</h1></div>
                <Link :href="route('vendor.products.create')" class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">New product</Link>
            </div>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div v-for="(value, key) in stats" :key="key" class="rounded-lg border border-zinc-200 bg-white p-4">
                    <p class="text-xs uppercase tracking-normal text-zinc-500">{{ key.replaceAll('_', ' ') }}</p>
                    <p class="mt-2 text-2xl font-bold">{{ value }}</p>
                </div>
            </div>
        </section>
    </StorefrontLayout>
</template>
