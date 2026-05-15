<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    tickets: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
});

const formatDate = (value) => value
    ? new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).format(new Date(value))
    : '';
</script>

<template>
    <StorefrontLayout>
        <Head title="Support tickets" />
        <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-normal text-emerald-700">Account support</p>
                    <h1 class="text-3xl font-bold tracking-normal">Support tickets</h1>
                </div>
                <Link :href="route('account.support.create')" class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">
                    New ticket
                </Link>
            </div>

            <div class="mt-6 space-y-3">
                <Link
                    v-for="ticket in tickets.data"
                    :key="ticket.id"
                    :href="route('support.show', ticket.id)"
                    class="block rounded-lg border border-zinc-200 bg-white p-4 hover:border-emerald-300"
                >
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-zinc-500">{{ ticket.ticket_number }}</p>
                            <h2 class="mt-1 truncate text-lg font-bold tracking-normal">{{ ticket.subject }}</h2>
                            <p class="mt-1 text-sm text-zinc-500">
                                {{ ticket.order?.order_number || 'General support' }} - {{ ticket.replies_count }} replies - {{ formatDate(ticket.updated_at || ticket.created_at) }}
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2">
                            <StatusBadge :value="ticket.status" />
                            <span class="inline-flex rounded-md bg-zinc-100 px-2.5 py-1 text-xs font-semibold capitalize text-zinc-700">{{ ticket.priority }}</span>
                        </div>
                    </div>
                </Link>
            </div>

            <EmptyState
                v-if="!tickets.data?.length"
                class="mt-6"
                title="No support tickets yet"
                message="Open a ticket when you need help with an order, payment, shipping, or account issue."
            />

            <div v-if="tickets.links?.length > 3" class="mt-6 flex flex-wrap gap-2">
                <Link
                    v-for="link in tickets.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    class="rounded-md border border-zinc-300 px-3 py-2 text-sm"
                    :class="{ 'bg-zinc-950 text-white': link.active, 'pointer-events-none opacity-50': !link.url }"
                    v-html="link.label"
                />
            </div>
        </section>
    </StorefrontLayout>
</template>
