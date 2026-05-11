<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ order: { type: Object, required: true } });
const money = (value) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(value || 0));
</script>

<template>
    <StorefrontLayout>
        <Head :title="order.order_number" />
        <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div><p class="text-sm text-zinc-500">Order</p><h1 class="text-3xl font-bold tracking-normal">{{ order.order_number }}</h1></div>
                <StatusBadge :value="order.status" />
            </div>
            <div class="mt-6 rounded-lg border border-zinc-200 bg-white p-5">
                <div v-for="item in order.items || []" :key="item.id" class="flex justify-between border-b border-zinc-100 py-3 text-sm last:border-b-0">
                    <span>{{ item.product?.name }} x {{ item.quantity }}</span>
                    <span>{{ money(item.subtotal_usd) }}</span>
                </div>
                <div class="mt-4 flex justify-between font-bold"><span>Total</span><span>{{ money(order.total_usd) }}</span></div>
            </div>
            <Link :href="route('account.orders.invoice', order.id)" class="mt-4 inline-flex rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold">Invoice</Link>
        </section>
    </StorefrontLayout>
</template>
