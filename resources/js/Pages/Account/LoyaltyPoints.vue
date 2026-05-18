<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    summary: {
        type: Object,
        default: () => ({
            balance: 0,
            minimum_points: 500,
            points_per_usd: 10,
            points_per_discount_usd: 100,
            register_bonus_points: 100,
            review_bonus_points: 50,
        }),
    },
    transactions: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
});

const balance = computed(() => Number(props.summary?.balance || 0));
const transactionRows = computed(() => props.transactions?.data || []);
const typeLabel = (type) => ({
    earn: 'Earned',
    bonus: 'Bonus',
    redeem: 'Redeemed',
    expired: 'Expired',
}[type] || type);
const typeClass = (type) => ({
    earn: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    bonus: 'bg-sky-50 text-sky-700 ring-sky-200',
    redeem: 'bg-amber-50 text-amber-700 ring-amber-200',
    expired: 'bg-zinc-100 text-zinc-700 ring-zinc-200',
}[type] || 'bg-zinc-100 text-zinc-700 ring-zinc-200');
const pointsText = (points) => `${points > 0 ? '+' : ''}${Number(points || 0).toLocaleString()}`;
</script>

<template>
    <StorefrontLayout>
        <Head title="Loyalty points" />

        <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-normal">Loyalty points</h1>
                    <p class="mt-2 text-sm text-zinc-600">Earn points on paid orders, account milestones, and helpful product reviews.</p>
                </div>
                <Link :href="route('checkout.index')" class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">
                    Redeem at checkout
                </Link>
            </div>

            <div class="mt-6 grid gap-4 lg:grid-cols-[1fr_2fr]">
                <div class="rounded-lg border border-zinc-200 bg-white p-6">
                    <p class="text-sm text-zinc-500">Available balance</p>
                    <p class="mt-2 text-5xl font-bold tracking-normal">{{ balance.toLocaleString() }}</p>
                    <p class="mt-2 text-sm text-zinc-600">points</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-lg border border-zinc-200 bg-white p-5">
                        <p class="text-sm font-semibold">Earn</p>
                        <p class="mt-2 text-sm text-zinc-600">{{ summary.points_per_usd }} points per $1 spent after payment.</p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-white p-5">
                        <p class="text-sm font-semibold">Redeem</p>
                        <p class="mt-2 text-sm text-zinc-600">{{ summary.points_per_discount_usd }} points = $1 discount, minimum {{ summary.minimum_points }} points.</p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-white p-5">
                        <p class="text-sm font-semibold">Bonuses</p>
                        <p class="mt-2 text-sm text-zinc-600">{{ summary.register_bonus_points }} points on registration and {{ summary.review_bonus_points }} points per review.</p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-white p-5">
                        <p class="text-sm font-semibold">Expiry</p>
                        <p class="mt-2 text-sm text-zinc-600">Earned and bonus points expire 1 year after they are granted.</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 overflow-hidden rounded-lg border border-zinc-200 bg-white">
                <div class="border-b border-zinc-200 px-5 py-4">
                    <h2 class="text-lg font-bold">Transaction history</h2>
                </div>

                <div v-if="transactionRows.length" class="divide-y divide-zinc-100">
                    <article v-for="transaction in transactionRows" :key="transaction.id" class="grid gap-3 px-5 py-4 md:grid-cols-[1fr_auto] md:items-center">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold ring-1" :class="typeClass(transaction.type)">
                                    {{ typeLabel(transaction.type) }}
                                </span>
                                <p class="text-sm font-semibold">{{ transaction.description }}</p>
                            </div>
                            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-zinc-500">
                                <span>{{ transaction.created_at }}</span>
                                <span v-if="transaction.expires_at">Expires {{ transaction.expires_at }}</span>
                                <Link v-if="transaction.order" :href="route('account.orders.show', transaction.order.id)" class="font-semibold text-emerald-700">
                                    {{ transaction.order.order_number }}
                                </Link>
                            </div>
                        </div>
                        <p class="text-right text-lg font-bold" :class="transaction.points >= 0 ? 'text-emerald-700' : 'text-zinc-700'">
                            {{ pointsText(transaction.points) }}
                        </p>
                    </article>
                </div>

                <div v-else class="p-6">
                    <EmptyState title="No points yet" message="Place a paid order or write a review to start earning loyalty rewards." />
                </div>
            </div>
        </section>
    </StorefrontLayout>
</template>
