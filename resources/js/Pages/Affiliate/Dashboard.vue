<script setup>
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({ affiliate: { type: Object, default: null }, recentCommissions: { type: Array, default: () => [] }, recentPayouts: { type: Array, default: () => [] } });
const form = useForm({ url: '' });
</script>

<template>
    <StorefrontLayout>
        <Head title="Affiliate dashboard" />
        <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-normal">Affiliate dashboard</h1>
                <Link :href="route('affiliate.payouts.index')" class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold">Withdraw</Link>
            </div>
            <div v-if="affiliate" class="mt-6 grid gap-4 sm:grid-cols-4">
                <div class="rounded-lg border border-zinc-200 bg-white p-4"><p class="text-xs text-zinc-500">Code</p><p class="mt-2 font-bold">{{ affiliate.referral_code }}</p></div>
                <div class="rounded-lg border border-zinc-200 bg-white p-4"><p class="text-xs text-zinc-500">Clicks</p><p class="mt-2 font-bold">{{ affiliate.clicks_count || affiliate.total_clicks }}</p></div>
                <div class="rounded-lg border border-zinc-200 bg-white p-4"><p class="text-xs text-zinc-500">Sales</p><p class="mt-2 font-bold">{{ affiliate.total_sales }}</p></div>
                <div class="rounded-lg border border-zinc-200 bg-white p-4"><p class="text-xs text-zinc-500">Earned</p><p class="mt-2 font-bold">${{ affiliate.total_earned_usd }}</p></div>
            </div>
            <form class="mt-6 flex gap-3 rounded-lg border border-zinc-200 bg-white p-4" @submit.prevent="form.post(route('affiliate.generate-link'))">
                <input v-model="form.url" class="min-w-0 flex-1 rounded-md border-zinc-300 text-sm" placeholder="Product or campaign URL" />
                <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">Generate link</button>
            </form>
        </section>
    </StorefrontLayout>
</template>
