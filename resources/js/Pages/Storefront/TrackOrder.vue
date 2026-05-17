<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import TrackingTimeline from '@/Components/Storefront/TrackingTimeline.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { onUnmounted, ref } from 'vue';

const order = ref(null);
const error = ref('');
let pollTimer = null;
const form = useForm({
    order_number: '',
    email: '',
});

const stopPolling = () => {
    if (pollTimer) {
        clearInterval(pollTimer);
        pollTimer = null;
    }
};

const refreshTracking = async () => {
    if (!order.value) {
        return;
    }

    const response = await axios.post(route('track.status'), form.data());
    order.value = response.data.order;
};

const startPolling = () => {
    stopPolling();
    pollTimer = setInterval(refreshTracking, 15000);
};

const submit = async () => {
    error.value = '';
    order.value = null;
    stopPolling();

    try {
        const response = await axios.post(route('track.order'), form.data());
        order.value = response.data.order;
        startPolling();
    } catch (e) {
        error.value = 'No order matched that order number and email.';
    }
};

onUnmounted(stopPolling);
</script>

<template>
    <StorefrontLayout>
        <Head title="Track order" />

        <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Track order</h1>
            <p class="mt-2 text-sm text-zinc-600">Guest buyers can track by order number and email.</p>

            <form class="mt-6 rounded-lg border border-zinc-200 bg-white p-5" @submit.prevent="submit">
                <div class="grid gap-4 sm:grid-cols-2">
                    <input v-model="form.order_number" required class="rounded-md border-zinc-300 text-sm" placeholder="ORD-20260511-ABCDEFGH" />
                    <input v-model="form.email" required type="email" class="rounded-md border-zinc-300 text-sm" placeholder="buyer@example.com" />
                </div>
                <button class="mt-4 rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">Track</button>
                <p v-if="error" class="mt-3 text-sm text-rose-600">{{ error }}</p>
            </form>

            <div v-if="order" class="mt-8 rounded-lg border border-zinc-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-500">Order</p>
                        <p class="text-lg font-bold">{{ order.order_number }}</p>
                    </div>
                    <StatusBadge :value="order.status" />
                </div>

                <TrackingTimeline class="mt-6" :tracking="order" />
            </div>
        </section>
    </StorefrontLayout>
</template>
