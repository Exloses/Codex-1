<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, router } from '@inertiajs/vue3';

defineProps({ orders: { type: Object, default: () => ({ data: [] }) } });
</script>

<template>
    <StorefrontLayout>
        <Head title="Vendor orders" />
        <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Vendor orders</h1>
            <div class="mt-6 space-y-3">
                <div v-for="order in orders.data" :key="order.id" class="rounded-lg border border-zinc-200 bg-white p-4">
                    <div class="flex items-center justify-between">
                        <div><p class="font-semibold">{{ order.dropship_number }}</p><p class="text-sm text-zinc-500">{{ order.order?.order_number }}</p></div>
                        <StatusBadge :value="order.status" />
                    </div>
                    <div class="mt-3 flex gap-2">
                        <button class="rounded-md border border-zinc-300 px-3 py-2 text-sm" @click="router.put(route('vendor.orders.confirm', order.id))">Confirm</button>
                        <button class="rounded-md border border-zinc-300 px-3 py-2 text-sm" @click="router.put(route('vendor.orders.ship', order.id), { tracking_number: 'MANUAL', carrier: 'Manual' })">Ship</button>
                    </div>
                </div>
            </div>
        </section>
    </StorefrontLayout>
</template>
