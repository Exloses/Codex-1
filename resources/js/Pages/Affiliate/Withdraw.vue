<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ affiliate: { type: Object, required: true }, payouts: { type: Object, default: () => ({ data: [] }) }, availableBalanceUsd: { type: [String, Number], default: 0 } });
const form = useForm({ payout_method_id: props.affiliate.payout_methods?.[0]?.id || props.affiliate.payoutMethods?.[0]?.id || '', amount_usd: 0, payout_type: 'standard' });
</script>

<template>
    <StorefrontLayout>
        <Head title="Affiliate payouts" />
        <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Payouts</h1>
            <div class="mt-6 rounded-lg border border-zinc-200 bg-white p-5">
                <p class="text-sm text-zinc-500">Available balance</p>
                <p class="text-3xl font-bold">${{ availableBalanceUsd }}</p>
                <form class="mt-4 grid gap-3 sm:grid-cols-3" @submit.prevent="form.post(route('affiliate.payouts.store'))">
                    <input v-model="form.payout_method_id" class="rounded-md border-zinc-300 text-sm" placeholder="Method ID" />
                    <input v-model.number="form.amount_usd" type="number" class="rounded-md border-zinc-300 text-sm" />
                    <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">Request payout</button>
                </form>
            </div>
            <div class="mt-6 space-y-3">
                <div v-for="payout in payouts.data" :key="payout.id" class="flex items-center justify-between rounded-lg border border-zinc-200 bg-white p-4">
                    <span>${{ payout.net_amount_usd }}</span>
                    <StatusBadge :value="payout.status" />
                </div>
            </div>
        </section>
    </StorefrontLayout>
</template>
