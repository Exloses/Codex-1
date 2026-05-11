<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({ affiliate: { type: Object, required: true }, commissions: { type: Object, default: () => ({ data: [] }) } });
</script>

<template>
    <StorefrontLayout>
        <Head title="Commissions" />
        <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Commissions</h1>
            <div class="mt-6 space-y-3">
                <div v-for="commission in commissions.data" :key="commission.id" class="flex items-center justify-between rounded-lg border border-zinc-200 bg-white p-4">
                    <div><p class="font-semibold">{{ commission.order?.order_number }}</p><p class="text-sm text-zinc-500">${{ commission.commission_usd }}</p></div>
                    <StatusBadge :value="commission.status" />
                </div>
            </div>
        </section>
    </StorefrontLayout>
</template>
