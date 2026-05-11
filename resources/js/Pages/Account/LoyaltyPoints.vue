<script setup>
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({ loyaltyPoint: { type: Object, default: null }, transactions: { type: Object, default: () => ({ data: [] }) } });
</script>

<template>
    <StorefrontLayout>
        <Head title="Loyalty points" />
        <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Loyalty points</h1>
            <div class="mt-6 rounded-lg border border-zinc-200 bg-white p-6">
                <p class="text-sm text-zinc-500">Balance</p>
                <p class="mt-1 text-4xl font-bold">{{ loyaltyPoint?.balance || 0 }}</p>
            </div>
        </section>
    </StorefrontLayout>
</template>
