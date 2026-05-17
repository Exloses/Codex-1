<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

defineProps({ orders: { type: Object, default: () => ({ data: [] }) } });

const forms = reactive({});
const statuses = [
    'processing',
    'shipped',
    'in_transit',
    'out_for_delivery',
    'delivered',
    'failed',
    'returned',
];

const formFor = (order) => {
    if (!forms[order.id]) {
        forms[order.id] = {
            status: order.status === 'pending' ? 'processing' : order.status,
            title: '',
            description: '',
            location: '',
            carrier: order.carrier || '',
            tracking_number: order.tracking_number || '',
        };
    }

    return forms[order.id];
};

const submitTracking = (order) => {
    router.post(route('vendor.orders.tracking.store', order.id), formFor(order), {
        preserveScroll: true,
    });
};
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
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button class="rounded-md border border-zinc-300 px-3 py-2 text-sm" @click="router.put(route('vendor.orders.confirm', order.id))">Confirm</button>
                        <button class="rounded-md border border-zinc-300 px-3 py-2 text-sm" @click="router.put(route('vendor.orders.ship', order.id), { tracking_number: formFor(order).tracking_number || 'Manual update', carrier: formFor(order).carrier || 'Manual' })">Ship</button>
                    </div>

                    <form class="mt-4 grid gap-3 rounded-lg bg-zinc-50 p-4 sm:grid-cols-2" @submit.prevent="submitTracking(order)">
                        <select v-model="formFor(order).status" class="rounded-md border-zinc-300 text-sm">
                            <option v-for="status in statuses" :key="status" :value="status">{{ status.replaceAll('_', ' ') }}</option>
                        </select>
                        <input v-model="formFor(order).location" class="rounded-md border-zinc-300 text-sm" placeholder="Location" />
                        <input v-model="formFor(order).carrier" class="rounded-md border-zinc-300 text-sm" placeholder="Carrier" />
                        <input v-model="formFor(order).tracking_number" class="rounded-md border-zinc-300 text-sm" placeholder="Tracking number" />
                        <input v-model="formFor(order).title" class="rounded-md border-zinc-300 text-sm sm:col-span-2" placeholder="Optional title" />
                        <textarea v-model="formFor(order).description" class="rounded-md border-zinc-300 text-sm sm:col-span-2" rows="2" placeholder="Optional update note"></textarea>
                        <button class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white sm:col-span-2">Add tracking update</button>
                    </form>

                    <div v-if="order.tracking_events?.length" class="mt-4 space-y-2 text-sm text-zinc-600">
                        <div v-for="event in order.tracking_events" :key="event.id" class="flex items-center justify-between rounded-md bg-white px-3 py-2">
                            <span>{{ event.title }}</span>
                            <StatusBadge :value="event.status" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </StorefrontLayout>
</template>
