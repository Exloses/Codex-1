<script setup>
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.user.name,
    phone: props.user.phone || '',
    country: props.user.country || 'US',
    currency: props.user.currency || 'USD',
    language: props.user.language || 'en',
});
</script>

<template>
    <StorefrontLayout>
        <Head title="Account" />
        <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Account</h1>
            <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_280px]">
                <form class="rounded-lg border border-zinc-200 bg-white p-5" @submit.prevent="form.put(route('account.profile.update'))">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <input v-model="form.name" class="rounded-md border-zinc-300 text-sm" placeholder="Name" />
                        <input v-model="form.phone" class="rounded-md border-zinc-300 text-sm" placeholder="Phone" />
                        <input v-model="form.country" class="rounded-md border-zinc-300 text-sm" maxlength="2" placeholder="Country" />
                        <input v-model="form.currency" class="rounded-md border-zinc-300 text-sm" maxlength="3" placeholder="Currency" />
                        <input v-model="form.language" class="rounded-md border-zinc-300 text-sm" placeholder="Language" />
                    </div>
                    <button class="mt-4 rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">Save profile</button>
                </form>
                <nav class="grid gap-2 self-start rounded-lg border border-zinc-200 bg-white p-4 text-sm font-semibold">
                    <Link :href="route('account.orders')">Orders</Link>
                    <Link :href="route('account.addresses')">Addresses</Link>
                    <Link :href="route('account.wishlist')">Wishlist</Link>
                    <Link :href="route('account.loyalty')">Loyalty points</Link>
                    <Link :href="route('account.notifications')">Notifications</Link>
                    <Link :href="route('account.support.index')">Support tickets</Link>
                </nav>
            </div>
        </section>
    </StorefrontLayout>
</template>
