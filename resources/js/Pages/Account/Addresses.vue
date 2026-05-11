<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({ addresses: { type: Array, default: () => [] } });
const form = useForm({ full_name: '', phone: '', address_line1: '', city: '', postal_code: '', country: 'US', is_default: false });
</script>

<template>
    <StorefrontLayout>
        <Head title="Addresses" />
        <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Addresses</h1>
            <form class="mt-6 grid gap-3 rounded-lg border border-zinc-200 bg-white p-5 sm:grid-cols-2" @submit.prevent="form.post(route('account.addresses.store'), { preserveScroll: true })">
                <input v-model="form.full_name" class="rounded-md border-zinc-300 text-sm" placeholder="Full name" />
                <input v-model="form.phone" class="rounded-md border-zinc-300 text-sm" placeholder="Phone" />
                <input v-model="form.address_line1" class="rounded-md border-zinc-300 text-sm sm:col-span-2" placeholder="Address line" />
                <input v-model="form.city" class="rounded-md border-zinc-300 text-sm" placeholder="City" />
                <input v-model="form.postal_code" class="rounded-md border-zinc-300 text-sm" placeholder="Postal code" />
                <input v-model="form.country" class="rounded-md border-zinc-300 text-sm" maxlength="2" placeholder="Country" />
                <label class="flex items-center gap-2 text-sm"><input v-model="form.is_default" type="checkbox" /> Default</label>
                <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white sm:col-span-2">Add address</button>
            </form>
            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                <div v-for="address in addresses" :key="address.id" class="rounded-lg border border-zinc-200 bg-white p-4 text-sm">
                    <p class="font-semibold">{{ address.full_name }}</p>
                    <p class="mt-1 text-zinc-600">{{ address.address_line1 }}, {{ address.city }}, {{ address.country }}</p>
                </div>
            </div>
            <EmptyState v-if="!addresses.length" class="mt-6" title="No addresses saved" />
        </section>
    </StorefrontLayout>
</template>
