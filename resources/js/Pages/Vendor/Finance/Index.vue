<script setup>
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({ vendor: { type: Object, required: true }, dropshipOrders: { type: Object, default: () => ({ data: [] }) }, withdrawals: { type: [Object, Array], default: () => ({ data: [] }) } });
const form = useForm({ amount_idr: 0, notes: '' });
</script>

<template>
    <StorefrontLayout>
        <Head title="Vendor finance" />
        <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Finance</h1>
            <div class="mt-6 rounded-lg border border-zinc-200 bg-white p-5">
                <p class="text-sm text-zinc-500">Balance</p>
                <p class="text-3xl font-bold">IDR {{ vendor.balance_idr }}</p>
                <form class="mt-4 flex gap-3" @submit.prevent="form.post(route('vendor.finance.withdraw'))">
                    <input v-model.number="form.amount_idr" type="number" class="rounded-md border-zinc-300 text-sm" />
                    <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">Request withdrawal</button>
                </form>
            </div>
        </section>
    </StorefrontLayout>
</template>
