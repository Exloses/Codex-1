<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ orders: { type: Object, default: () => ({ data: [] }) } });
const money = (value) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(value || 0));
</script>

<template>
    <StorefrontLayout>
        <Head title="Orders" />
        <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Orders</h1>
            <div class="mt-6 space-y-3">
                <Link v-for="order in orders.data" :key="order.id" :href="route('account.orders.show', order.id)" class="flex items-center justify-between rounded-lg border border-zinc-200 bg-white p-4">
                    <div><p class="font-semibold">{{ order.order_number }}</p><p class="text-sm text-zinc-500">{{ money(order.total_usd) }}</p></div>
                    <StatusBadge :value="order.status" />
                </Link>
                <EmptyState v-if="!orders.data?.length" title="No orders yet" />
            </div>
        </section>
    </StorefrontLayout>
</template>
