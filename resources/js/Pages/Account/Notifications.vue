<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import axios from 'axios';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    notifications: { type: Object, default: () => ({ data: [] }) },
    unreadCount: { type: Number, default: 0 },
});

const notificationItems = ref([...(props.notifications.data || [])]);
const currentUnreadCount = ref(Number(props.unreadCount || 0));

const safeActionUrl = (url) => {
    if (typeof url !== 'string' || !url.trim()) return null;

    const value = url.trim();

    if (value.startsWith('/') && !value.startsWith('//')) {
        return value;
    }

    return null;
};

const markAllRead = async () => {
    try {
        const response = await axios.post(route('account.notifications.read-all'));
        currentUnreadCount.value = Number(response.data.unread_count || 0);
        notificationItems.value = notificationItems.value.map((notification) => ({
            ...notification,
            read_at: notification.read_at || new Date().toISOString(),
        }));
    } catch {
        // Keep the page stable if a transient request fails.
    }
};

const markAsRead = async (notification) => {
    try {
        const response = await axios.post(route('account.notifications.read', notification.id));
        currentUnreadCount.value = Number(response.data.unread_count || 0);
        notificationItems.value = notificationItems.value.map((item) => (
            item.id === notification.id ? response.data.notification : item
        ));
    } catch {
        // Keep the page stable if a transient request fails.
    }
};

const openNotification = async (notification) => {
    await markAsRead(notification);
    router.visit(safeActionUrl(notification.action_url) || route('account.notifications'));
};
</script>

<template>
    <StorefrontLayout>
        <Head title="Notifications" />
        <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-normal">Notifications</h1>
                    <p class="mt-1 text-sm text-zinc-600">{{ currentUnreadCount }} unread notification{{ currentUnreadCount === 1 ? '' : 's' }}</p>
                </div>
                <button
                    class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-700 hover:bg-white disabled:text-zinc-400"
                    :disabled="currentUnreadCount === 0"
                    @click="markAllRead"
                >
                    Mark all read
                </button>
            </div>

            <div class="mt-6 space-y-3">
                <div
                    v-for="notification in notificationItems"
                    :key="notification.id"
                    class="rounded-lg border p-4 text-sm"
                    :class="notification.read_at ? 'border-zinc-200 bg-white' : 'border-emerald-200 bg-emerald-50/70'"
                >
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span
                                    class="h-2 w-2 shrink-0 rounded-full"
                                    :class="notification.read_at ? 'bg-zinc-300' : 'bg-rose-600'"
                                />
                                <h2 class="truncate text-base font-semibold text-zinc-950">{{ notification.title }}</h2>
                            </div>
                            <p class="mt-2 leading-6 text-zinc-700">{{ notification.message }}</p>
                            <p class="mt-2 text-xs text-zinc-500">{{ notification.created_at_human }}</p>
                        </div>

                        <div class="flex shrink-0 flex-wrap gap-2">
                            <button
                                v-if="!notification.read_at"
                                class="rounded-md border border-zinc-300 px-3 py-2 text-xs font-semibold text-zinc-700 hover:bg-white"
                                @click="markAsRead(notification)"
                            >
                                Mark read
                            </button>
                            <button
                                class="rounded-md bg-zinc-950 px-3 py-2 text-xs font-semibold text-white hover:bg-zinc-800"
                                @click="openNotification(notification)"
                            >
                                Open
                            </button>
                        </div>
                    </div>
                </div>

                <EmptyState
                    v-if="!notificationItems.length"
                    title="No notifications"
                    message="Order updates, loyalty activity, support replies, and account messages will appear here."
                />
            </div>

            <div v-if="notifications.links?.length > 3" class="mt-8 flex flex-wrap justify-center gap-2">
                <Link
                    v-for="link in notifications.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    class="rounded-md border px-3 py-2 text-sm"
                    :class="[
                        link.active ? 'border-zinc-950 bg-zinc-950 text-white' : 'border-zinc-300 bg-white text-zinc-700',
                        !link.url ? 'pointer-events-none opacity-50' : 'hover:bg-zinc-50',
                    ]"
                    preserve-scroll
                >
                    {{ link.label.replace('&laquo;', 'Previous').replace('&raquo;', 'Next') }}
                </Link>
            </div>
        </section>
    </StorefrontLayout>
</template>
