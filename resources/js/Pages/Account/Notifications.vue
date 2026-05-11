<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, router } from '@inertiajs/vue3';

defineProps({ notifications: { type: Object, default: () => ({ data: [] }) } });
</script>

<template>
    <StorefrontLayout>
        <Head title="Notifications" />
        <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-normal">Notifications</h1>
                <button class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold" @click="router.post(route('account.notifications.read-all'))">Mark all read</button>
            </div>
            <div class="mt-6 space-y-3">
                <div v-for="notification in notifications.data || []" :key="notification.id" class="rounded-lg border border-zinc-200 bg-white p-4 text-sm">
                    {{ notification.data?.message || notification.type }}
                </div>
                <EmptyState v-if="!(notifications.data || []).length" title="No notifications" />
            </div>
        </section>
    </StorefrontLayout>
</template>
