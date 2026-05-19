<script setup>
import { BellIcon } from '@heroicons/vue/24/outline';
import axios from 'axios';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const page = usePage();
const isOpen = ref(false);
const isLoading = ref(false);
const notifications = ref([]);
const unreadCount = ref(Number(page.props.unread_notifications_count || 0));
const pollTimer = ref(null);
const root = ref(null);

const user = computed(() => page.props.auth?.user);
const displayCount = computed(() => (unreadCount.value > 99 ? '99+' : unreadCount.value));

const safeActionUrl = (url) => {
    if (typeof url !== 'string' || !url.trim()) return null;

    const value = url.trim();

    if (value.startsWith('/') && !value.startsWith('//')) {
        return value;
    }

    return null;
};

const fetchFeed = async () => {
    if (!user.value) return;

    try {
        isLoading.value = true;
        const response = await axios.get(route('account.notifications.feed'), {
            headers: { Accept: 'application/json' },
        });

        notifications.value = response.data.notifications || [];
        unreadCount.value = Number(response.data.unread_count || 0);
    } catch {
        // Polling should never interrupt shopping or navigation.
    } finally {
        isLoading.value = false;
    }
};

const markAsRead = async (notification) => {
    if (!notification?.id) return;

    try {
        const response = await axios.post(route('account.notifications.read', notification.id));
        unreadCount.value = Number(response.data.unread_count || 0);
        notifications.value = notifications.value.map((item) => (
            item.id === notification.id ? response.data.notification : item
        ));
    } catch {
        // Keep the dropdown usable even if the read update fails.
    }
};

const openNotification = async (notification) => {
    await markAsRead(notification);
    router.visit(safeActionUrl(notification?.action_url) || route('account.notifications'));
};

const markAllRead = async () => {
    try {
        const response = await axios.post(route('account.notifications.read-all'));
        unreadCount.value = Number(response.data.unread_count || 0);
        notifications.value = notifications.value.map((notification) => ({
            ...notification,
            read_at: notification.read_at || new Date().toISOString(),
        }));
    } catch {
        // Ignore transient request failures in the menu.
    }
};

const toggle = async () => {
    isOpen.value = !isOpen.value;

    if (isOpen.value && notifications.value.length === 0) {
        await fetchFeed();
    }
};

const closeOnOutsideClick = (event) => {
    if (!root.value || root.value.contains(event.target)) return;

    isOpen.value = false;
};

onMounted(() => {
    if (!user.value) return;

    fetchFeed();
    pollTimer.value = window.setInterval(fetchFeed, 30000);
    document.addEventListener('click', closeOnOutsideClick);
});

onUnmounted(() => {
    if (pollTimer.value) {
        window.clearInterval(pollTimer.value);
    }

    document.removeEventListener('click', closeOnOutsideClick);
});
</script>

<template>
    <div ref="root" class="relative">
        <button
            type="button"
            class="relative rounded-md p-2 text-zinc-600 hover:bg-zinc-100"
            aria-label="Notifications"
            @click="toggle"
        >
            <BellIcon class="h-5 w-5" />
            <span
                v-if="unreadCount > 0"
                class="absolute -right-1 -top-1 min-w-5 rounded-full bg-rose-600 px-1.5 py-0.5 text-center text-[10px] font-bold leading-none text-white"
            >
                {{ displayCount }}
            </span>
        </button>

        <div
            v-if="isOpen"
            class="absolute right-0 z-50 mt-2 w-[min(22rem,calc(100vw-2rem))] overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-lg"
        >
            <div class="flex items-center justify-between border-b border-zinc-200 px-4 py-3">
                <div>
                    <p class="text-sm font-semibold text-zinc-950">Notifications</p>
                    <p class="text-xs text-zinc-500">{{ unreadCount }} unread</p>
                </div>
                <button
                    type="button"
                    class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 disabled:text-zinc-400"
                    :disabled="unreadCount === 0"
                    @click="markAllRead"
                >
                    Mark all read
                </button>
            </div>

            <div class="max-h-96 overflow-y-auto">
                <button
                    v-for="notification in notifications"
                    :key="notification.id"
                    type="button"
                    class="block w-full border-b border-zinc-100 px-4 py-3 text-left hover:bg-zinc-50"
                    :class="notification.read_at ? 'bg-white' : 'bg-emerald-50/60'"
                    @click="openNotification(notification)"
                >
                    <div class="flex gap-3">
                        <span
                            class="mt-1 h-2 w-2 shrink-0 rounded-full"
                            :class="notification.read_at ? 'bg-zinc-300' : 'bg-rose-600'"
                        />
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold text-zinc-950">{{ notification.title }}</span>
                            <span class="mt-1 line-clamp-2 block text-xs leading-5 text-zinc-600">{{ notification.message }}</span>
                            <span class="mt-1 block text-xs text-zinc-400">{{ notification.created_at_human }}</span>
                        </span>
                    </div>
                </button>

                <div v-if="!notifications.length" class="px-4 py-8 text-center text-sm text-zinc-500">
                    {{ isLoading ? 'Loading notifications...' : 'No notifications yet.' }}
                </div>
            </div>

            <Link
                :href="route('account.notifications')"
                class="block border-t border-zinc-200 px-4 py-3 text-center text-sm font-semibold text-zinc-700 hover:bg-zinc-50"
            >
                View all notifications
            </Link>
        </div>
    </div>
</template>
