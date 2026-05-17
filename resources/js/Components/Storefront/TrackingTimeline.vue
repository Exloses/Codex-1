<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';

defineProps({
    tracking: {
        type: Object,
        default: () => ({ tracking_events: [], dropship_orders: [] }),
    },
});
</script>

<template>
    <section class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm text-zinc-500">Latest status</p>
                <p class="text-xl font-bold tracking-normal">{{ tracking.latest_tracking_label || tracking.latest_tracking_status || tracking.status }}</p>
            </div>
            <StatusBadge :value="tracking.latest_tracking_status || tracking.status" />
        </div>

        <div v-if="tracking.dropship_orders?.length" class="grid gap-3 sm:grid-cols-2">
            <div v-for="dropship in tracking.dropship_orders" :key="dropship.id" class="rounded-lg border border-zinc-200 bg-zinc-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-semibold">{{ dropship.dropship_number }}</p>
                    <StatusBadge :value="dropship.status" />
                </div>
                <div class="mt-3 space-y-1 text-sm text-zinc-600">
                    <p>Carrier: {{ dropship.carrier || 'Pending' }}</p>
                    <p>Tracking: {{ dropship.tracking_number || 'Pending' }}</p>
                </div>
            </div>
        </div>

        <div v-if="tracking.tracking_events?.length" class="relative space-y-4">
            <div v-for="event in tracking.tracking_events" :key="event.id" class="relative pl-7">
                <span class="absolute left-0 top-1.5 h-3 w-3 rounded-full bg-zinc-950"></span>
                <div class="rounded-lg border border-zinc-200 bg-white p-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <p class="font-semibold">{{ event.title || event.label }}</p>
                            <p class="text-xs uppercase tracking-normal text-zinc-500">{{ event.source }}</p>
                        </div>
                        <StatusBadge :value="event.status" />
                    </div>
                    <p v-if="event.description" class="mt-2 text-sm text-zinc-600">{{ event.description }}</p>
                    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-zinc-500">
                        <span>{{ event.occurred_at_human }}</span>
                        <span v-if="event.location">{{ event.location }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="rounded-lg border border-dashed border-zinc-300 bg-zinc-50 p-5 text-sm text-zinc-600">
            Tracking updates will appear here once the order starts moving.
        </div>
    </section>
</template>
